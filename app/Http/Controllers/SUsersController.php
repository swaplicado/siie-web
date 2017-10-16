<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\SYS\SUserType;
use Laracasts\Flash\Flash;
use App\Http\Requests\UserRequest;
use App\Http\Requests\SPasswordRequest;
use App\SUtils\SValidation;
use App\SUtils\SUtil;

class SUsersController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ADMINISTRATOR'), \Config::get('scsys.MODULES.ERP'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $users = User::Search($request->name, $this->iFilter)->orderBy('username', 'ASC')->paginate(10);

        return view('admin.users.index')
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
        if (SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
          {
            $types = SUserType::orderBy('name', 'ASC')->lists('name', 'id_type');

            return view('admin.users.createEdit')->with('types', $types);
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
    public function store(UserRequest $request)
    {
        $user = new User($request->all());
        $user->password = bcrypt($request->password);
        $user->created_by_id =\Auth::user()->id;
        $user->updated_by_id =\Auth::user()->id;

        $user->save();
        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('admin.users.index');
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
      $user = User::find($id);

      if (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $user->created_by_id))
      {
          $types = SUserType::orderBy('name', 'ASC')->lists('name', 'id_type');
          return view('admin.users.createEdit')->with('user', $user)
                                          ->with('iFilter', $this->iFilter)
                                          ->with('types', $types);
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
        $user = User::find($id);
        $user->fill($request->all());
        $user->updated_by_id = \Auth::user()->id;
        $user->save();

        Flash::warning(trans('messages.REG_EDITED'))->important();
        return redirect()->route('admin.users.index');
    }

    /**
     * Inactive the registry setting the flag is_deleted to true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function activate(Request $request, $id)
    {
      $user = User::find($id);

      $user->fill($request->all());
      $user->is_deleted = \Config::get('scsys.STATUS.ACTIVE');

      $user->save();

      Flash::success(trans('messages.REG_ACTIVATED'))->important();

      return redirect()->route('admin.users.index');
    }

    /**
     * Updates the user's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function updatePass(SPasswordRequest $request, $id)
    {
       $user = User::find($id);
       $request_data = $request->All();

       if(password_verify($request_data['current_password'], $user->password))
       {
         $user->password = bcrypt($request_data['password']);
         $user->updated_by_id = \Auth::user()->id;
         $user->save();

         Flash::success(trans('messages.PASS_CHANGED'))->important();

         return redirect()->route('admin.users.index');
       }
       else
       {
         $error = array(trans('messages.PASS_CURRENT') => trans('messages.PASS_ERROR'));

         return redirect()->back()->withErrors($error)->withInput();
       }
    }

    /**
     * Inactive the registry setting the flag is_deleted to true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function copy(Request $request, $id)
    {
        $user = User::find($id);

        $userCopy = clone $user;
        $userCopy->id = 0;
        $types = SUserType::orderBy('name', 'ASC')->lists('name', 'id_type');

        return view('admin.users.createEdit')->with('user', $userCopy)
                                      ->with('iFilter', $this->iFilter)
                                      ->with('bIsCopy', true)
                                      ->with('types', $types);
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
        $user = User::find($id);
        $user->fill($request->all());
        $user->is_deleted = \Config::get('scsys.STATUS.DEL');
        $user->updated_by_id = \Auth::user()->id;

        $user->save();
        #$user->delete();

        Flash::error(trans('messages.REG_DELETED'))->important();
        return redirect()->route('admin.users.index');
      }
      else
      {
        return redirect()->route('notauthorized');
      }
    }

    /**
     * change the password of user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePass(Request $request, $id)
    {
        $user = User::find($id);

        return view('admin.users.changepass')->with('user', $user);
    }
}
