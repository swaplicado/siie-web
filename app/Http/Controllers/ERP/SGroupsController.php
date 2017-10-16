<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\ERP\SGroupRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SItemFamily;
use App\ERP\SItemGroup;
use App\SUtils\SProcess;

class SGroupsController extends Controller
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

      $lGroups = SItemGroup::Search($request->name, $this->iFilter)->orderBy('name', 'ASC')->paginate(20);

      foreach ($lGroups as $group) {
        $group->family;
      }

      return view('siie.groups.index')
          ->with('groups', $lGroups)
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

        $lFamilies = SItemFamily::orderBy('name', 'ASC')->lists('name', 'id_item_family');

        return view('siie.groups.createEdit')
                    ->with('families', $lFamilies);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SGroupRequest $request)
    {
      $group = new SItemGroup($request->all());

      $group->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $group->updated_by_id = \Auth::user()->id;
      $group->created_by_id = \Auth::user()->id;

      $group->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('siie.groups.index');
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
        $group = SItemGroup::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $group->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $lFamilies = SItemFamily::orderBy('name', 'ASC')->lists('name', 'id_item_family');

        return view('siie.groups.createEdit')
                              ->with('families', $lFamilies)
                                ->with('group', $group);

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
        $group = SItemGroup::find($id);
        $group->fill($request->all());
        $group->updated_by_id = \Auth::user()->id;
        $group->save();

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('siie.groups.index');
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

        $group = SItemGroup::find($id);

        $groupCopy = clone $group;
        $groupCopy->id_group = 0;

        return view('siie.groups.createEdit')->with('group', $groupCopy)
                                              ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $group = SItemGroup::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $group->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        $group->fill($request->all());
        $group->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $group->updated_by_id = \Auth::user()->id;

        $group->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('siie.groups.index');
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

      $group = SItemGroup::find($id);
      $group->fill($request->all());
      $group->is_deleted = \Config::get('scsys.STATUS.DEL');
      $group->updated_by_id = \Auth::user()->id;

      $group->save();
      #$user->delete();

      Flash::error(trans('messages.REG_DELETED'))->important();
      return redirect()->route('siie.groups.index');
    }
}
