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

class SPassController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
      // $this->middleware('guest');
      $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
      $this->oCurrentUserPermission = NULL;
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

         return redirect()->route('start.selmod');
       }
       else
       {
         $error = array(trans('messages.PASS_CURRENT') => trans('messages.PASS_ERROR'));

         return redirect()->back()->withErrors($error)->withInput();
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
        // dd($user);

        return view('admin.users.changepass')->with('user', $user);
    }
}
