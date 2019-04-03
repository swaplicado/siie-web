<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;

use App\SUtils\SValidation;
use App\SUtils\SProcess;
use App\SUtils\SMenu;
use App\SUtils\SUtil;
use App\SUtils\SGuiUtils;
use App\Database\Config;
use App\ERP\SErpConfiguration;

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
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_RESULTS'), \Config::get('scsys.MODULES.QMS'));

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
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
        
        $sSelect = "
                        wl.id_lot,
                        wl.lot,
                        wl.dt_expiry,
                        CONCAT(ei.code, '-', ei.name) AS _item,
                        CONCAT(eu.code) AS _unit,
                        (SELECT COUNT(id_result) FROM qms_results AS qr WHERE lot_id = wl.id_lot AND NOT qr.is_deleted) AS _nresults
                    ";

        $lSegregatedLots = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('wms_lots as wl')
                            ->join('erpu_items as ei', 'wl.item_id', '=', 'ei.id_item')
                            ->join('erpu_units as eu', 'wl.unit_id', '=', 'eu.id_unit');
        
        $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);

        $lSegregatedLots = $lSegregatedLots->where('wl.is_deleted', false)
                                        ->whereBetween('wl.created_at', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);

        $lSegregatedLots = $lSegregatedLots->select(\DB::raw($sSelect))
                                            ->orderBy('wl.lot', 'ASC')
                                            ->orderBy('wl.dt_expiry', 'ASC')
                                            ->get();

        return view('qms.lots_results.index')
                    ->with('lSegregatedLots', $lSegregatedLots)
                    ->with('actualUserPermission', $this->oCurrentUserPermission)
                    ->with('sFilterDate', $sFilterDate)
                    ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($idLot, $idType)
    {
        $oLot = SWmsLot::find($idLot);
        $oItem = $oLot->item;

        $sSelect = "
                        qa.id_analysis,
                        qa.code,
                        qa.name,
                        qa.standard,
                        qa.type_id,
                        qac.min_value,
                        qac.max_value,
                        qat.code as _typecode,
                        item_link_type_id,
                        item_link_id,
                        id_analysis_type,
                        qac.is_deleted AS qac_is_deleted,
                        qa.is_deleted AS qa_is_deleted,
                        COALESCE((SELECT result_value FROM qms_results 
                            WHERE lot_id = ".$oLot->id_lot." AND analysis_id = qa.id_analysis 
                            ORDER BY id_result DESC LIMIT 1), 0) AS _result,
                        COALESCE((SELECT username 
                            FROM ".\DB::connection(Config::getConnSys())->getDatabaseName().".users 
                            WHERE id = (SELECT updated_by_id FROM qms_results 
                            WHERE lot_id = ".$oLot->id_lot." AND analysis_id = qa.id_analysis 
                            ORDER BY id_result DESC LIMIT 1)), '') AS _mod_user
                    ";

        $lConfigsSub = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_ana_configs as qac')
                            ->join('qms_analysis as qa', 'qac.analysis_id', '=', 'qa.id_analysis')
                            ->join('qmss_analysis_types as qat', 'qa.type_id', '=', 'qat.id_analysis_type');
                            
        $lConfigsSub = $lConfigsSub->orderBy('qac.item_link_type_id', 'DESC')
                                    ->select(\DB::raw($sSelect));

        $lQuery = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table(\DB::raw("({$lConfigsSub->toSql()}) as sub"))
                            // ->mergeBindings($lConfigsSub->getQuery()->getQuery())
                            ->where('qac_is_deleted', false)
                            ->where('qa_is_deleted', false)
                            ->where('id_analysis_type', $idType)
                            ->where(function ($query) use ($oItem) {
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
                            })
                            ->groupBy('id_analysis')
                            ->orderBy('item_link_type_id', 'DESC')
                            ->orderBy('id_analysis', 'ASC');

        $lQuery = $lQuery->get();

        $aAnalysis = $lConfigsSub->distinct('analysis_id')->lists('analysis_id');

        $lResults = SResult::where('is_deleted', false)->where('lot_id', $oLot->id_lot)->whereIn('analysis_id', $aAnalysis)->get();

        return view('qms.lots_results.createEdit')
                    ->with('lAnalysis', $lQuery)
                    ->with('lResults', $lResults)
                    ->with('oItem', $oItem)
                    ->with('oLot', $oLot)
                    ->with('title', $idType == \Config::get('scqms.ANALYSIS_TYPE.FQ') ? 
                                                    trans('qms.labels.PHYSIOCHEMICALS') : 
                                                    trans('qms.labels.MICROBIOLOGICALS'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lResults = array();
        $lParameters = $request->all();
        foreach ($lParameters as $name => $val) {
            $aValues = explode("_", $name);
            if (sizeof($aValues) == 2 && $aValues[0] == "dresult") {
                $lResults[$aValues[1]] = $val;
            }
        }

        \DB::transaction(function () use ($lResults, $request)  {
            foreach ($lResults as $key => $value) {
                $oResult = SResult::where('lot_id', $request->idlot)
                            ->where('analysis_id', $key)
                            ->orderBy('id_result', 'DESC')
                            ->first();
                
                if ($oResult != null) {
                    $oResult->result_value = $value;
                    $oResult->updated_by_id = \Auth::user()->id;

                    $oResult->save();
                }
                else {
                    $oNewResult = new SResult();

                    $oNewResult->dt_date = session('work_date')->toDateString();
                    $oNewResult->result_value = $value;
                    $oNewResult->is_deleted = false;
                    $oNewResult->lot_id = $request->idlot;
                    $oNewResult->analysis_id = $key;
                    $oNewResult->created_by_id = \Auth::user()->id;
                    $oNewResult->updated_by_id = \Auth::user()->id;

                    $oNewResult->save();
                }
            }
        });
        

        Flash::success(trans('messages.RESULTS_SAVED'))->important();

        return redirect()->route('qms.results.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request)
    {
        $oBranch = session('branch');
        $lotid = $request->id_lot;
        $oLot = SWmsLot::find($lotid);
        $oItem = $oLot->item;

        $lResults = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_results as qr')
                            ->join('qms_analysis as qa', 'qr.analysis_id', '=', 'qa.id_analysis');      

        $sSelect = "
                        qr.id_result,
                        qr.result_value,
                        qr.analysis_id,
                        qa.name AS _analysis,
                        qa.standard,
                        qa.specification,
                        qa.result_unit,
                        qa.min_value,
                        qa.max_value,
                        COALESCE((SELECT 
                                specification
                            FROM
                                qms_ana_configs AS qac
                            WHERE
                                is_deleted = 0 AND
                                analysis_id = qa.id_analysis
                                    AND ((item_link_type_id = ".\Config::get('scsiie.ITEM_LINK.ITEM')."
                                    AND item_link_id = ".$oItem->id_item.")
                                    OR (item_link_type_id = ".\Config::get('scsiie.ITEM_LINK.GENDER')."
                                    AND item_link_id = ".$oItem->item_gender_id.")
                                    OR (item_link_type_id = ".\Config::get('scsiie.ITEM_LINK.GROUP')."
                                    AND item_link_id = ".$oItem->gender->item_group_id.")
                                    OR (item_link_type_id = ".\Config::get('scsiie.ITEM_LINK.FAMILY')."
                                    AND item_link_id = ".$oItem->gender->group->family->id_item_family.")
                                    OR (item_link_type_id = ".\Config::get('scsiie.ITEM_LINK.TYPE')."
                                    AND item_link_id = ".$oItem->gender->item_type_id.")
                                    OR (item_link_type_id = ".\Config::get('scsiie.ITEM_LINK.CLASS')."
                                    AND item_link_id = ".$oItem->gender->item_class_id."))
                            ORDER BY item_link_type_id DESC
                            LIMIT 1), '') as _specification,
                        qa.specification AS _ana_specification

                        ";

        $lResultsGen = $lResults->select(\DB::raw($sSelect))
                                ->where('qr.is_deleted', false)
                                ->where('lot_id', $lotid)
                                ->orderBy('qa.order_num', 'ASC');

        $lResultsGenFq = clone $lResultsGen;
        $lResultsGenQm = clone $lResultsGen;

        $lFQResults = $lResultsGenFq->where('qa.type_id', \Config::get('scqms.ANALYSIS_TYPE.FQ'))
                                    ->get();
        $lMBResults = $lResultsGenQm->where('qa.type_id', \Config::get('scqms.ANALYSIS_TYPE.MB'))
                                    ->get();

        $sSelectOrg = "
                        qa.id_analysis,
                        qa.code,
                        qa.name as _analysis,
                        qa.standard,
                        qa.type_id,
                        qac.min_value,
                        qac.max_value,
                        qac.result AS _result,
                        qac.specification AS _specification,
                        qac.group_number,
                        item_link_type_id,
                        item_link_id,
                        qa.order_num,
                        qac.is_deleted AS qac_is_deleted,
                        qa.is_deleted AS qa_is_deleted
                    ";

        $lConfigsSub = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_ana_configs as qac')
                            ->join('qms_analysis as qa', 'qac.analysis_id', '=', 'qa.id_analysis');
                            
        $lConfigsSub = $lConfigsSub->orderBy('qac.item_link_type_id', 'DESC')
                                    ->select(\DB::raw($sSelectOrg));

        $lOLResults = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table(\DB::raw("({$lConfigsSub->toSql()}) as sub"))
                            // ->mergeBindings($lConfigsSub->getQuery()->getQuery())
                            ->where('qac_is_deleted', false)
                            ->where('qa_is_deleted', false)
                            ->where('type_id', \Config::get('scqms.ANALYSIS_TYPE.OL'))
                            ->where(function ($query) use ($oItem) {
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
                            })
                            ->groupBy('id_analysis')
                            ->orderBy('type_id', 'DESC')
                            ->orderBy('order_num', 'ASC');

        $lOLResults = $lOLResults->get();

        $oQltySup = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.QLTY_SUPERVISOR'));
        $oQltyMgr = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.QLTY_MANAGER'));

        $view = view('qms.lots_results.print', ['oBranch' => $oBranch,
                                                    'oLot' => $oLot,
                                                    'sDate' => $request->cert_date,
                                                    'lFQResults' => $lFQResults,
                                                    'lOLResults' => $lOLResults,
                                                    'lMBResults' => $lMBResults,
                                                    'sSupervisor' => $oQltySup->val_text,
                                                    'sManager' => $oQltyMgr->val_text
                                                ])
                                                ->render();
        
        // set ukuran kertas dan orientasi
        $pdf = \PDF::loadHTML($view)->setPaper('letter', 'potrait')->setWarnings(false);
        // cetak
        return $pdf->stream();
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
