<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\WMS\SItemContainerRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\ERP\SItemLinkType;
use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\ERP\SItemFamily;
use App\ERP\SItemGroup;
use App\ERP\SItemGender;
use App\ERP\SItem;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\WMS\SWhsType;
use App\WMS\SItemContainer;
use App\WMS\SWmsValidations;
use App\SUtils\SProcess;

class SItemContainersController extends Controller
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

      $lItemContainers = SItemContainer::Search($request->name, $this->iFilter)->orderBy('container_type_id', 'ASC')->paginate(20);

      // dd($lLimits);

      return view('wms.itemcontainers.index')
                  ->with('itemcontainers', $lItemContainers)
                  ->with('actualUserPermission', $this->oCurrentUserPermission)
                  ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new itemcontainer.
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

        $lBranches = SBranch::where('is_deleted', false)
                                ->where('partner_id', session('partner')->id_partner)
                                ->orderBy('name', 'ASC')
                                ->lists('name', 'id_branch');
        $lWarehouses = SWarehouse::where('is_deleted', false)
                                ->get();
        $lLocations = SLocation::where('is_deleted', false)->get();

        return view('wms.itemcontainers.createEdit')
                      ->with('links', $lLinkTypes)
                      ->with('items', $lItems)
                      ->with('genders', $lGenders)
                      ->with('groups', $lGroups)
                      ->with('families', $lFamilies)
                      ->with('itemTypes', $lItemTypes)
                      ->with('itemClasses', $lItemClass)
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
    public function store(SItemContainerRequest $request)
    {
      $itemcontainer = new SItemContainer($request->all());
      // dd($itemcontainer);
      if ($itemcontainer->aux_branch_id == '')
      {
          $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.COMPANY');
          $itemcontainer->container_id = session('partner')->id_partner;
      }
      elseif ($itemcontainer->aux_whs_id == '0') {
          $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.BRANCH');
          $itemcontainer->container_id = $itemcontainer->aux_branch_id;
      }
      elseif ($itemcontainer->aux_location_id == '0') {
          $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.WAREHOUSE');
          $itemcontainer->container_id = $itemcontainer->aux_whs_id;
      }
      else {
          $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.LOCATION');
          $itemcontainer->container_id = $itemcontainer->aux_location_id;
      }

      $itemcontainer->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $itemcontainer->updated_by_id = \Auth::user()->id;
      $itemcontainer->created_by_id = \Auth::user()->id;

      // $aErrors = SWmsValidations::validateLimits($itemcontainer);
      // if(sizeof($aErrors) > 0)
      // {
      //     return redirect()->back()->withErrors($aErrors)->withInput();
      // }

      unset($itemcontainer->aux_branch_id);
      unset($itemcontainer->aux_whs_id);
      unset($itemcontainer->aux_location_id);

      $itemcontainer->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('wms.itemcontainers.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $itemcontainer = SItemContainer::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $itemcontainer);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($itemcontainer);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $itemcontainer->aux_branch_id = '';
        $itemcontainer->aux_whs_id = '';
        $itemcontainer->aux_location_id = '';

        switch ($itemcontainer->container_type_id) {
          case \Config::get('scwms.CONTAINERS.COMPANY'):
            break;

          case \Config::get('scwms.CONTAINERS.BRANCH'):
            $itemcontainer->aux_branch_id = $itemcontainer->container_id;
            break;

          case \Config::get('scwms.CONTAINERS.WAREHOUSE'):
            $itemcontainer->aux_branch_id = SWarehouse::find($itemcontainer->container_id)->branch->id_branch;
            $itemcontainer->aux_whs_id = $itemcontainer->container_id;
            break;

          case \Config::get('scwms.CONTAINERS.LOCATION'):
            $location = SLocation::find($itemcontainer->container_id);
            $itemcontainer->aux_branch_id = $location->warehouse->branch_id;
            $itemcontainer->aux_whs_id = $location->whs_id;
            $itemcontainer->aux_location_id = $itemcontainer->container_id;
            break;

          default:
            # code...
            break;
        }

        $lLinkTypes = SItemLinkType::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_link_type');

        $lItems = SItem::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lGenders = SItemGender::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lGroups = SItemGroup::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lFamilies = SItemFamily::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemTypes = SItemType::where('is_deleted', false)->orderBy('name', 'ASC')->get();
        $lItemClass = SItemClass::where('is_deleted', false)->orderBy('name', 'ASC')->get();

        $lBranches = SBranch::where('is_deleted', false)
                             ->where('partner_id', session('partner')->id_partner)
                             ->orderBy('name', 'ASC')
                             ->lists('name', 'id_branch');
        $lWarehouses = SWarehouse::where('is_deleted', false)->get();
        $lLocations = SLocation::where('is_deleted', false)->get();

        return view('wms.itemcontainers.createEdit')
                    ->with('itemcontainer', $itemcontainer)
                    ->with('links', $lLinkTypes)
                    ->with('items', $lItems)
                    ->with('genders', $lGenders)
                    ->with('groups', $lGroups)
                    ->with('families', $lFamilies)
                    ->with('itemTypes', $lItemTypes)
                    ->with('itemClasses', $lItemClass)
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
    public function update(SItemContainerRequest $request, $id)
    {
        $itemcontainer = SItemContainer::find($id);
        $itemcontainer->fill($request->all());

        if ($itemcontainer->aux_branch_id == '')
        {
            $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.COMPANY');
            $itemcontainer->container_id = session('partner')->id_partner;
        }
        elseif ($itemcontainer->aux_whs_id == '0') {
            $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.BRANCH');
            $itemcontainer->container_id = $itemcontainer->aux_branch_id;
        }
        elseif ($itemcontainer->aux_location_id == '0') {
            $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.WAREHOUSE');
            $itemcontainer->container_id = $itemcontainer->aux_whs_id;
        }
        else {
            $itemcontainer->container_type_id = \Config::get('scwms.CONTAINERS.LOCATION');
            $itemcontainer->container_id = $itemcontainer->aux_location_id;
        }

        $itemcontainer->updated_by_id = \Auth::user()->id;

        unset($itemcontainer->aux_branch_id);
        unset($itemcontainer->aux_whs_id);
        unset($itemcontainer->aux_location_id);

        $errors = $itemcontainer->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('wms.itemcontainers.index');
    }

    public function activate(Request $request, $id)
    {
        $itemcontainer = SItemContainer::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $itemcontainer);

        $itemcontainer->fill($request->all());
        $itemcontainer->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $itemcontainer->updated_by_id = \Auth::user()->id;

        $itemcontainer->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('wms.itemcontainers.index');
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

        $itemcontainer = SItemContainer::find($id);
        $itemcontainer->fill($request->all());
        $itemcontainer->is_deleted = \Config::get('scsys.STATUS.DEL');
        $itemcontainer->updated_by_id = \Auth::user()->id;

        $itemcontainer->save();
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('wms.itemcontainers.index');
    }


}
