<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\WMS\SFolioRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\WMS\SMvtClass;
use App\WMS\SMvtType;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\WMS\SWhsType;
use App\WMS\SFolio;
use App\WMS\SWmsValidations;
use App\SUtils\SProcess;

class SFoliosController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.STK_MOVS_MANAGE'), \Config::get('scsys.MODULES.WMS'));

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

      $lFolios = SFolio::Search($request->name, $this->iFilter)->orderBy('folio_start', 'ASC')->paginate(20);

      // dd($lFolios);

      return view('wms.folios.index')
                  ->with('folios', $lFolios)
                  ->with('actualUserPermission', $this->oCurrentUserPermission)
                  ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new folio.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $lClasses = SMvtClass::orderBy('name', 'ASC')->lists('name', 'id_mvt_class');
        $lTypes = SMvtType::orderBy('name', 'ASC')->get();
        $lBranches = SBranch::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_branch');
        $lWarehouses = SWarehouse::where('is_deleted', false)->get();
        $lLocations = SLocation::where('is_deleted', false)->get();

        return view('wms.folios.createEdit')
                      ->with('mvtClasses', $lClasses)
                      ->with('mvtTypes', $lTypes)
                      ->with('branches', $lBranches)
                      ->with('warehouses', $lWarehouses)
                      ->with('locations', $lLocations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SFolioRequest $request)
    {
      $folio = new SFolio($request->all());
      // dd($folio);
      if ($folio->aux_branch_id == '')
      {
          $folio->container_type_id = \Config::get('scwms.CONTAINERS.COMPANY');
          $folio->container_id = session('partner')->id_partner;
      }
      elseif ($folio->aux_whs_id == '0') {
          $folio->container_type_id = \Config::get('scwms.CONTAINERS.BRANCH');
          $folio->container_id = $folio->aux_branch_id;
      }
      elseif ($folio->aux_location_id == '0') {
          $folio->container_type_id = \Config::get('scwms.CONTAINERS.WAREHOUSE');
          $folio->container_id = $folio->aux_whs_id;
      }
      else {
          $folio->container_type_id = \Config::get('scwms.CONTAINERS.LOCATION');
          $folio->container_id = $folio->aux_location_id;
      }

      $folio->mvt_trn_type_id = 1;
      $folio->mvt_adj_type_id = 1;
      $folio->mvt_mfg_type_id = 1;
      $folio->mvt_exp_type_id = 1;
      $folio->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $folio->updated_by_id = \Auth::user()->id;
      $folio->created_by_id = \Auth::user()->id;

      $aErrors = SWmsValidations::validateFolios($folio);
      if(sizeof($aErrors) > 0)
      {
          return redirect()->back()->withErrors($aErrors)->withInput();
      }

      unset($folio->aux_branch_id);
      unset($folio->aux_whs_id);
      unset($folio->aux_location_id);

      $folio->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('wms.folios.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $folio = SFolio::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $folio->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $folio->aux_branch_id = '';
        $folio->aux_whs_id = '';
        $folio->aux_location_id = '';

        switch ($folio->container_type_id) {
          case \Config::get('scwms.CONTAINERS.COMPANY'):
            break;

          case \Config::get('scwms.CONTAINERS.BRANCH'):
            $folio->aux_branch_id = $folio->container_id;
            break;

          case \Config::get('scwms.CONTAINERS.WAREHOUSE'):
            $folio->aux_branch_id = SWarehouse::find($folio->container_id)->branch->id_branch;
            $folio->aux_whs_id = $folio->container_id;
            break;

          case \Config::get('scwms.CONTAINERS.LOCATION'):
            $location = SLocation::find($folio->container_id);
            $folio->aux_branch_id = $location->warehouse->branch_id;
            $folio->aux_whs_id = $location->whs_id;
            $folio->aux_location_id = $folio->container_id;
            break;

          default:
            # code...
            break;
        }

        $lClasses = SMvtClass::orderBy('name', 'ASC')->lists('name', 'id_mvt_class');
        $lTypes = SMvtType::orderBy('name', 'ASC')->lists('name', 'id_mvt_type');
        $lBranches = SBranch::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_branch');
        $lWarehouses = SWarehouse::where('is_deleted', false)->get();
        $lLocations = SLocation::where('is_deleted', false)->get();

        return view('wms.folios.createEdit')
                    ->with('folio', $folio)
                    ->with('mvtClasses', $lClasses)
                    ->with('mvtTypes', $lTypes)
                    ->with('branches', $lBranches)
                    ->with('warehouses', $lWarehouses)
                    ->with('locations', $lLocations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SFolioRequest $request, $id)
    {
        $folio = SFolio::find($id);
        $folio->fill($request->all());

        if ($folio->aux_branch_id == '')
        {
            $folio->container_type_id = \Config::get('scwms.CONTAINERS.COMPANY');
            $folio->container_id = session('partner')->id_partner;
        }
        elseif ($folio->aux_whs_id == '') {
            $folio->container_type_id = \Config::get('scwms.CONTAINERS.BRANCH');
            $folio->container_id = $folio->aux_branch_id;
        }
        elseif ($folio->aux_location_id == '') {
            $folio->container_type_id = \Config::get('scwms.CONTAINERS.WAREHOUSE');
            $folio->container_id = $folio->aux_whs_id;
        }
        else {
            $folio->container_type_id = \Config::get('scwms.CONTAINERS.LOCATION');
            $folio->container_id = $folio->aux_location_id;
        }

        $folio->updated_by_id = \Auth::user()->id;

        $aErrors = SWmsValidations::validateFolios($folio, true);

        if(sizeof($aErrors) > 0)
        {
            return redirect()->back()->withErrors($aErrors)->withInput();
        }

        unset($folio->aux_branch_id);
        unset($folio->aux_whs_id);
        unset($folio->aux_location_id);

        $folio->save();

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('wms.folios.index');
    }

    public function activate(Request $request, $id)
    {
        $folio = SFolio::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $folio->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $folio->fill($request->all());
        $folio->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $folio->updated_by_id = \Auth::user()->id;

        $folio->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('wms.folios.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! SValidation::canDestroy($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $folio = SFolio::find($id);
        $folio->fill($request->all());
        $folio->is_deleted = \Config::get('scsys.STATUS.DEL');
        $folio->updated_by_id = \Auth::user()->id;

        $folio->save();
        #$user->delete();

        Flash::error(trans('messages.REG_DELETED'))->important();
        return redirect()->route('wms.folios.index');
    }


}
