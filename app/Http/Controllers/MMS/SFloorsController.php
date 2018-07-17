<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\SUtils\SProcess;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\MMS\SFloor;
use App\WMS\SWarehouse;



class SFloorsController extends Controller {

  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;

  public function __construct()
  {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.MANUFACTURING'), \Config::get('scsys.MODULES.MMS'));

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
        $Floors = SFloor::Search($request->name, $this->iFilter)->orderBy('id_floor', 'ASC')->paginate(10);

        $Floors->each(function($Floors) {
          $Floors->branch;
        });

        return view('mms.floors.index')
            ->with('floors', $Floors)
            ->with('actualUserPermission', $this->oCurrentUserPermission)
            ->with('iFilter', $this->iFilter);
    }

    public function create()
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        // $lBranches = SUtil::companyBranchesArray();
        $branch = SBranch::where('partner_id', session('partner')->id_partner)
                    ->where('is_deleted', false)
                    ->orderBy('name', 'ASC')
                    ->lists('name', 'id_branch');
        return view('mms.floors.createEdit')
                      ->with('branches', $branch);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $floors = new SFloor($request->all());

        $floors->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $floors->updated_by_id = \Auth::user()->id;
        $floors->created_by_id = \Auth::user()->id;

        $iValidCode = SFloor::where('code', $floors->code)
                                  ->where('branch_id', $floors->branch_id)->get();

        if (sizeof($iValidCode) > 0) {
           return redirect()->back()->withInput(
                  $request->input())
                  ->withErrors(['Ya hay un planta con este código en la sucursal']);
        }

        $floors->save();



        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('mms.floors.index');
    }

    public function edit($id)
    {
        $floor = SFloor::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $floor);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($floor);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }
        $branch = SBranch::where('partner_id', session('partner')->id_partner)
                    ->where('is_deleted', false)
                    ->orderBy('name', 'ASC')
                    ->lists('name', 'id_branch');

        return view('mms.floors.createEdit')
                    ->with('floors', $floor)
                    ->with('branches', $branch);
    }

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

    public function copy(Request $request, $id)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $floors = SFloor::find($id);

        $floorsCopy = clone $floors;
        $floorsCopy->id_floor = 0;



        return view('mms.floors.createEdit')->with('floors', $floorsCopy)
                                        ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $floors = SFloor::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $floors);

        $floors->fill($request->all());
        $floors->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $floors->updated_by_id = \Auth::user()->id;

        $errors = $floors->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('mms.floors.index');
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

        $floors = SFloor::find($id);
        $floors->fill($request->all());
        $floors->is_deleted = \Config::get('scsys.STATUS.DEL');
        $floors->updated_by_id = \Auth::user()->id;

        $errors = $floors->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('mms.floors.index');
    }
}
