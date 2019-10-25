<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Validator;

use App\Database\Config;
use App\SUtils\SValidation;
use App\SUtils\SProcess;
use App\SUtils\SMenu;
use App\SUtils\SUtil;

use App\ERP\SItemLinkType;
use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\ERP\SItemFamily;
use App\ERP\SItemGroup;
use App\ERP\SItemGender;
use App\ERP\SItem;

use App\QMS\SAnalysisType;
use App\QMS\SAnalysis;
use App\QMS\SCertConfig;

class CertConfigsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;
    private $iAnaType;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_ANALYSIS_CONFIGURATION'), \Config::get('scsys.MODULES.QMS'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
        $this->iAnaType = 0;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $iAnaType = 0)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $this->iAnaType = $iAnaType;

        $sSelect = 'qcc.id_cert_configuration,
                    CONCAT(qa.code, "-", qa.name) AS _analysis,
                    qat.code AS _ana_type,
                    qcc.specification,
                    qcc.created_by_id,
                    qcc.updated_by_id,
                    qcc.created_at,
                    qcc.updated_at,
                    qcc.is_deleted,
                    uc.username AS creation_user_name,
                    uu.username AS mod_user_name,
                    CASE
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.CLASS').' THEN "'.trans('siie.CLASSES').'"
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.TYPE').' THEN "'.trans('siie.TYPES').'"
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.FAMILY').' THEN "'.trans('siie.FAMILIES').'"
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GROUP').' THEN "'.trans('siie.GROUPS').'"
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GENDER').' THEN "'.trans('siie.GENDERS').'"
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.ITEM').' THEN "'.trans('siie.ITEMS').'"
                        ELSE "OTHERS"
                    END AS _ntype,
                    CASE
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.CLASS').' THEN (SELECT name FROM erps_item_classes WHERE id_item_class = qcc.item_link_id)
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.TYPE').' THEN (SELECT name FROM erps_item_types WHERE id_item_type = qcc.item_link_id)
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.FAMILY').' THEN (SELECT name FROM erpu_item_families WHERE id_item_family = qcc.item_link_id)
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GROUP').' THEN (SELECT name FROM erpu_item_groups WHERE id_item_group = qcc.item_link_id)
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GENDER').' THEN (SELECT name FROM erpu_item_genders WHERE id_item_gender = qcc.item_link_id)
                        WHEN qcc.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.ITEM').' THEN (SELECT name FROM erpu_items WHERE id_item = qcc.item_link_id)
                        ELSE "OTHERS"
                    END AS _nname
                    ';

        $lConfigs = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('qms_cert_configurations as qcc')
                     ->join('qms_analysis as qa', 'qcc.analysis_id', '=', 'qa.id_analysis')
                     ->join('qmss_analysis_types as qat', 'qa.type_id', '=', 'qat.id_analysis_type')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'qcc.created_by_id', '=', 'uc.id')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'qcc.updated_by_id', '=', 'uu.id');

        switch ($this->iFilter) {
            case \Config::get('scsys.FILTER.ACTIVES'):
                $lConfigs = $lConfigs->where('qcc.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
            break;

            case \Config::get('scsys.FILTER.DELETED'):
                $lConfigs = $lConfigs->where('qcc.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
            break;

            default:
        }

        if ($iAnaType == 0) {
            $sSel = "qcc.min_value,
                        qcc.max_value,";
            $lConfigs = $lConfigs->where('qa.type_id', '!=', \Config::get('scqms.ANALYSIS_TYPE.OL'));
            $sView = "qms.certconfigs.index";
        }
        else {
            $sSel = "qcc.result,
                    qcc.specification,
                    qcc.group_number,";

            $lConfigs = $lConfigs->where('qa.type_id', \Config::get('scqms.ANALYSIS_TYPE.OL'));
            $sView = "qms.certconfigs.indexorg";
        }

        $sSelect = $sSel.$sSelect;

        $lConfigs = $lConfigs->select(\DB::raw($sSelect))->get();

        return view($sView)
                ->with('lConfigs', $lConfigs)
                ->with('iAnaType', $iAnaType)
                ->with('actualUserPermission', $this->oCurrentUserPermission)
                ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($iAnaType)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $lLinkTypes = SItemLinkType::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_link_type');

        $lItems = SItem::where('is_deleted', false)->orderBy('code', 'ASC')->get();
        $lGenders = SItemGender::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lGroups = SItemGroup::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lFamilies = SItemFamily::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemTypes = SItemType::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemClass = SItemClass::where('is_deleted', false)->orderBy('name', 'ASC')->get();

        $lTypes = SAnalysisType::where('is_deleted', false)
                                ->orderBy('order', 'ASC');

        $lAnalysis = SAnalysis::where('is_deleted', false)
                                ->select('id_analysis', \DB::raw("CONCAT(code, ' - ', name) as ana_name"), 'type_id', 'name')
                                ->orderBy('id_analysis', 'ASC');

        if ($iAnaType == \Config::get('scqms.ANALYSIS_TYPE.OL')) {
            $sView = "qms.certconfigs.createEditorg";
            $lTypes = $lTypes->where('id_analysis_type', \Config::get('scqms.ANALYSIS_TYPE.OL'));
            $lAnalysis = $lAnalysis->where('type_id', \Config::get('scqms.ANALYSIS_TYPE.OL'));
        }
        else {
            $sView = "qms.certconfigs.createEdit";
        }
        
        $lTypes = $lTypes->get();
        $lAnalysis = $lAnalysis->get();

        return view($sView)
                    ->with('lTypes', $lTypes)
                    ->with('lAnalysis', $lAnalysis)
                    ->with('links', $lLinkTypes)
                    ->with('items', $lItems)
                    ->with('genders', $lGenders)
                    ->with('groups', $lGroups)
                    ->with('families', $lFamilies)
                    ->with('itemTypes', $lItemTypes)
                    ->with('itemClasses', $lItemClass);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
                        'item_link_type_id' => 'required',
                        'item_link_id' => 'required',
                        'aranalysis' => 'array|required',
                    ]);

        if ($validator->fails()) {
            return redirect()->route('qms.certconfigs.create')
                            ->withErrors($validator)
                            ->withInput();
        }

        $aAnalysis = $request->aranalysis;
        
        $validator->after(function($validator) use ($aAnalysis, $request) {
            foreach ($aAnalysis as $iAnalysis) {
                $oConfiguration =  new SCertConfig($request->all());

                $oConfiguration->analysis_id = $iAnalysis;

                if (! $oConfiguration->isValid()) {
                    $validator->errors()->add('Error', 'La configuración ya existe.');
                }
            }
        });
        
        if ($validator->fails()) {
            return redirect()->route('qms.certconfigs.create')
                    ->withErrors($validator)
                    ->withInput();
        }
        
        \DB::connection(session('db_configuration')->getConnCompany())
                        ->transaction(function () use ($aAnalysis, $request) {

            foreach ($aAnalysis as $iAnalysis) {
                $oConfiguration =  new SCertConfig($request->all());

                $oConfiguration->analysis_id = $iAnalysis;
                $oConfiguration->is_text = false;
                $oConfiguration->result = '';
                $oConfiguration->group_number = 1;
                $oConfiguration->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
                $oConfiguration->updated_by_id = \Auth::user()->id;
                $oConfiguration->created_by_id = \Auth::user()->id;

                $oConfiguration->save();
            }

        });

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('qms.certconfigs.index', 0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOrg(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_link_type_id' => 'required',
            'item_link_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                        ->route('qms.certconfigs.create', \Config::get('scqms.ANALYSIS_TYPE.OL'))
                        ->withErrors($validator)
                        ->withInput();
        }

        $params = $request->all();
        $aAnalysis = array();

        foreach ($params as $key => $value) {
            if (! strpos($key, '+')) {
                continue;
            }
            
            $vals = explode("+", $key);
            if (sizeof($vals) > 1 && $vals[1] == 'anaid') {
                $aAnalysis[$vals[0]] = $value;
            }
        }

        $iMaxGroup = SCertConfig::max('group_number');
        $iGroup = $iMaxGroup + 1;

        $lConfigs = array();

        $validator->after(function($validator) use ($aAnalysis, $request, $iGroup, &$lConfigs) {
            foreach ($aAnalysis as $key => $value) {
                $oConfiguration =  new SCertConfig();
    
                $oConfiguration->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
                $oConfiguration->is_text = true;
                $oConfiguration->result = "CUMPLE";
                $oConfiguration->specification = $value;
                $oConfiguration->group_number = $iGroup;
                $oConfiguration->analysis_id = $key;
                $oConfiguration->item_link_type_id = $request->item_link_type_id;
                $oConfiguration->item_link_id = $request->item_link_id;
                $oConfiguration->min_value = 0;
                $oConfiguration->max_value = 0;
                $oConfiguration->updated_by_id = \Auth::user()->id;
                $oConfiguration->created_by_id = \Auth::user()->id;

                if (! $oConfiguration->isValid()) {
                    $validator->errors()->add('Error', 'La configuración ya existe.');
                }
    
                array_push($lConfigs, $oConfiguration);
            }
        });
        
        if ($validator->fails()) {
            return redirect()
                    ->route('qms.certconfigs.create', \Config::get('scqms.ANALYSIS_TYPE.OL'))
                    ->withErrors($validator)
                    ->withInput();
        }

        \DB::connection(session('db_configuration')->getConnCompany())
                        ->transaction(function () use (&$lConfigs) {

            foreach ($lConfigs as $oConfiguration) {
                $oConfiguration->save();
            }
        });

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('qms.certconfigs.index', \Config::get('scqms.ANALYSIS_TYPE.OL'));
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
        $oCertConfig = SCertConfig::find($id);
        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oCertConfig);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($oCertConfig);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($oCertConfig);
        }

        $lLinkTypes = SItemLinkType::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_link_type');
        
        $lItems = SItem::where('is_deleted', false)->orderBy('code', 'ASC')->get();
        $lGenders = SItemGender::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lGroups = SItemGroup::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lFamilies = SItemFamily::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemTypes = SItemType::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemClass = SItemClass::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        
        $lTypes = SAnalysisType::where('is_deleted', false)
                                ->orderBy('order', 'ASC');

        $lAnalysis = SAnalysis::where('is_deleted', false)
                                ->select('id_analysis', \DB::raw("CONCAT(code, ' - ', name) as ana_name"), 'type_id', 'name')
                                ->orderBy('id_analysis', 'ASC');

        if ($oCertConfig->analysis->type_id == \Config::get('scqms.ANALYSIS_TYPE.OL')) {
            $sView = "qms.certconfigs.createEditorg";
            $lTypes = $lTypes->where('id_analysis_type', \Config::get('scqms.ANALYSIS_TYPE.OL'));
            $lAnalysis = $lAnalysis->where('type_id', \Config::get('scqms.ANALYSIS_TYPE.OL'));

            $oCertConfig = SCertConfig::where('group_number', $oCertConfig->group_number)
                                        ->where('is_deleted', false)
                                        ->orderBy('analysis_id', 'ASC')
                                        ->get();
        }
        else {
            $sView = "qms.certconfigs.createEdit";
        }
        
        $lTypes = $lTypes->get();
        $lAnalysis = $lAnalysis->get();

        return view($sView)
                ->with('oCertConfig', $oCertConfig)
                ->with('lTypes', $lTypes)
                ->with('lAnalysis', $lAnalysis)
                ->with('links', $lLinkTypes)
                ->with('items', $lItems)
                ->with('genders', $lGenders)
                ->with('groups', $lGroups)
                ->with('families', $lFamilies)
                ->with('itemTypes', $lItemTypes)
                ->with('itemClasses', $lItemClass);
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
        $isOrganolepctic = $id == 'updorg';

        if ($isOrganolepctic) {
            $validator = Validator::make($request->all(), [
                'item_link_type_id' => 'required',
                'item_link_id' => 'required',
            ]);
        }
        else {
            $validator = Validator::make($request->all(), [
                'item_link_type_id' => 'required',
                'item_link_id' => 'required',
                'aranalysis' => 'array|required',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->route('qms.certconfigs.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        if ($isOrganolepctic) {
            $params = $request->all();
            $aConfigs = array();

            foreach ($params as $key => $value) {
                if (! strpos($key, '+')) {
                    continue;
                }
                
                $vals = explode("+", $key);
                if (sizeof($vals) > 1 && $vals[1] == 'anaid') {
                    $aConfigs[$vals[0]] = $value;
                }
            }
            $lConfigs = array();

            $validator->after(function($validator) use ($aConfigs, $request, &$lConfigs) {
                foreach ($aConfigs as $iConfig => $sSpec) {
                    $oConfiguration = SCertConfig::find($iConfig);

                    $oConfiguration->item_link_type_id = $request->item_link_type_id;
                    $oConfiguration->item_link_id = $request->item_link_id;
                    $oConfiguration->specification = $sSpec;

                    if (! $oConfiguration->isValid()) {
                        $validator->errors()->add('Error', 'La configuración ya existe.');
                    }

                    array_push($lConfigs, $oConfiguration);
                }
            });

        }
        else {
            $aAnalysis = $request->aranalysis;

            $validator->after(function($validator) use ($aAnalysis, $request, $id) {
                $bFirst = true;
                foreach ($aAnalysis as $iAnalysis) {
                    
                    if (! $bFirst) {
                        $oConfiguration =  new SCertConfig($request->all());
                        unset($oConfiguration->id_cert_configuration);
                        $oConfiguration->analysis_id = $iAnalysis;
                    }
                    else {
                        $oConfiguration = SCertConfig::find($id);

                        $oConfiguration->item_link_type_id = $request->item_link_type_id;
                        $oConfiguration->item_link_id = $request->item_link_id;
                        $oConfiguration->analysis_id = $iAnalysis;

                        $bFirst = false;
                    }

                    if (! $oConfiguration->isValid()) {
                        $validator->errors()->add('Error', 'La configuración ya existe.');
                    }
                }
            });
        }
        
        if ($validator->fails()) {
            return redirect()->route('qms.certconfigs.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        if ($isOrganolepctic) {

            \DB::connection(session('db_configuration')->getConnCompany())
                ->transaction(function () use (&$lConfigs) {
                foreach ($lConfigs as $oConfig) {
                    $oConfig->save();
                }
            });

        }
        else {
            \DB::connection(session('db_configuration')->getConnCompany())
                ->transaction(function () use ($aAnalysis, $request, $id) {
                
                $bFirst = true;
                foreach ($aAnalysis as $iAnalysis) {
                    if (! $bFirst) {
                        $oConfiguration =  new SCertConfig($request->all());
                        unset($oConfiguration->id_cert_configuration);
                        $oConfiguration->analysis_id = $iAnalysis;
                    }
                    else {
                        $oConfiguration = SCertConfig::find($id);

                        $oConfiguration->item_link_type_id = $request->item_link_type_id;
                        $oConfiguration->item_link_id = $request->item_link_id;
                        $oConfiguration->specification = $request->specification;
                        $oConfiguration->min_value = $request->min_value;
                        $oConfiguration->max_value = $request->max_value;
                        $oConfiguration->analysis_id = $iAnalysis;

                        $bFirst = false;
                    }

                    $oConfiguration->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
                    $oConfiguration->updated_by_id = \Auth::user()->id;
                    $oConfiguration->created_by_id = \Auth::user()->id;

                    $errors = $oConfiguration->save();

                    if (sizeof($errors) > 0)
                    {
                        throw new Exception($errors);
                    }
                }
            });
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('qms.certconfigs.index', $isOrganolepctic ? \Config::get('scqms.ANALYSIS_TYPE.OL') : 0);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        session('utils')->validateDestroy($this->oCurrentUserPermission->privilege_id);

        $oCertConfig = SCertConfig::find($id);

        $oCertConfig->is_deleted = \Config::get('scsys.STATUS.DEL');
        $oCertConfig->updated_by_id = \Auth::user()->id;

        $errors = $oCertConfig->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('qms.certconfigs.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('qms.certconfigs.index', 0);
    }

    /**
     * set the is_deleted flag to false
     *
     * @param  Request $request
     * @param  integer  $id  id of SAnalysis
     *
     * @return redirect()->route('mms.orders.index')
     */
    public function activate(Request $request, $id)
    {
        $oCertConfig = SCertConfig::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oCertConfig);

        $oCertConfig->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oCertConfig->updated_by_id = \Auth::user()->id;

        $errors = $oCertConfig->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($error);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('qms.certconfigs.index', 0);
    }
}
