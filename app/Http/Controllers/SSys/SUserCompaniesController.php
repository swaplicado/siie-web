<?php namespace App\Http\Controllers\SSYS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;

use Laracasts\Flash\Flash;
use App\SSYS\SUserCompany;
use App\User;
use App\SSYS\SCompany;

class SUserCompaniesController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->middleware('mdadmin');
       $this->middleware('mdpermission:'.\Config::get('scperm.TP_PERMISSION.VIEW').','.\Config::get('scperm.VIEW_CODE.ACCESS'));
       $this->oCurrentUserPermission = SUtil::getTheUserPermission(\Auth::user()->id, \Config::get('scperm.VIEW_CODE.ACCESS'));

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
        $companies = SCompany::orderBy('name', 'ASC')->get();
        $users = User::groupBy('username')->paginate(10);

        $users->each(function($user) {
          $user->userCompanies;
        });

        return view('admin.usrcompanies.index')
                            ->with('companies', $companies)
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

          if (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $userCompany->created_by_id))
          {
              $companies = SCompany::orderBy('name', 'ASC')->get();

              return view('admin.usrcompanies.createEdit')
                                              ->with('user', $user)
                                              ->with('companies', $companies);
          }
          else
          {
              return redirect()->route('notauthorized');
          }
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
        $companies = SCompany::orderBy('name', 'ASC')->get();

        SUserCompany::where('user_id', $id)->delete();

        foreach ($companies as $company) {
          if (isset($request_data[$company->id_company]))
          {
            $userCompany = new SUserCompany();
            $userCompany->user_id = $id;
            $userCompany->company_id = $company->id_company;
            $userCompany->created_by_id = \Auth::user()->id;
            $userCompany->save();
          }
        }

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('admin.usraccess.index');
    }
}
