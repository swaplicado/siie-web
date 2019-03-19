<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;

use App\SUtils\SValidation;
use App\SUtils\SProcess;
use App\SUtils\SMenu;
use App\SUtils\SUtil;

use App\QMS\SAnalysis;
use App\QMS\SAnaConfig;
use App\QMS\SResult;
use App\QMS\SDataResponse;
use App\WMS\SWmsLot;

class SResultsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.MMS_FORMULAS'), \Config::get('scsys.MODULES.QMS'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

        
        $sSelect = "
                    wl.id_lot,
                    wl.lot,
                    wl.dt_expiry,
                    SUM(IF(wsr.segregation_mvt_type_id = '".\Config::get('scqms.SEGREGATION.INCREMENT')."', wsr.quantity, 0)) AS _inc,
                    SUM(IF(wsr.segregation_mvt_type_id = '".\Config::get('scqms.SEGREGATION.DECREMENT')."', wsr.quantity, 0)) AS _dec,
                    (SUM(IF(wsr.segregation_mvt_type_id = '".\Config::get('scqms.SEGREGATION.INCREMENT')."', wsr.quantity, 0)) -
                    SUM(IF(wsr.segregation_mvt_type_id = '".\Config::get('scqms.SEGREGATION.DECREMENT')."', wsr.quantity, 0))) AS _seg,
                    wsr.segregation_event_id,
                    qse.name as _evtname,
                    CONCAT(ei.code, '-', ei.name) as _item,
                    CONCAT(eu.code) as _unit
                    ";

        $lSegregatedLots = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('wms_segregations as ws')
                            ->join('wms_segregation_rows as wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                            ->join('qmss_segregation_events as qse', 'qse.id_segregation_event', '=', 'wsr.segregation_event_id')
                            ->join('wms_lots as wl', 'wsr.lot_id', '=', 'wl.id_lot')
                            ->join('erpu_items as ei', 'wsr.item_id', '=', 'ei.id_item')
                            ->join('erpu_units as eu', 'wsr.unit_id', '=', 'eu.id_unit');
        
        $lSegregatedLots = $lSegregatedLots->where('ws.is_deleted', false)
                                        ->where('wsr.is_deleted', false)
                                        ->having('_seg','>', '0');

        $lSegregatedLots = $lSegregatedLots->select(\DB::raw($sSelect))
                                            ->groupBy('wsr.lot_id')
                                            ->groupBy('ws.segregation_type_id')
                                            ->get();

        return view('qms.lots_results.index')
                    ->with('lSegregatedLots', $lSegregatedLots)
                    ->with('actualUserPermission', $this->oCurrentUserPermission)
                    ->with('iFilter', $this->iFilter);
    }

    public function getAnalysis(Request $request)
    {
        $oLot = SWmsLot::find($request->idLot);
        $oItem = $oLot->item;

        $sSelect = "
                        qa.id_analysis,
                        qa.code,
                        qa.name,
                        qa.standard,
                        qa.type_id,
                        qac.min_value,
                        qac.max_value,
                        qat.code as _typecode
                    ";

        $lConfigs = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_ana_configs as qac')
                            ->join('qms_analysis as qa', 'qac.analysis_id', '=', 'qa.id_analysis')
                            ->join('qmss_analysis_types as qat', 'qa.type_id', '=', 'qat.id_analysis_type')
                            ->where('qac.is_deleted', false)
                            ->where('qa.is_deleted', false);

        $lConfigs = $lConfigs->where(function ($query) use ($oItem) {
                            $query->orWhere(function ($query) use ($oItem) {
                                $query->where('item_link_type_id', \Config::get('scsiie.ITEM_LINK.ITEM'))
                                        ->where('item_link_id', $oItem->id_item);
                            })->orWhere(function ($query) use ($oItem) {
                                $query->where('item_link_type_id', \Config::get('scsiie.ITEM_LINK.GENDER'))
                                        ->where('item_link_id', $oItem->item_gender_id);
                            })->orWhere(function ($query) use ($oItem) {
                                $query->where('item_link_type_id', \Config::get('scsiie.ITEM_LINK.GROUP'))
                                        ->where('item_link_id', $oItem->gender->item_group_id);
                            })->orWhere(function ($query) use ($oItem) {
                                $query->where('item_link_type_id', \Config::get('scsiie.ITEM_LINK.FAMILY'))
                                        ->where('item_link_id', $oItem->gender->group->family->id_item_family);
                            })->orWhere(function ($query) use ($oItem) {
                                $query->where('item_link_type_id', \Config::get('scsiie.ITEM_LINK.TYPE'))
                                        ->where('item_link_id', $oItem->gender->item_type_id);
                            })->orWhere(function ($query) use ($oItem) {
                                $query->where('item_link_type_id', \Config::get('scsiie.ITEM_LINK.CLASS'))
                                        ->where('item_link_id', $oItem->gender->item_class_id);
                            });
                        });
                            
        $lConfigsQ = $lConfigs->distinct('analysis_id')
                                ->select(\DB::raw($sSelect))
                                ->get();

        $aAnalysis = $lConfigs->distinct('analysis_id')->lists('analysis_id');

        // $lAnalysis = SAnalysis::where('is_deleted', false)
        //                         ->whereIn('id_analysis', $lConfigs)
        //                         ->orderBy('item_link_type_id', 'DESC')
        //                         ->get();

        $lResults = SResult::where('is_deleted', false)->where('lot_id', $oLot->id_lot)->whereIn('analysis_id', $aAnalysis)->get();

        $oResponse = new SDataResponse();
        $oResponse->setAnalysis($lConfigsQ);
        $oResponse->setResults($lResults);
        $oResponse->oItem = $oItem;
        $oResponse->oLot = $oLot;

        return json_encode($oResponse);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
