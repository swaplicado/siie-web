<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Carbon\Carbon;

use App\Http\Requests\UserRequest;
use App\Http\Requests\SPasswordRequest;
use App\Http\Requests\SPasswordSuperRequest;

use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\SUtils\SConnectionUtils;

use App\User;
use App\SYS\SUserType;
use App\ERP\SYear;

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
     * Updates the user's password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function updateSuperPass(SPasswordSuperRequest $request, $id)
    {
      $user = User::find($id);
      $request_data = $request->All();

      $user->password = bcrypt($request_data['password']);
      $user->updated_by_id = \Auth::user()->id;
      $user->save();

      Flash::success(trans('messages.PASS_CHANGED'))->important();

      return redirect()->route('start.selmod');
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

    /**
     * change the password of user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changeSuperPass(Request $request, $id)
    {
        $user = User::find($id);
        // dd($user);

        return view('admin.users.changesuperpass')->with('user', $user);
    }

    public function changeDate(Request $request)
    {
        $oData = json_decode($request->value);

        $oWorkDate = Carbon::parse($oData);
        $today = Carbon::today();

        if ($today->gte($oWorkDate)) {
          session(['work_date' => $oWorkDate]);

          $iYear = $oWorkDate->year;

          SConnectionUtils::reconnectCompany();
          $oYear = SYear::where('year', $iYear)
                          ->where('is_deleted', false)
                          ->first();

          session(['work_year' => $oYear->id_year]);

          return json_encode('Fecha actualizada');
        }

        return json_encode('ERROR, La fecha de trabajo no puede ser posterior al día de hoy');
    }
}
