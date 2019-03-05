<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;

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
                    qac.created_by_id,
                    qac.updated_by_id,
                    qac.created_at,
                    qac.updated_at,
                    uc.username AS creation_user_name,
                    uu.username AS mod_user_name
                    ';

        $lConfigs = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('qms_ana_configs as qac')
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

        $lConfigs = $lConfigs->select(\DB::raw($sSelect))
                     ->get();

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
        $oConfiguration = new SAnalysis($request->all());
        $oConfiguration->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oConfiguration->updated_by_id = \Auth::user()->id;
        $oConfiguration->created_by_id = \Auth::user()->id;
        $oConfiguration->save();

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
