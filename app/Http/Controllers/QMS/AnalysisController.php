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

use App\QMS\SAnalysis;
use App\QMS\SAnalysisType;

class AnalysisController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_ANALYSIS_CONFIGURATION'), \Config::get('scsys.MODULES.QMS'));

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

        $sSelect = '
                    qa.id_analysis,
                    qa.code,
                    qa.name,
                    qa.result_unit,
                    qa.specification,
                    qa.order_num,
                    qa.is_deleted,
                    qa.type_id,
                    qat.code AS type_code,
                    qa.created_by_id,
                    qa.updated_by_id,
                    qa.created_at,
                    qa.updated_at,
                    uc.username AS creation_user_name,
                    uu.username AS mod_user_name
                    ';

        $lAnalysis = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('qms_analysis as qa')
                     ->join('qmss_analysis_types as qat', 'qa.type_id', '=', 'qat.id_analysis_type')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'qa.created_by_id', '=', 'uc.id')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'qa.updated_by_id', '=', 'uu.id');

        switch ($this->iFilter) {
            case \Config::get('scsys.FILTER.ACTIVES'):
                $lAnalysis = $lAnalysis->where('qa.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
            break;

            case \Config::get('scsys.FILTER.DELETED'):
                $lAnalysis = $lAnalysis->where('qa.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
            break;

            default:
        }

        $lAnalysis = $lAnalysis->select(\DB::raw($sSelect))
                     ->where('qa.name', 'LIKE', "%".$request->name."%")
                     ->orderBy('order_num', 'ASC')
                     ->get();

        return view('qms.analysis.index')
                    ->with('lAnalysis', $lAnalysis)
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

        $types = SAnalysisType::orderBy('name','ASC')->lists('name','id_analysis_type');

        return view('qms.analysis.createEdit')
                        ->with('types', $types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $oAnalysis = new SAnalysis($request->all());
        $oAnalysis->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oAnalysis->updated_by_id = \Auth::user()->id;
        $oAnalysis->created_by_id = \Auth::user()->id;
        $oAnalysis->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('qms.analysis.index');
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
        $oAnalysis = SAnalysis::find($id);
        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oAnalysis);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($oAnalysis);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($oAnalysis);
        }

        $types = SAnalysisType::orderBy('name','ASC')->lists('name', 'id_analysis_type');

        return view('qms.analysis.createEdit')
                      ->with('oAnalysis', $oAnalysis)
                      ->with('types', $types);
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
        $oAnalysis = SAnalysis::find($id);
        $oAnalysis->fill($request->all());

        $oAnalysis->updated_by_id = \Auth::user()->id;

        $errors = $oAnalysis->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('qms.analysis.index', 0);
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

        $oAnalysis = SAnalysis::find($id);

        $oAnalysis->is_deleted = \Config::get('scsys.STATUS.DEL');
        $oAnalysis->updated_by_id = \Auth::user()->id;

        $errors = $oAnalysis->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('qms.analysis.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('qms.analysis.index');
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
        $oAnalysis = SAnalysis::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oAnalysis);

        $oAnalysis->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oAnalysis->updated_by_id = \Auth::user()->id;

        $errors = $oAnalysis->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('qms.analysis.index');
    }
}
