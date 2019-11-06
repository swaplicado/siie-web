<?php

namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;
use App\QMS\SQDocSection;
use App\QMS\SQDocElement;
use App\QMS\SElementField;
use App\QMS\SElementType;
use App\QMS\SQDocConfiguration;
use App\QMS\SQDocConfigRow;
use App\QMS\SAnalysis;
use App\QMS\data\SData;
use App\SUtils\SConnectionUtils;

class SQDocConfigurationsController extends Controller
{
    private $oCurrentUserPermission;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_CONFIG_DOCS'), \Config::get('scsys.MODULES.QMS'));
    }

    /**
     * Display a listing of the resource.
    *
    * @param int $cfgZone
    *
    * @return \Illuminate\Http\Response
    */
    public function index($cfgZone)
    {
        $lElementTypes = SElementType::where('is_deleted', false)
                                        ->select('id_element_type', 'element_type', 'table_name', 'is_table')
                                        ->orderBy('element_type', 'ASC')
                                        ->get();

        $lAllAnalysis = SAnalysis::where('is_deleted', false)
                                    ->select('id_analysis',
                                                'code',
                                                'name',
                                                'standard',
                                                'min_value',
                                                'max_value',
                                                'result_unit',
                                                'specification',
                                                'order_num',
                                                'is_deleted',
                                                'type_id')
                                    ->orderBy('code', 'ASC')
                                    ->get();

        return view('qms.doc_configs.index')
                    ->with('cfgZone', $cfgZone)
                    ->with('lAllAnalysis', $lAllAnalysis)
                    ->with('lElementTypes', $lElementTypes);
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
        SConnectionUtils::reconnectCompany();
        $oDocConfig = new SQDocConfiguration($request->all());
        
        $oDocConfig->is_deleted = false;
        $oDocConfig->created_by_id = \Auth::user()->id;
        $oDocConfig->updated_by_id = \Auth::user()->id;

        $oDocConfig->save();

        return $oDocConfig;
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
        $oConfiguration = SQDocConfiguration::find($id);

        $oConfiguration->is_deleted = true;
        $oConfiguration->updated_by_id = \Auth::user()->id;
        $oConfiguration->save();

        return $oConfiguration;
    }

    public function getSectionsData(Request $request)
    {
        $iLinkType = $request->linkType;
        $iLinkId = $request->link;
        $iZone = $request->zone;

        $lConfigurations = \DB::connection(session('db_configuration')->getConnCompany())
                                    ->table('qms_doc_configurations as qdc')
                                    ->join('qms_doc_sections as qds', 'qdc.section_id', '=', 'qds.id_section')
                                    ->join('qms_doc_elements as qde', 'qdc.element_id', '=', 'qde.id_element')
                                    ->join('qmss_element_types as qet', 'qde.element_type_id', '=', 'qet.id_element_type')
                                    ->where('item_link_type_id', $iLinkType)
                                    ->where('item_link_id', $iLinkId)
                                    ->where('qdc.config_zone_id', $iZone)
                                    ->where('qdc.is_deleted', false)
                                    ->select(
                                        'qdc.id_configuration',
                                        'qde.id_element',
                                        'qde.element',
                                        'qde.analysis_id',
                                        'qde.n_values',
                                        'qde.element_type_id',
                                        'qet.element_type',
                                        'qet.table_name',
                                        'qet.is_table',
                                        'qds.id_section',
                                        'qds.title AS _section_title',
                                        'qds.dt_section',
                                        'qds.comments'
                                    )
                                    ->orderBy('section_id', 'ASC')
                                    ->orderBy('element', 'ASC');

        $lConfigurations1 = $lConfigurations->lists('id_section');
        $lConfigurations1 = array_values(array_unique($lConfigurations1));
        $lConfigs = $lConfigurations->get();

        $lSections = SQDocSection::whereIn('id_section', $lConfigurations1)
                                    ->select('id_section',
                                            'title',
                                            'dt_section',
                                            'comments',
                                            'is_deleted')
                                    ->orderBy('order', 'ASC')
                                    ->get();

        $lAllSections = SQDocSection::whereNotIn('id_section', $lConfigurations1)
                                    ->select('id_section',
                                            'title',
                                            'dt_section',
                                            'comments',
                                            'is_deleted')
                                    ->orderBy('order', 'ASC')
                                    ->get();

        $lAllElements = SQDocElement::select('id_element',
                                                'element',
                                                'n_values',
                                                'is_deleted',
                                                'analysis_id',
                                                'element_type_id')
                                    ->where('is_deleted', false)
                                    ->orderBy('element', 'ASC')
                                    ->get();

        $lElementTypes = SElementType::where('is_deleted', false)
                                        ->select('id_element_type', 'element_type', 'table_name', 'is_table')
                                        ->orderBy('element_type', 'ASC')
                                        ->get();

       

        $oData = new SData();

        $oData->lAllSections = $lAllSections;
        $oData->lAllElements = $lAllElements;
        $oData->lElementTypes = $lElementTypes;
        $oData->lSections = $lSections;
        $oData->lConfigurations = $lConfigs;

        return json_encode($oData);
    }

    /**
     * Get the fields of element
     *
     * @param Request $request
     * @return void
     */
    public function getFields(Request $request)
    {
        $iElement = $request->ielement;

        $lFields = SElementField::where('element_id', $iElement)
                                    ->select('id_field',
                                            'field_name',
                                            'is_reported',
                                            'is_deleted',
                                            'element_id')
                                    ->get();

        $oData = new SData();
        $oData->lFields = $lFields;
        $oData->oElement = SQDocElement::find($iElement);

        return json_encode($oData);
    }
}
