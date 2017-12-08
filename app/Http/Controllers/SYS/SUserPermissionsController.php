w<?php

namespace App\Http\Controllers\SYS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\SUtils\SProcess;
use App\WMS\SWmsLot;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\SYS\SPermissionType;
use App\SYS\SModule;
use App\SYS\SPermission;
use App\SYS\SPrivilege;
use App\SYS\SCompany;
use App\SYS\SUserPermission;
use App\SYS\SUserCompany;
use App\User;

class SUserPermissionsController extends Controller
{
  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;

  public function __construct()
  {

       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CENTRAL_CONFIG'), \Config::get('scsys.MODULES.ERP'));

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
        $users = User::Search($request->username, $this->iFilter)->orderBy('id', 'ASC')->paginate(10);

        return view('userpermissions.index')
        ->with('actualUserPermission', $this->oCurrentUserPermission)
        ->with('users', $users)
        ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function creator($id)
    {

      $Syspermission = SPermissionType::orderBy('name', 'ASC')->lists('name', 'id_permission_type');
      $Module = SModule::orderBy('name', 'ASC')->lists('name', 'id_module');
      $permission = SPermission::orderBy('name', 'ASC')->lists('name', 'id_permission');
      $Privilege = SPrivilege::orderBy('name', 'ASC')->lists('name', 'id_privilege');

      $id_id = preg_split("/[,,]+/", $id);
      $nameUser = $id_id[0];
      $idUser = $id_id[1];
      $Companies = SUserCompany::select('id_user_access','company_id')
                  ->where('user_id',$idUser)
                  ->get();

      $Companies->each(function($Companies){
                  $Companies->company;
      });



      return view('userpermissions.createEdit')
        ->with('actualUserPermission', $this->oCurrentUserPermission)
        ->with('syspermissions', $Syspermission)
        ->with('modules', $Module)
        ->with('permissions', $permission)
        ->with('companies', $Companies)
        ->with('privileges', $Privilege)
        ->with('selectedNameId', $nameUser)
        ->with('selectedUserId', $idUser);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function brWhsCreate2()
    {
      $id="Admin,,2";
      $Syspermission = SPermissionType::orderBy('name', 'ASC')->lists('name', 'id_permission_type');
      $Module = SModule::orderBy('name', 'ASC')->lists('name', 'id_module');
      $permission = SPermission::orderBy('name', 'ASC')->lists('name', 'id_permission');
      $Privilege = SPrivilege::orderBy('name', 'ASC')->lists('name', 'id_privilege');

      $id_id = preg_split("/[,,]+/", $id);
      $nameUser = $id_id[0];
      $idUser = $id_id[1];
      $Companies = SUserCompany::select('id_user_access','company_id')
                  ->where('user_id',$idUser)
                  ->get();

      $Companies->each(function($Companies){
                  $Companies->company;
      });



      return view('userpermissions.brWhsCreate')
        ->with('actualUserPermission', $this->oCurrentUserPermission)
        ->with('syspermissions', $Syspermission)
        ->with('modules', $Module)
        ->with('permissions', $permission)
        ->with('companies', $Companies)
        ->with('privileges', $Privilege)
        ->with('selectedNameId', $nameUser)
        ->with('selectedUserId', $idUser);

    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function brWhsCreate(Request $request)
    {
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

      $branches = SBranch::orderBy('name', 'ASC')->where('partner_id',session('partner')->id_partner)->get();
      $users = SUserCompany::orderBy('user_id','ASC')->where('company_id',session('company')->id_company)->get();
      $users->each(function($users){
            $users->user;
            $users->company;
            $users->user->userBranches;
        });

      return view('userpermissions.brWhsCreate')
          ->with('branches', $branches)
          ->with('users', $users)
          ->with('actualUserPermission', $this->oCurrentUserPermission)
          ->with('iFilter', $this->iFilter);
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

       return redirect()->route('admin.userpermissions.index');
     }


    /**
     * Display the userpermission.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserPermission($id)
    {
      $data = SItem::select('id_module')
                    ->join('syss_modules','syss_modules.id_module','=','erpu_items.id_item')
                    ->join('syss_permission_types','syss_permission_types.item_id','=','erpu_items.id_item')
                    ->join('syss_permissions','syss_permissions.item_id','=','erpu_items.id_item')
                    ->join('syss_privileges','syss_privileges.item_id','=','erpu_items.id_item')
                    ->get();
      return response()->json($data);
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
     * Function for find companies
     */
    public function findCompanies(){
        $data = SCompany::select('id_company', 'name')
                        ->get();
        return response()->json($data);
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
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
      // $Syspermission = SPermissionType::orderBy('name', 'ASC')->lists('name', 'id_permission_type');
      // $Module = SModule::orderBy('name', 'ASC')->lists('name', 'id_module');
      // $permission = SPermission::orderBy('name', 'ASC')->lists('name', 'id_permission');
      // $Privilege = SPrivilege::orderBy('name', 'ASC')->lists('name', 'id_privilege');
      // $Companies = SCompany::orderBy('name', 'ASC')->lists('name', 'id_company');
      //
      //   return view('userpermissions.view')
      //   ->with('actualUserPermission', $this->oCurrentUserPermission)
      //   ->with('syspermissions', $Syspermission)
      //   ->with('modules', $Module)
      //   ->with('permissions', $permission)
      //   ->with('companies', $Companies)
      //   ->with('privileges', $Privilege);
    // }

}
