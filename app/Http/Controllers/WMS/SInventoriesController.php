<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Laracasts\Flash\Flash;

use App\SUtils\SProcess;
use App\SUtils\SUtil;
use App\SUtils\SGuiUtils;
use App\SUtils\SMenu;

use App\SCore\SInventoryCore;
use App\WMS\SWarehouse;

class SInventoriesController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.INVENTORY_OPERATION'), \Config::get('scsys.MODULES.WMS'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function emptyWarehouseIndex(Request $request)
    {
        $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, true);

        $title = trans('wms.EMPTY_WAREHOUSE');

        return view('wms.inventories.emptywhs')
                    ->with('lWarehouses', $lWarehouses)
                    ->with('title', $title);
    }

    /**
     * index
     *
     * @param  Request $request
     */
    public function initialIndex(Request $request)
    {
       $title = trans('wms.GENERATE_INITIAL_INVENTORY');

       return view('wms.inventories.initialinventories')
                  ->with('title', $title);
    }

    /**
     * @param  Request $request
     *
     * @return redirect()->route('wms.home');
     */
    public function generateInitialInventory(Request $request)
    {
        $oInvStk = new SInventoryCore();
        $aResult = $oInvStk->generateInitialInventory($request->year, $request);

        if (is_array($aResult)) {
          if(sizeof($aResult) > 0) {
              return redirect()
                        ->back()
                        ->withErrors($aResult)
                        ->withInput();
          }
        }

        Flash::success(trans('messages.GENERATED_INVENTORY'))->important();

        return redirect()->route('wms.home');
    }

    /**
     * @param  Request $request
     *
     * @return JSON(lStock)
     */
    public function getStock(Request $request)
    {
       $lStock = array();
       $oInvStk = new SInventoryCore();

       $lStock = $oInvStk->getStock($request->iWhs, 0, $request->dt_date);

       return json_encode($lStock);
    }

    public function movementsIndex(Request $request)
    {
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
        $iFilterWhs = $request->warehouse == null ? session('whs')->id_whs : $request->warehouse;

        $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);

        $sSelect = 'ei.code AS item_code,
                    ei.name AS item,
                    ei.is_lot,
                    wl.id_lot,
                    wl.lot,
                    wl.dt_expiry,
                    eu.code AS unit,
                    eb.code AS branch_code,
                    ww.code AS whs_code,
                    wm.dt_date AS mvt_whs_class_id,
                    wm.dt_date AS mov_date,
                    wm.folio AS mov_folio,
                    wmt.code AS mov_code,
                    wmt.name AS movement,
                    wmr.quantity AS row_quantity,
                    wmrl.quantity AS lot_quantity,
                    ed_ord.num AS num_order,
                    ed_ord.service_num AS ser_num_order,
                    ed_ord.dt_date AS dt_order,
                    ed_inv.num AS num_invoice,
                    ed_inv.service_num AS ser_num_invoice,
                    ed_inv.dt_date AS dt_invoice,
                    ed_cn.num AS num_cn,
                    ed_cn.service_num AS ser_num_cn,
                    ed_cn.dt_date AS dt_cn,
                    wm.doc_order_id,
                    wm.doc_invoice_id,
                    wm.doc_credit_note_id
                    ';

        $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, true);
        $lWarehouses['0'] = 'TODOS';

        $movs = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('wms_mvts as wm')
                     ->join('wms_mvt_rows as wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                     ->join('wms_mvt_row_lots as wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                     ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                     ->join('erpu_items as ei', 'wmr.item_id', '=', 'ei.id_item')
                     ->join('erpu_units as eu', 'wmr.unit_id', '=', 'eu.id_unit')
                     ->join('wms_pallets as wp', 'wmr.pallet_id', '=', 'wp.id_pallet')
                     ->join('wms_lots as wl', 'wmrl.lot_id', '=', 'wl.id_lot')
                     ->join('wmsu_whs_locations as wwl', 'wmr.location_id', '=', 'wwl.id_whs_location')
                     ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                     ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                     ->join('erpu_documents as ed_ord', 'wm.doc_order_id', '=', 'ed_ord.id_document')
                     ->join('erpu_documents as ed_inv', 'wm.doc_invoice_id', '=', 'ed_inv.id_document')
                     ->join('erpu_documents as ed_cn', 'wm.doc_credit_note_id', '=', 'ed_cn.id_document');

         if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
             $movs = $movs->where('wm.whs_id', $iFilterWhs);
         }

         $movs = $movs->whereBetween('wm.dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()])
                       ->select(\DB::raw($sSelect))
                       ->groupBy('id_mvt', 'id_mvt_row', 'id_mvt_row_lot')
                       ->get();

         return view('wms.movs.indexdetail')
                     ->with('lWarehouses', $lWarehouses)
                     ->with('iFilterWhs', $iFilterWhs)
                     ->with('iFilter', $this->iFilter)
                     ->with('sFilterDate', $sFilterDate)
                     ->with('lRows', $movs)
                     ->with('title', trans('wms.WHS_MOVS_DETAIL'));
    }

  }
