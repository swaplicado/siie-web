<?php

namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\ERP\SBranch;
use App\ERP\SUserBranch;
use Laracasts\Flash\Flash;
use App\SYS\SUserCompany;
use App\User;
use App\SYS\SCompany;
use App\SUtils\SProcess;


class SUserBranchesController extends Controller
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

      $branches = SBranch::orderBy('name', 'ASC')->where('partner_id',session('partner')->id_partner)->get();
      $users = SUserCompany::orderBy('user_id','ASC')->where('company_id',session('company')->id_company)->get();
      $users->each(function($users){
            $users->user;
            $users->company;
            $users->user->userBranches;
        });

      return view('siie.userBranches.index')
          ->with('branches', $branches)
          ->with('users', $users)
          ->with('actualUserPermission', $this->oCurrentUserPermission)
          ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $user = User::find($id);
      $branches = SBranch::orderBy('name', 'ASC')->where('partner_id',session('partner')->id_partner)->get();

      return view('siie.userBranches.createEdit')
                                          ->with('user', $user)
                                          ->with('branches', $branches);
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
        $request_data = $request->All();

          $branches = SBranch::orderBy('name', 'ASC')->get();

          SUserBranch::where('user_id', $id)->delete();

          foreach ($branches as $branch) {
            if (isset($request_data[$branch->id_branch]))
            {
              $userBranch = new SUserBranch();
              $userBranch->user_id = $id;
              $userBranch->branch_id = $branch->id_branch;
              $userBranch->is_universal = 0;
              $userBranch->save();
            }


        }

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('siie.userBranches.index');
    }

}
