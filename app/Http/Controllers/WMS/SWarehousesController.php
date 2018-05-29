<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\WMS\SWhsRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\ERP\SBranch;
use App\WMS\SWhsType;
use App\SUtils\SProcess;

class SWarehousesController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CONTAINERS'), \Config::get('scsys.MODULES.WMS'));

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

      $lWarehouses = SWarehouse::Search($request->name, $this->iFilter)->orderBy('name', 'ASC')->paginate(20);
      $lWarehouses->each(function($lWarehouses) {
        $lWarehouses->branch;
      });

      return view('wms.whs.index')
          ->with('warehouses', $lWarehouses)
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

        $lTypes = SWhsType::orderBy('name', 'ASC')->lists('name', 'id_whs_type');
        // $lBranches = SUtil::companyBranchesArray();
        $branch = SBranch::where('partner_id', session('partner')->id_partner)
                    ->where('is_deleted', false)
                    ->orderBy('name', 'ASC')
                    ->lists('name', 'id_branch');
        return view('wms.whs.createEdit')
                      ->with('branches', $branch)
                      ->with('types', $lTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SWhsRequest $request)
    {
        $whs = new SWarehouse($request->all());

        $whs->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $whs->updated_by_id = \Auth::user()->id;
        $whs->created_by_id = \Auth::user()->id;

        $iValidCode = SWarehouse::where('code', $whs->code)
                                  ->where('branch_id', $whs->branch_id)->get();

        if (sizeof($iValidCode) > 0) {
           return redirect()->back()->withInput(
                  $request->input())
                  ->withErrors(['Ya hay un almacén con este código en la sucursal']);
        }

        $whs->save();

        $location = new SLocation();
        $location->code = trans('wms.DEFAULT_CODE').$whs->id_whs;
        $location->name = trans('wms.DEFAULT');
        $location->is_deleted = false;
        $location->is_default = true;
        $location->whs_id = $whs->id_whs;
        $location->created_by_id = 1;
        $location->updated_by_id = 1;
        $location->is_recondition = 0;
        $location->is_reprocess = 0;
        $location->is_destruction = 0;

        $location->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('wms.whs.index');
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
        $whs = SWarehouse::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $whs);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($whs);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $lTypes = SWhsType::orderBy('name', 'ASC')->lists('name', 'id_whs_type');
        $lBranches = SUtil::companyBranchesArray();

        return view('wms.whs.createEdit')
                    ->with('whs', $whs)
                    ->with('branches', $lBranches)
                    ->with('types', $lTypes);
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
        $whs = SWarehouse::find($id);
        $whs->fill($request->all());
        $whs->updated_by_id = \Auth::user()->id;

        $iValidCode = SWarehouse::where('code', $whs->code)
                                  ->where('branch_id', $whs->branch_id)
                                  ->where('id_whs', '!=', $whs->id_whs)->get();

        if (sizeof($iValidCode) > 0) {
           return redirect()->back()->withInput(
                  $request->input())
                  ->withErrors(['Ya hay un almacén con este código en la sucursal']);
        }

        $errors = $whs->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('wms.whs.index');
    }

    /**
     * Inactive the registry setting the flag is_deleted to true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function copy(Request $request, $id)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $whs = SWarehouse::find($id);

        $whsCopy = clone $whs;
        $whsCopy->id_whs = 0;

        $lTypes = SWhsType::orderBy('name', 'ASC')->lists('name', 'id_whs_type');
        $lBranches = SUtil::companyBranchesArray();

        return view('wms.whs.createEdit')->with('whs', $whsCopy)
                                        ->with('branches', $lBranches)
                                        ->with('types', $lTypes)
                                        ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $whs = SWarehouse::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $whs);

        $whs->fill($request->all());
        $whs->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $whs->updated_by_id = \Auth::user()->id;

        $errors = $whs->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('wms.whs.index');
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

        $whs = SWarehouse::find($id);
        $whs->fill($request->all());
        $whs->is_deleted = \Config::get('scsys.STATUS.DEL');
        $whs->updated_by_id = \Auth::user()->id;

        $errors = $whs->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('wms.whs.index');
    }
}
