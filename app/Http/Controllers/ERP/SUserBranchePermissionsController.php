<?php

namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\ERP\SUserBranch;
use Laracasts\Flash\Flash;
use App\SYS\SUserCompany;
use App\SYS\SModule;
use App\SYS\SPermission;
use App\SYS\SPrivilege;
use App\SYS\SUserPermission;
use App\User;
use App\SYS\SCompany;
use App\SUtils\SProcess;
use App\SUtils\SConnectionUtils;

class SUserBranchePermissionsController extends Controller
{
  private $oCurrentUserPermission;
  private $iFilter;

  public function __construct()
  {
      $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
      $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ITEM_CONFIG'), \Config::get('scsys.MODULES.ERP'));
  }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      SConnectionUtils::reconnectCompany();
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
      $branches = SBranch::orderBy('name', 'ASC')->where('partner_id',session('partner')->id_partner)->get();
      $users = SUserCompany::orderBy('user_id','ASC')->where('company_id',session('company')->id_company)->get();


      $users->each(function($users){
           $users->user;
           $users->company;
           $users->user->userBranches;
       });

     return view('siie.userbranchepermissions.index')
         ->with('branches', $branches)
         ->with('users', $users)
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
      $permission_id_array = $request->permission_id;

         for ($i=0; $i < count($permission_id_array); $i++) {

           $assignament = new SUserPermission($request->all());
           $assignament->user_id = $request->selectedUserId;
           $assignament->module_id = $request->module_id;
           $assignament->company_id_opt = $request->companies_id;
           $assignament->permission_type_id = 2;
           $assignament->permission_id = $permission_id_array[$i];

           $assignament->save();
         }

       Flash::success(trans('messages.REG_CREATED'))->important();

       return redirect()->route('siie.userbranchepermissions.index');
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
      $Module = SModule::orderBy('name', 'ASC')->lists('name', 'id_module');
      $permission = SPermission::orderBy('name', 'ASC')->lists('name', 'id_permission');
      $branch = SBranch::orderBy('name', 'ASC')->lists('name', 'id_branch');
      $user = User::find($id);
      $Privilege = SPrivilege::orderBy('name', 'ASC')->lists('name', 'id_privilege');

        return view('siie.userbranchepermissions.brWhsCreate')
              ->with('modules', $Module)
              ->with('permissions', $permission)
              ->with('branches', $branch)
              ->with('user',$user)
              ->with('privileges', $Privilege)
              ->with('id',$id);
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

    /*
     * Function for find permissions
     */
    public function findPermission(Request $request){
        $data = SPermission::select('id_permission', 'name')
                ->WHERE('module_id', $request->id)
                        ->get();
         return response()->json($data);
    }

    /*
     * Function for find branches
     */
    public function findbranches(){
        $data = SPermission::select('id_branch', 'name')
                ->WHERE('partner_id', session('partner')->id_partner)
                        ->get();
         return response()->json($data);
    }

    /*
     * Function for find whs
     */
    public function findWhs(Request $request){
         $data = SWarehouse::select('id_whs', 'name')
                ->WHERE('branch_id', $request->id)
                        ->get();
         return response()->json($data);
    }
}
