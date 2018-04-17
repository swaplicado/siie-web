<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Requests\ERP\SBpRequest;
use App\Http\Controllers\Controller;
use App\ERP\SPartner;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;

class SPartnersController extends Controller {

    private $oCurrentUserPermission;
    private $iFilter;
    private $iFilterBp;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CENTRAL_CONFIG'), \Config::get('scsys.MODULES.ERP'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
        $this->iFilterBp = \Config::get('scsiie.ATT.ALL');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
      $this->iFilterBp = $request->filterBp == null ? \Config::get('scsiie.ATT.ALL') : $request->filterBp;

      $lBPartners = SPartner::Search($request->name, $this->iFilter, $this->iFilterBp)->orderBy('name', 'ASC');

      return view('siie.bps.index')
          ->with('bps', $lBPartners)
          ->with('actualUserPermission', $this->oCurrentUserPermission)
          ->with('iFilter', $this->iFilter)
          ->with('iFilterBp', $this->iFilterBp);
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

        return view('siie.bps.createEdit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SBpRequest $request)
    {
        $bpartner = new SPartner($request->all());

        $bpartner->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $bpartner->updated_by_id = \Auth::user()->id;
        $bpartner->created_by_id = \Auth::user()->id;

        $bpartner->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('siie.bps.index');
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
        $bpartner = SPartner::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $bpartner->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        return view('siie.bps.createEdit')->with('bpartner', $bpartner)
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
         $bpartner = SPartner::find($id);
         $bpartner->fill($request->all());
         $bpartner->updated_by_id = \Auth::user()->id;
         $bpartner->save();

         Flash::success(trans('messages.REG_EDITED'))->important();

         return redirect()->route('siie.bps.index');
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

         $bpartner = SPartner::find($id);

         $bpartnerCopy = clone $bpartner;
         $bpartnerCopy->id_bp = 0;

         return view('siie.bps.createEdit')->with('bpartner', $bpartnerCopy)
                                       ->with('bIsCopy', true);
     }


     public function activate(Request $request, $id)
     {
         $bpartner = SPartner::find($id);

         if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $bpartner->created_by_id)))
         {
           return redirect()->route('notauthorized');
         }

         $bpartner->fill($request->all());
         $bpartner->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
         $bpartner->updated_by_id = \Auth::user()->id;

         $bpartner->save();

         Flash::success(trans('messages.REG_ACTIVATED'))->important();

         return redirect()->route('siie.bps.index');
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

         $bpartner = SPartner::find($id);
         $bpartner->fill($request->all());
         $bpartner->is_deleted = \Config::get('scsys.STATUS.DEL');
         $bpartner->updated_by_id = \Auth::user()->id;

         $bpartner->save();

         Flash::success(trans('messages.REG_DELETED'))->important();

         return redirect()->route('siie.bps.index');
     }
}
