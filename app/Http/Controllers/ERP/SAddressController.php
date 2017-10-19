<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SAddress;
use App\ERP\SCountry;
use App\ERP\SState;
use App\SUtils\SProcess;

class SAddressController extends Controller
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
    public function index(Request $request, $iBranchId = NULL)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        session(['branchIdAux' => $iBranchId]);

        $lAddress = SAddress::Search($request->name, $this->iFilter, $iBranchId)->orderBy('name', 'ASC')->paginate(20);

        return view('siie.address.index')
            ->with('address', $lAddress)
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

        $lCountries = SCountry::where('is_deleted', '=', false)->orderBy('name', 'ASC')->lists('name', 'id_country');

        return view('siie.address.createEdit')
                        ->with('countries', $lCountries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $domicile = new SAddress($request->all());

      $domicile->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $domicile->branch_id = session('branchIdAux');
      $domicile->updated_by_id = \Auth::user()->id;
      $domicile->created_by_id = \Auth::user()->id;

      $domicile->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('siie.address.index');
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
        $domicile = SAddress::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $domicile->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $lCountries = SCountry::where('is_deleted', '=', false)->orderBy('name', 'ASC')->lists('name', 'id_country');
        $lStates = SState::where('is_deleted', '=', false)->where('country_id', $domicile->country_id)->orderBy('name', 'ASC')->get();

        return view('siie.address.createEdit')->with('domicile', $domicile)
                                              ->with('states', $lStates)
                                              ->with('countries', $lCountries);
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
        $domicile = SAddress::find($id);
        $domicile->fill($request->all());
        $domicile->updated_by_id = \Auth::user()->id;
        $domicile->save();

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('siie.address.index');
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

        $domicile = SAddress::find($id);

        $domicileCopy = clone $domicile;
        $domicileCopy->id_item_family = 0;

        return view('siie.address.createEdit')->with('family', $domicileCopy)
                                              ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $domicile = SAddress::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $domicile->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $domicile->fill($request->all());
        $domicile->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $domicile->updated_by_id = \Auth::user()->id;

        $domicile->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('siie.address.index');
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

        $domicile = SAddress::find($id);
        $domicile->fill($request->all());
        $domicile->is_deleted = \Config::get('scsys.STATUS.DEL');
        $domicile->updated_by_id = \Auth::user()->id;

        $domicile->save();
        #$user->delete();

        Flash::error(trans('messages.REG_DELETED'))->important();

        return redirect()->route('siie.address.index');
    }

    public function children(Request $request)
    {
    	 return SState::where('country_id', '=', $request->parent)->get();
    }

}
