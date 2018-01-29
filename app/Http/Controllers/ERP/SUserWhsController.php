<?php

namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\ERP\SBranch;
use App\ERP\SUserBranch;
use App\WMS\SWarehouse;
use App\ERP\SUserWhs;
use Laracasts\Flash\Flash;
use App\SYS\SUserCompany;
use App\User;
use App\SYS\SCompany;
use App\SUtils\SProcess;


class SUserWhsController extends Controller
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
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;


      $users = SUserCompany::orderBy('user_id','ASC')->where('company_id',session('company')->id_company)->get();


      return view('siie.userwhs.index')
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
      $branches = SBranch::orderBy('name', 'ASC')->where('partner_id',session('partner')->id_partner)->get();
      $users = SUserCompany::orderBy('user_id','ASC')->where('company_id',session('company')->id_company)->get();


      return view('siie.userwhs.createEdit')
          ->with('branches', $branches)
          ->with('users', $users)
          ->with('actualUserPermission', $this->oCurrentUserPermission);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $data = SWarehouse::select('id_whs','name')
                          ->where('branch_id',$request->branch)
                          ->lists('id_whs');
      for($i=0; $i<count($data) ;$i++) {
        SUserWhs::where('user_id',$request->user)->where('whs_id',$data[$i])->delete();
      }
      $whs_id_array = $request->whs;

         for ($i=0; $i < count($whs_id_array); $i++) {
           $whs = new SUserWhs();
           $whs->user_id = $request->user;
           $whs->whs_id = $whs_id_array[$i];
           $whs->save();
         }

         Flash::warning(trans('messages.REG_EDITED'))->important();

         return redirect()->route('admin.userwhs.index');

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
      $userbranches = SUserBranch::orderBy('id_access_branch','ASC')->where('user_id',$id)->get();
      $userbranches->each(function($userbranches){
            $userbranches->branch;
            $userbranches->user;
        });
      $userwhs = SUserWhs::orderBy('id_access_whs','ASC')->where('user_id',$id)->lists('whs_id');
      // $userwhs = SUserWhs::orderBy('id_access_whs', 'ASC')->where('user_id',$id)->get();

      //$users = SUserCompany::orderBy('user_id','ASC')->where('company_id',session('company')->id_company)->get();
      $users = User::find($id);
      return view('siie.userwhs.createEdit')
          ->with('branches', $userbranches)
          ->with('whs', $userwhs)
          ->with('users', $users)
          ->with('actualUserPermission', $this->oCurrentUserPermission);
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

    public function findWhs(Request $request){
        // $data = SUserWhs::select('id_access_whs','whs_id','wmsu_whs.name')
        //                 ->join('wmsu_whs','erpu_access_whs.whs_id','=','wmsu_whs.id_whs' )
        //                 ->where('wmsu_whs.branch_id',$request->user)
        //                 ->get();
        $data = SWarehouse::select('id_whs','name')
                            ->where('branch_id',$request->id)
                            ->get();

        return response()->json($data);


      }

}
