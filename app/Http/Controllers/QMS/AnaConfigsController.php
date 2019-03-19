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
use App\QMS\SAnaConfig;

class AnaConfigsController extends Controller
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

        $sSelect = 'qac.id_config,
                    CONCAT(qa.code, "-", qa.name) AS _analysis,
                    qac.min_value,
                    qac.max_value,
                    qac.created_by_id,
                    qac.updated_by_id,
                    qac.created_at,
                    qac.updated_at,
                    qac.is_deleted,
                    uc.username AS creation_user_name,
                    uu.username AS mod_user_name,
                    CASE
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.CLASS').' THEN "'.trans('siie.CLASSES').'"
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.TYPE').' THEN "'.trans('siie.TYPES').'"
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.FAMILY').' THEN "'.trans('siie.FAMILIES').'"
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GROUP').' THEN "'.trans('siie.GROUPS').'"
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GENDER').' THEN "'.trans('siie.GENDERS').'"
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.ITEM').' THEN "'.trans('siie.ITEMS').'"
                        ELSE "OTHERS"
                    END AS _ntype,
                    CASE
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.CLASS').' THEN (SELECT name FROM erps_item_classes WHERE id_item_class = qac.item_link_id)
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.TYPE').' THEN (SELECT name FROM erps_item_types WHERE id_item_type = qac.item_link_id)
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.FAMILY').' THEN (SELECT name FROM erpu_item_families WHERE id_item_family = qac.item_link_id)
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GROUP').' THEN (SELECT name FROM erpu_item_groups WHERE id_item_group = qac.item_link_id)
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.GENDER').' THEN (SELECT name FROM erpu_item_genders WHERE id_item_gender = qac.item_link_id)
                        WHEN qac.item_link_type_id = '.\Config::get('scsiie.ITEM_LINK.ITEM').' THEN (SELECT name FROM erpu_items WHERE id_item = qac.item_link_id)
                        ELSE "OTHERS"
                    END AS _nname
                    ';

        $lConfigs = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('qms_ana_configs as qac')
                     ->join('qms_analysis as qa', 'qac.analysis_id', '=', 'qa.id_analysis')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'qac.created_by_id', '=', 'uc.id')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'qac.updated_by_id', '=', 'uu.id');

        switch ($this->iFilter) {
            case \Config::get('scsys.FILTER.ACTIVES'):
                $lConfigs = $lConfigs->where('qac.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
            break;

            case \Config::get('scsys.FILTER.DELETED'):
                $lConfigs = $lConfigs->where('qac.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
            break;

            default:
        }

        $lConfigs = $lConfigs->select(\DB::raw($sSelect))->get();

        return view('qms.anaconfigs.index')
                    ->with('lConfigs', $lConfigs)
                    ->with('actualUserPermission', $this->oCurrentUserPermission)
                    ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
                                ->orderBy('order', 'ASC')
                                ->get();

        $lAnalysis = SAnalysis::where('is_deleted', false)
                                ->select('id_analysis', \DB::raw("CONCAT(code, ' - ', name) as ana_name"), 'type_id')
                                ->orderBy('name', 'ASC')
                                ->get();

        return view('qms.anaconfigs.createEdit')
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
            return redirect()->route('qms.anaconfigs.create')
                            ->withErrors($validator)
                            ->withInput();
        }

        $aAnalysis = $request->aranalysis;
        
        $validator->after(function($validator) use ($aAnalysis, $request) {
            foreach ($aAnalysis as $iAnalysis) {
                $oConfiguration =  new SAnaConfig($request->all());

                $oConfiguration->analysis_id = $iAnalysis;

                if (! $oConfiguration->isValid()) {
                    $validator->errors()->add('Error', 'La configuración ya existe.');
                }
            }
        });
        
        if ($validator->fails()) {
            return redirect()->route('qms.anaconfigs.create')
            ->withErrors($validator)
            ->withInput();
        }
        
        \DB::connection(session('db_configuration')->getConnCompany())
                        ->transaction(function () use ($aAnalysis, $request) {

            foreach ($aAnalysis as $iAnalysis) {
                $oConfiguration =  new SAnaConfig($request->all());

                $oConfiguration->analysis_id = $iAnalysis;
                $oConfiguration->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
                $oConfiguration->updated_by_id = \Auth::user()->id;
                $oConfiguration->created_by_id = \Auth::user()->id;

                $oConfiguration->save();
            }

        });

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('qms.anaconfigs.index');
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
        $oAnaConfig = SAnaConfig::find($id);
        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oAnaConfig);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($oAnaConfig);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($oAnaConfig);
        }

        $lLinkTypes = SItemLinkType::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_link_type');

        $lItems = SItem::where('is_deleted', false)->orderBy('code', 'ASC')->get();
        $lGenders = SItemGender::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lGroups = SItemGroup::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lFamilies = SItemFamily::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemTypes = SItemType::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemClass = SItemClass::where('is_deleted', false)->orderBy('name', 'ASC')->get();

        $lTypes = SAnalysisType::where('is_deleted', false)
                                ->orderBy('order', 'ASC')
                                ->get();

        $lAnalysis = SAnalysis::where('is_deleted', false)
                                ->select('id_analysis', \DB::raw("CONCAT(code, ' - ', name) as ana_name"), 'type_id')
                                ->orderBy('name', 'ASC')
                                ->get();

        return view('qms.anaconfigs.createEdit')
                    ->with('oAnaConfig', $oAnaConfig)
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
        $validator = Validator::make($request->all(), [
            'item_link_type_id' => 'required',
            'item_link_id' => 'required',
            'aranalysis' => 'array|required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('qms.anaconfigs.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        $aAnalysis = $request->aranalysis;

        $validator->after(function($validator) use ($aAnalysis, $request, $id) {
            $bFirst = true;
            foreach ($aAnalysis as $iAnalysis) {
                
                if (! $bFirst) {
                    $oConfiguration =  new SAnaConfig($request->all());
                    unset($oConfiguration->id_config);
                    $oConfiguration->analysis_id = $iAnalysis;
                }
                else {
                    $oConfiguration = SAnaConfig::find($id);

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

        if ($validator->fails()) {
            return redirect()->route('qms.anaconfigs.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        \DB::connection(session('db_configuration')->getConnCompany())
                ->transaction(function () use ($aAnalysis, $request, $id) {
            
            $bFirst = true;
            foreach ($aAnalysis as $iAnalysis) {
                if (! $bFirst) {
                    $oConfiguration =  new SAnaConfig($request->all());
                    unset($oConfiguration->id_config);
                    $oConfiguration->analysis_id = $iAnalysis;
                }
                else {
                    $oConfiguration = SAnaConfig::find($id);

                    $oConfiguration->item_link_type_id = $request->item_link_type_id;
                    $oConfiguration->item_link_id = $request->item_link_id;
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

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('qms.anaconfigs.index');
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

        $oAnaConfig = SAnaConfig::find($id);

        $oAnaConfig->is_deleted = \Config::get('scsys.STATUS.DEL');
        $oAnaConfig->updated_by_id = \Auth::user()->id;

        $errors = $oAnaConfig->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('qms.anaconfigs.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('qms.anaconfigs.index');
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
        $oAnaConfig = SAnaConfig::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oAnaConfig);

        $oAnaConfig->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oAnaConfig->updated_by_id = \Auth::user()->id;

        $errors = $oAnaConfig->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($error);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('qms.anaconfigs.index');
    }
}
