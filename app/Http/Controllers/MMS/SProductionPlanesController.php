<?php namespace App\Http\Controllers\MMS;

use App\Database\Config;
use App\Http\Controllers\Controller;
use App\MMS\SProductionPlan;
use App\SUtils\SProcess;
use App\SUtils\SValidation;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class SProductionPlanesController extends Controller {

    private $oCurrentUserPermission;
    private $iFilter;
    private $sClassNav;

    public function __construct()
    {
         $this->oCurrentUserPermission = SProcess::constructor($this,
         \Config::get('scperm.PERMISSION.MMS_PRODUCTION_PLANES'),
         \Config::get('scsys.MODULES.MMS'));

         $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request, $iFolio = 0)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

        $sSelect = '
                      id_production_plan,
                      folio,
                      production_plan,
                      dt_start,
                      dt_end,
                      floor_id,
                      mf.name AS plant,
                      eb.name AS branch,
                      mpp.is_deleted,
                      mpp.created_by_id,
                      mpp.updated_by_id
                    ';

        $oPlanes = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('mms_production_planes as mpp')
                     ->join('mms_floor as mf', 'mpp.floor_id', '=', 'mf.id_floor')
                     ->join('erpu_branches as eb', 'mf.branch_id', '=', 'eb.id_branch')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'mpp.created_by_id', '=', 'uc.id')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'mpp.updated_by_id', '=', 'uu.id');

       switch ($this->iFilter) {
         case \Config::get('scsys.FILTER.ACTIVES'):
             $oPlanes = $oPlanes->where('mpp.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
           break;

         case \Config::get('scsys.FILTER.DELETED'):
             $oPlanes = $oPlanes->where('mpp.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
           break;

         default:
       }

       $oPlanes = $oPlanes->select(\DB::raw($sSelect))
                     ->where('folio', 'LIKE', "%".$request->name."%")
                     ->get();

       $sTitle = 'Planes de producción';

        return view('mms.planes.index')
            ->with('planes', $oPlanes)
            ->with('sTitle', $sTitle)
            ->with('iFolio', $iFolio)
            ->with('actualUserPermission', $this->oCurrentUserPermission)
            ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new SProductionPlan.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($iFormula = 0)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id)) {
          return redirect()->route('notauthorized');
        }

        $sTitle = trans('mms.titles.CREATE_PRODUCTION_PLAN');

        $lFloors = \DB::connection(session('db_configuration')->getConnCompany())
                          ->table('mms_floor AS mf')
                          ->where('branch_id', session('branch')->id_branch)
                          ->where('is_deleted', false)
                          ->orderBy('name', 'ASC')
                          ->lists('name', 'id_floor');

        return view('mms.planes.createEdit')
                  ->with('lFloors', $lFloors)
                  ->with('sTitle', $sTitle);
    }

    /**
     * [store saves the Plan in the database]
     * @param  Request $request [description]
     *
     * @return [redirect to mms.planes.index]
     */
    public function store(Request $request)
    {
        $oProductionPlan = new SProductionPlan($request->all());

        if ($oProductionPlan->dt_start > $oProductionPlan->dt_end) {
            return redirect()->back()->withErrors(['Elija un rango de fechas válido']);
        }

        $oPlan = SProductionPlan::max('folio');

        $oProductionPlan->folio = is_numeric($oPlan) ? ($oPlan + 1) : 1;
        $oProductionPlan->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oProductionPlan->created_by_id = \Auth::user()->id;
        $oProductionPlan->updated_by_id = \Auth::user()->id;

        $oProductionPlan->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('mms.planes.index', session('utils')->formatFolio($oProductionPlan->folio));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * 
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $oProductionPlan = SProductionPlan::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oProductionPlan);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($oProductionPlan);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $lFloors = \DB::connection(session('db_configuration')->getConnCompany())
                          ->table('mms_floor AS mf')
                          ->where('branch_id', session('branch')->id_branch)
                          ->where('is_deleted', false)
                          ->orderBy('name', 'ASC')
                          ->lists('name', 'id_floor');

        return view('mms.planes.createEdit')
                    ->with('oPlan', $oProductionPlan)
                    ->with('lFloors', $lFloors)
                    ->with('sTitle', trans('mms.titles.EDIT_PRODUCTION_PLAN'));
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
        $oProductionPlan = SProductionPlan::find($id);
        $oProductionPlan->fill($request->all());

        if ($oProductionPlan->dt_start > $oProductionPlan->dt_end) {
            return redirect()->back()->withErrors(['Elija un rango de fechas válido']);
        }

        $oProductionPlan->updated_by_id = \Auth::user()->id;

        $errors = $oProductionPlan->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('mms.planes.index', 0);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        session('utils')->validateDestroy($this->oCurrentUserPermission->privilege_id);

        $oProductionPlan = SProductionPlan::find($id);

        $oProductionPlan->is_deleted = \Config::get('scsys.STATUS.DEL');
        $oProductionPlan->updated_by_id = \Auth::user()->id;

        $errors = $oProductionPlan->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('mms.planes.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('mms.planes.index');
    }

    public function activate(Request $request, $id)
    {
        $oProductionPlan = SProductionPlan::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oProductionPlan);

        $oProductionPlan->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oProductionPlan->updated_by_id = \Auth::user()->id;

        $errors = $oProductionPlan->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('mms.planes.index');
    }

}
