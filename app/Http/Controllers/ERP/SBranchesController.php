<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ERP\SBranch;
use App\ERP\SPartner;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;

class SBranchesController extends Controller {

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
      $lBranches = SBranch::Search($request->name, $this->iFilter)->orderBy('code', 'ASC')->paginate(10);

      $lBranches->each(function($lBranches) {
        $lBranches->company;
      });

      return view('siie.branches.index')
          ->with('branches', $lBranches)
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
      if (SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          $companies = SPartner::orderBy('name', 'ASC')->lists('name', 'id_partner');

          return view('siie.branches.createEdit')
                        ->with('companies', $companies);
        }
        else
        {
           return redirect()->route('notauthorized');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = SPartner::find(1);

        $branch = new SBranch($request->all());

        $branch->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $branch->partner_id = $company->id_partner;
        $branch->updated_by_id = \Auth::user()->id;
        $branch->created_by_id = \Auth::user()->id;

        $branch->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('siie.branches.index');
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
        $branch = SBranch::find($id);

        return view('siie.branches.createEdit')->with('branch', $branch)
                                      ->with('iFilter', $this->iFilter);
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
         $branch = SBranch::find($id);
         $branch->fill($request->all());
         $branch->updated_by_id = \Auth::user()->id;
         $branch->save();

         Flash::warning(trans('messages.REG_EDITED'))->important();

         return redirect()->route('siie.branches.index');
     }


     public function activate(Request $request, $id)
     {
         $branch = SBranch::find($id);

         $branch->fill($request->all());
         $branch->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
         $branch->updated_by_id = \Auth::user()->id;

         $branch->save();

         Flash::success(trans('messages.REG_ACTIVATED'))->important();

         return redirect()->route('siie.branches.index');
     }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy(Request $request, $id)
     {
         $branch = SBranch::find($id);
         $branch->fill($request->all());
         $branch->is_deleted = \Config::get('scsys.STATUS.DEL');
         $branch->updated_by_id = \Auth::user()->id;

         $branch->save();
         #$user->delete();

         Flash::error(trans('messages.REG_DELETED'))->important();

         return redirect()->route('siie.branches.index');
     }
}
