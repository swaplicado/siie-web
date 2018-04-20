<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\WMS\SLimitRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\ERP\SItem;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\WMS\SWhsType;
use App\WMS\SLimit;
use App\WMS\SWmsValidations;
use App\SUtils\SProcess;

class SLimitsController extends Controller
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

      $lLimits = SLimit::Search($request->name, $this->iFilter)->orderBy('item_id', 'ASC')->paginate(20);

      // dd($lLimits);

      return view('wms.limits.index')
                  ->with('limits', $lLimits)
                  ->with('actualUserPermission', $this->oCurrentUserPermission)
                  ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new limit.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $lItems = SItem::where('is_deleted', false)->orderBy('name', 'ASC')
                          ->select(\DB::raw("CONCAT(code, '-', name) AS item_option, id_item"))
                          ->lists('item_option', 'id_item');
        $lBranches = SBranch::where('is_deleted', false)
                              ->where('partner_id',session('partner')->id_partner)
                              ->orderBy('name', 'ASC')->lists('name', 'id_branch');
        $lWarehouses = SWarehouse::where('is_deleted', false)->get();
        $lLocations = SLocation::where('is_deleted', false)->get();

        return view('wms.limits.createEdit')
                      ->with('items', $lItems)
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
    public function store(SLimitRequest $request)
    {
      $limit = new SLimit($request->all());
      // dd($limit);
      if ($limit->aux_branch_id == '')
      {
          $limit->container_type_id = \Config::get('scwms.CONTAINERS.COMPANY');
          $limit->container_id = session('partner')->id_partner;
      }
      elseif ($limit->aux_whs_id == '0') {
          $limit->container_type_id = \Config::get('scwms.CONTAINERS.BRANCH');
          $limit->container_id = $limit->aux_branch_id;
      }
      elseif ($limit->aux_location_id == '0') {
          $limit->container_type_id = \Config::get('scwms.CONTAINERS.WAREHOUSE');
          $limit->container_id = $limit->aux_whs_id;
      }
      else {
          $limit->container_type_id = \Config::get('scwms.CONTAINERS.LOCATION');
          $limit->container_id = $limit->aux_location_id;
      }

      $limit->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $limit->updated_by_id = \Auth::user()->id;
      $limit->created_by_id = \Auth::user()->id;

      // $aErrors = SWmsValidations::validateLimits($limit);
      // if(sizeof($aErrors) > 0)
      // {
      //     return redirect()->back()->withErrors($aErrors)->withInput();
      // }

      unset($limit->aux_branch_id);
      unset($limit->aux_whs_id);
      unset($limit->aux_location_id);

      $limit->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('wms.limits.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $limit = SLimit::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $limit);

        $limit->aux_branch_id = '';
        $limit->aux_whs_id = '';
        $limit->aux_location_id = '';

        switch ($limit->container_type_id) {
          case \Config::get('scwms.CONTAINERS.COMPANY'):
            break;

          case \Config::get('scwms.CONTAINERS.BRANCH'):
            $limit->aux_branch_id = $limit->container_id;
            break;

          case \Config::get('scwms.CONTAINERS.WAREHOUSE'):
            $limit->aux_branch_id = SWarehouse::find($limit->container_id)->branch->id_branch;
            $limit->aux_whs_id = $limit->container_id;
            break;

          case \Config::get('scwms.CONTAINERS.LOCATION'):
            $location = SLocation::find($limit->container_id);
            $limit->aux_branch_id = $location->warehouse->branch_id;
            $limit->aux_whs_id = $location->whs_id;
            $limit->aux_location_id = $limit->container_id;
            break;

          default:
            # code...
            break;
        }

        $lItems = SItem::where('is_deleted', false)->orderBy('name', 'ASC')
                          ->select(\DB::raw("CONCAT(code, '-', name) AS item_option, id_item"))
                          ->lists('item_option', 'id_item');
                          $lBranches = SBranch::where('is_deleted', false)
                                                ->where('partner_id',session('partner')->id_partner)
                                                ->orderBy('name', 'ASC')->lists('name', 'id_branch');
        $lWarehouses = SWarehouse::where('is_deleted', false)->get();
        $lLocations = SLocation::where('is_deleted', false)->get();

        return view('wms.limits.createEdit')
                    ->with('limit', $limit)
                    ->with('items', $lItems)
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
    public function update(SLimitRequest $request, $id)
    {
        $limit = SLimit::find($id);
        $limit->fill($request->all());

        if ($limit->aux_branch_id == '')
        {
            $limit->container_type_id = \Config::get('scwms.CONTAINERS.COMPANY');
            $limit->container_id = session('partner')->id_partner;
        }
        elseif ($limit->aux_whs_id == '0') {
            $limit->container_type_id = \Config::get('scwms.CONTAINERS.BRANCH');
            $limit->container_id = $limit->aux_branch_id;
        }
        elseif ($limit->aux_location_id == '0') {
            $limit->container_type_id = \Config::get('scwms.CONTAINERS.WAREHOUSE');
            $limit->container_id = $limit->aux_whs_id;
        }
        else {
            $limit->container_type_id = \Config::get('scwms.CONTAINERS.LOCATION');
            $limit->container_id = $limit->aux_location_id;
        }

        $limit->updated_by_id = \Auth::user()->id;

        // $aErrors = SWmsValidations::validateLimits($limit, true);
        //
        // if(sizeof($aErrors) > 0)
        // {
        //     return redirect()->back()->withErrors($aErrors)->withInput();
        // }

        unset($limit->aux_branch_id);
        unset($limit->aux_whs_id);
        unset($limit->aux_location_id);

        $limit->save();

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('wms.limits.index');
    }

    public function activate(Request $request, $id)
    {
        $limit = SLimit::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $limit);

        $limit->fill($request->all());
        $limit->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $limit->updated_by_id = \Auth::user()->id;

        $limit->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('wms.limits.index');
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

        $limit = SLimit::find($id);
        $limit->fill($request->all());
        $limit->is_deleted = \Config::get('scsys.STATUS.DEL');
        $limit->updated_by_id = \Auth::user()->id;

        $limit->save();
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();
        return redirect()->route('wms.limits.index');
    }


}
