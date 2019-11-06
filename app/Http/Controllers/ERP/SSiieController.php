<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\SUtils\SGuiUtils;
use App\SUtils\SProcess;
use App\MMS\SStatusOrder;

class SSiieController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;
    private $lOrderStatus;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ERP'), \Config::get('scsys.MODULES.ERP'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');

        $this->lOrderStatus = SStatusOrder::where('is_deleted', false)
                                ->orderBy('id_status', 'asc')
                                ->lists('name', 'id_status');
        $this->lOrderStatus['0'] = 'TODAS';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home($isImported = 0)
    {
        return view('siie.index');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param integer $iFrom
     * @return view 'siie.pos.vpos'
     */
    public function posIndex(Request $request, $iFrom = 0)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
        $iOrderStatus = $request->po_status == null ? \Config::get('scmms.PO_STATUS.ST_ALL') : $request->po_status;

        $lProductionOrders = \DB::connection(session('db_configuration')->getConnCompany())
                                    ->table('mms_production_orders AS mpo')
                                    ->leftjoin('mms_production_orders AS mpof', 'mpo.father_order_id', '=', 'mpof.id_order')
                                    ->join('erpu_items AS ei', 'mpo.item_id', '=', 'ei.id_item')
                                    ->join('erpu_items AS eif', 'mpof.item_id', '=', 'eif.id_item')
                                    ->join('erpu_units AS eu', 'mpo.unit_id', '=', 'eu.id_unit')
                                    ->join('erpu_units AS euf', 'mpof.unit_id', '=', 'euf.id_unit')
                                    ->join('wms_lots AS wl', 'mpo.lot_id', '=', 'wl.id_lot')
                                    ->select('mpo.id_order',
                                            'mpo.date AS dateh_',
                                            'mpof.date AS datef_',
                                            'mpo.folio AS folio_hijo',
                                            'mpof.folio AS folio_padre',
                                            'ei.name AS item_',
                                            'ei.code AS item_code_',
                                            'eif.name AS itemf_',
                                            'eif.code AS item_code_f_',
                                            'eu.code AS unit_',
                                            'euf.code AS unitf_',
                                            'mpo.father_order_id',
                                            'mpo.lot_id',
                                            'mpof.lot_id AS lot_id_f',
                                            'wl.lot');

        $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);
        $lProductionOrders = $lProductionOrders->whereBetween('mpo.date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);

        if (\Config::get('scmms.PO_STATUS.ST_ALL') != $iOrderStatus) {
            $lProductionOrders = $lProductionOrders->where('status_id', $iOrderStatus);
        }

        switch ($this->iFilter) {
            case \Config::get('scsys.FILTER.ACTIVES'):
                $lProductionOrders = $lProductionOrders->where('mpo.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
              break;
   
            case \Config::get('scsys.FILTER.DELETED'):
                $lProductionOrders = $lProductionOrders->where('mpo.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
              break;
   
            default:
        }

        if ($iFrom == \Config::get('scsiie.OP_FROM.PRODUCTION') || $iFrom == \Config::get('scsiie.OP_FROM.QUALITY')) {
            $lProductionOrders = $lProductionOrders->where('mpo.father_order_id', '>', '1');
        }

        $lProductionOrders = $lProductionOrders->get();

        return view('siie.pos.vpos')
                ->with('actualUserPermission', $this->oCurrentUserPermission)
                ->with('lOrderStatus', $this->lOrderStatus)
                ->with('iOrderStatus', $iOrderStatus)
                ->with('lProductionOrders', $lProductionOrders)
                ->with('iFrom', $iFrom)
                ->with('sFilterDate', $sFilterDate)
                ->with('iFilter', $this->iFilter);
    }
}
