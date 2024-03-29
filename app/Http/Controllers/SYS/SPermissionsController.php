<?php namespace App\Http\Controllers\SYS;

use Illuminate\Http\Request;
use App\SYS\SPermission;
use App\SYS\SPermissionType;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SValidation;
use App\SUtils\SUtil;

class SPermissionsController extends Controller
{
      private $oCurrentUserPermission;
      private $iFilter;

      public function __construct()
      {
          $this->middleware('mdadmin');
          $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
          $this->oCurrentUserPermission = NULL;
      }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $permissions = SPermission::Search($request->name, $this->iFilter)->orderBy('name', 'ASC')->paginate(10);

        return view('admin.permissions.index')
                    ->with('permissions', $permissions)
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
              $lPermissionTypes = SPermissionType::orderBy('name', 'ASC')->lists('name', 'id_permission_type');

              return view('admin.permissions.createEdit')->with('types', $lPermissionTypes);
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
        $permission = new SPermission($request->all());
        $permission->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('admin.permissions.index');
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
        $permission = SPermission::find($id);
        $lPermissionTypes = SPermissionType::orderBy('name', 'ASC')->lists('name', 'id_permission_type');

        if (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $permission->created_by_id))
        {
          return view('admin.permissions.createEdit')->with('permission', $permission)
                                               ->with('types', $lPermissionTypes)
                                                ->with('iFilter', $this->iFilter);
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
        $permission = SPermission::find($id);
        $permission->fill($request->all());
        $permission->save();

        Flash::success(trans('messages.REG_EDITED'))->important();
        return redirect()->route('admin.permissions.index');
    }

    public function activate(Request $request, $id)
    {
        $permission = SPermission::find($id);
        $permission->fill($request->all());
        $permission->is_deleted = \Config::get('scsys.STATUS.ACTIVE');

        $permission->save();

        Flash::success("Se ha activado de forma exitosa!");

        return redirect()->route('admin.permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
      if (SValidation::canDestroy($this->oCurrentUserPermission->privilege_id))
        {
          $permission = SPermission::find($id);

          $permission->fill($request->all());
          $permission->is_deleted = \Config::get('scsys.STATUS.DEL');

          $permission->save();
          #$permission->delete();
          Flash::success(trans('messages.REG_DELETED'))->important();

          return redirect()->route('admin.permissions.index');
        }
        else
        {
          return redirect()->route('notauthorized');
        }
    }
}
