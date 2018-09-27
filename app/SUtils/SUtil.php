<?php namespace App\SUtils;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\SYS\SUserPermission;
use App\SYS\SUserCompany;
use App\SYS\SCompany;
use App\ERP\SBranch;

class SUtil {

  public function __construct()
  {

  }

  /**
   * Return an UserPermission object with the privileges assigned.
   * If the type user is ADMIN returns an UserPermission object with all the privileges.
   *
   * @param  int  $identifier
   * @param  int  $id_user
   * @return App\SYS\UserPermission
   */
  public static function getTheUserPermission($sPermissionCode)
  {
    SConnectionUtils::reconnectCompany();

      if (\Auth::check()) {
        if (\Auth::user()->user_type_id == SValidation::getUserTypeByArea())
        {
            $userPermission = new SUserPermission();
            $userPermission->id_user_permission = 0;
            $userPermission->privilege_id = \Config::get('scsys.PRIVILEGES.MANAGER');
            $userPermission->user_id = \Auth::user()->id;

            return $userPermission;
        }

        foreach (session('usr_permissions') as $oUserPermission) {
            if ($oUserPermission->thePermission->code == $sPermissionCode)
            {
                return $oUserPermission;
            }
        }
        // foreach (\Auth::user()->userPermission as $oUserPermission)
        // {
        //     if ($oUserPermission->permission->code == $sPermissionCode)
        //     {
        //         return $oUserPermission;
        //     }
        // }
      }

      return NULL;
  }

  /**
   * Return an UserPermission object with the privileges assigned.
   * If the type user is ADMIN returns an UserPermission object with all the privileges.
   *
   * @param  int  $identifier
   * @param  int  $id_user
   * @return App\SYS\UserPermission
   */
  public static function getTheUserPermissionDB($id_user, $identifier)
  {
      \Config::set('database.connections.siie.database', session()->has('company') ? session('company')->database_name : "");

      if (\Auth::check() && \Auth::user()->user_type_id == SValidation::getUserTypeByArea())
      {
          $userPermission = new SUserPermission();
          $userPermission->id_usr_per = 0;
          $userPermission->privilege_id = \Config::get('scsys.PRIVILEGES.MANAGER');
          $userPermission->permission_id = $identifier;
          $userPermission->user_id = \Auth::user()->id;
      }
      else
      {
          $userPermission = SUserPermission::where('user_id', $id_user)
                                  ->where('permission_id', $identifier)->first();
      }

      return $userPermission;
  }

  /**
   * Return a list of UserCompany objects corresponding to the user.
   *
   * @param  int  $iUserId
   * @return list of App\SYS\UserCompany
   */
  public static function getUserCompany($oUser)
  {
      $lUserCompany = array();

      if (session('utils')->isSuperUser(\Auth::user()))
      {
        $lCompanies = SCompany::where('is_deleted', 0)->paginate(10);

        $i = 0;
        foreach ($lCompanies as $oCompany) {
          $oUserCompany = new SUserCompany();
          $oUserCompany->company_id = $oCompany->id_company;
          $lUserCompany[$i] = $oUserCompany;
          $i++;
        }
      }
      else
      {
        $lUserCompany = SUserCompany::where('user_id', '=', $oUser->id)->paginate(10);
      }

      foreach($lUserCompany as $UC) {
        $UC->company;
      }

      return $lUserCompany;
  }

  /**
   *  return an array with the branches corresponding
   *  to the current partner in session
   *
   * @return array SBranch
   */
  public static function companyBranchesArray()
  {
      return SBranch::where('partner_id', session()->has('partner') ? session('partner')->id_partner : 0)
                  ->where('is_deleted', false)
                  ->orderBy('name', 'ASC')
                  ->lists('name', 'id_branch');
  }

}
