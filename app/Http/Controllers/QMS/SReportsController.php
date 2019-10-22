<?php

namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SUtils\SGuiUtils;
use Carbon\Carbon;
use App\SUtils\SProcess;
use App\QMS\SQDocument;
use App\QMS\SQMongoDoc;
use App\ERP\SItem;
use App\QMS\data\SReportData;

class SReportsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_REPORTS'), \Config::get('scsys.MODULES.QMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lItems = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('erpu_items as ei')
                            ->join('erpu_item_genders as eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                            ->selectRaw('CONCAT(ei.code,"-",ei.name) as _item, ei.id_item')
                            ->where('ei.is_deleted', false)
                            ->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.FINISHED_PRODUCT'))
                            ->lists('_item', 'id_item');

        return view('qms.reports.index')
                    ->with('lItems', $lItems);
    }


    /**
     * Undocumented function
     *
     * @param string $sDate
     * @return \Illuminate\Http\Response
     */
    public function lotsPh(Request $request)
    {
        // inicio de consulta a MongoDB para resultados
        $lPhLotsQ = SQMongoDoc::select(['lot_date',
                                        'lot',
                                        'dt_expiry',
                                        'lot_id',
                                        'results.analysis_id',
                                        'results.result',
                                        'usr_creation',
                                        'usr_upd',
                                        'created_at', 
                                        'updated_at']);

        // Si los resultados vienen de la pantalla de generación del reporte, 
        // las fechas se tratan por separado, si no, son una cadena compuesta por ambas
        if (strlen($request->start_date) > 0) {
            $oStartDate = Carbon::parse($request->start_date);
            $oEndDate = Carbon::parse($request->end_date);

            $oStDate = clone $oStartDate;
            $oEDate = clone $oEndDate;
            $sFilterDate = $oStDate->format('d/m/Y').' - '.$oEDate->format('d/m/Y');

            $aDates[0] = $oStartDate;
            $aDates[1] = $oEndDate;
        }
        else {
            $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
            $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);
        }

        $oItem = SItem::find($request->item_id);
        
        // En la consulta original se filtran las fechas y el ítem
        $lPhLotsQ = $lPhLotsQ->where('results.analysis_id', 12)
                                ->whereBetween('lot_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()])
                                ->where('item_id', $oItem->id_item)
                                ->where('unit_id', $oItem->unit_id)
                                // ->project(['results' => ['$elemmatch' => ['analysis_id',12]]])
                                ->get();

        // Se recorre el arreglo resultante de la consulta para filtrar solamente los resultados
        // que correspondan al análisis de PH
        $lPhLots = array();
        foreach ($lPhLotsQ as $phLot) {
            foreach ($phLot->results as $elem) {
                if ($elem['analysis_id'] == 12) {
                    $row = new SReportData();

                    $row->lot = $phLot->lot;
                    $row->dt_expiry = $phLot->dt_expiry;
                    $row->lot_date = $phLot->lot_date;
                    $row->created_by = $phLot->usr_creation;
                    $row->updated_by = $phLot->usr_upd;
                    $row->created_at = $phLot->created_at;
                    $row->updated_at = $phLot->updated_at;
                    $row->result = $elem['result'];
                    $row->max_ph = $request->max_ph;
                    $row->item = $oItem->code.'-'.$oItem->name;
                    $row->analysis_id = $elem['analysis_id'];

                    $lPhLots[] = $row;
                }
            }
        }

        return view('qms.reports.ph')
                    ->with('sFilterDate', $sFilterDate)
                    ->with('max_ph', $request->max_ph)
                    ->with('item_id', $oItem->id_item)
                    ->with('iFilter', $this->iFilter)
                    ->with('lPhLots', $lPhLots);
    }
}
