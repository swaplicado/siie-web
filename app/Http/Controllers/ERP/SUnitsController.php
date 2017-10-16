<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SUnit;
use App\SUtils\SProcess;

class SUnitsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ITEM_CONFIG'), \Config::get('scsys.MODULES.ERP'));

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

      $lUnits = SUnit::Search($request->name, $this->iFilter)->orderBy('name', 'ASC')->paginate(20);
      $lUnits->each(function($lUnits) {
        $lUnits->equivalence;
      });

      return view('siie.units.index')
          ->with('units', $lUnits)
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

        $unitsEq = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

        return view('siie.units.createEdit')
                          ->with('unitseq', $unitsEq);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $unit = new SUnit($request->all());

      $unit->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $unit->updated_by_id = \Auth::user()->id;
      $unit->created_by_id = \Auth::user()->id;

      $unit->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('siie.units.index');
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
        $unit = SUnit::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $unit->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $unitsEq = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

        return view('siie.units.createEdit')->with('unit', $unit)
                                        ->with('unitseq', $unitsEq);
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
        $unit = SUnit::find($id);
        $unit->fill($request->all());
        $unit->updated_by_id = \Auth::user()->id;
        $unit->save();

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('siie.units.index');
    }

    /**
     * Inactive the registry setting the flag is_deleted to true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function copy(Request $request, $id)
    {
        $unit = SUnit::find($id);

        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $unitCopy = clone $unit;
        $unitCopy->id_bp = 0;
        $unitsEq = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

        return view('siie.units.createEdit')->with('unit', $unitCopy)
                                        ->with('unitseq', $unitsEq)
                                      ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $unit = SUnit::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $unit->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $unit->fill($request->all());
        $unit->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $unit->updated_by_id = \Auth::user()->id;

        $unit->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('siie.units.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (! SValidation::canDestroy($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }
        
        $unit = SUnit::find($id);
        $unit->fill($request->all());
        $unit->is_deleted = \Config::get('scsys.STATUS.DEL');
        $unit->updated_by_id = \Auth::user()->id;

        $unit->save();
        #$user->delete();

        Flash::error(trans('messages.REG_DELETED'))->important();
        return redirect()->route('siie.units.index');
    }
}
