<?php namespace App\SUtils;

use App\User;

class SValidation {

  /**
   * Determines the visibility of the element, based on the privilege received and the element type.
   *
   * @param  int  $iElementType
   * @param  \App\UserPermission $oUserPermission
   * @param  int  $iCreatedBy
   * @return visibility: visible|hidden|collapse|initial|inherit;
   */
  public static function isRendered($iElementType, $oUserPermission, $iCreatedBy)
  {
    # visibility: visible|hidden|collapse|initial|inherit;

    $sRender = 'hidden';

    if (\Auth::user()->user_type_id == SValidation::getUserTypeByArea()) {
      return 'visible';
    }

    switch ($iElementType) {
      case \Config::get('scsys.OPERATION.CREATE'): # create
          if ($oUserPermission->privilege_id >= \Config::get('scsys.PRIVILEGES.AUTHOR')) {
              $sRender = 'visible';
          }
          break;
      case \Config::get('scsys.OPERATION.EDIT'): # edit
          if ($oUserPermission->privilege_id >= \Config::get('scsys.PRIVILEGES.EDITOR')) {
              $sRender = 'visible';
          }
          else if ($oUserPermission->privilege_id == \Config::get('scsys.PRIVILEGES.AUTHOR') && $iCreatedBy == $oUserPermission->user_id) {
            $sRender = 'visible';
          }
          break;
      case \Config::get('scsys.OPERATION.DEL'): #delete
      case \Config::get('scsys.OPERATION.SUPER'): #super
          if ($oUserPermission->privilege_id == \Config::get('scsys.PRIVILEGES.MANAGER')) {
            $sRender = 'visible';
          }
          break;
    }

    return $sRender;
  }

  /**
   * Determines if the element is visible based in the visibility attribute.
   *
   * @param  int  $iElementType
   * @param  \App\Assignament $oUserPermission
   * @param  int  $iCreatedBy
   * @return true or false
   */
  public static function isRenderedB($iElementType, $oUserPermission, $iCreatedBy)
  {
      $sRender = SValidation::isRendered($iElementType, $oUserPermission, $iCreatedBy);

      return $sRender == 'visible';
  }

  /**
   * Determines if the element of menú is visible based in the permission.
   *
   * @param  int  $iPermissionId
   * @return 'none' or ''
   */
  public static function showMenu($iPermissionType, $iPermissionCode)
  {
      if (SValidation::hasPermission($iPermissionType, $iPermissionCode))
      {
        return '';
      }
      else
      {
        return 'none';
      }
  }

  /**
   * Determines whether the session user has the permission or not.
   *
   * @param  int  $iPermissionCode code assigned to permission. \Config.scperm
   *
   * @return true or false
   */
   public static function hasPermission($sPermissionCode)
   {
       if (\Auth::check())
       {
         if (\Auth::user()->user_type_id == SValidation::getUserTypeByArea())
         {
             return true;
         }

         foreach (session('usr_permissions') as $oUserPermission) {
             if ($oUserPermission->thePermission->code == $sPermissionCode)
             {
                 return $oUserPermission;
             }
         }

         // foreach (\Auth::user()->userPermission as $oUserPermission)
         // {
         //   if ($oUserPermission->permission->code == $sPermissionCode)
         //   {
         //       return true;
         //   }
         // }
       }

       return false;
   }

  /**
   * Determines whether the session user has the received permission or not.
   *
   * @param  int  $iPermissionType integer value from \Config.scperm.TP_PERMISSION
   * @param  int  $iPermissionCode code assigned to permission. \Config.scperm
   *
   * @return true or false
   */
   public static function hasPermissionByType($iPermissionType, $iPermissionCode)
   {
       if (\Auth::check()) {
         if (\Auth::user()->user_type_id == SValidation::getUserTypeByArea())
         {
             return true;
         }

         foreach (session('usr_permissions') as $oUserPermission) {
           if ($oUserPermission->permission_type_id == $iPermissionType)
           {
             if ($oUserPermission->thePermission->code == $iPermissionCode)
             {
                 return $oUserPermission;
             }
           }
         }

         // foreach (\Auth::user()->userPermission as $oUserPermission)
         // {
         //   if ($oUserPermission->permission_type_id == $iPermissionType)
         //   {
         //     if ($oUserPermission->permission->code == $iPermissionCode)
         //     {
         //         return true;
         //     }
         //   }
         // }
       }

       return false;
   }

  /**
   * Determines if ,based on the privilege received, the user is authorized to create
   *
   * @param  int  $iPrivilegeId
   * @return true or false
   */
  public static function canCreate($iPrivilegeId)
  {
      return \Config::get('scsys.PRIVILEGES.AUTHOR') <= $iPrivilegeId;
  }

  /**
   * Determines if ,based on the privilege received, the user is authorized to edit
   *
   * @param  int  $iPrivilegeId
   * @return true or false
   */
  public static function canEdit($iPrivilegeId)
  {
      return \Config::get('scsys.PRIVILEGES.EDITOR') <= $iPrivilegeId;
  }

  /**
   * Determines if the user is the author of the registry and if,
   * based on the privilege received, it has the authority to edit
   *
   * @param  int  $iPrivilegeId
   * @return true or false
   */
  public static function canAuthorEdit($iPrivilegeId, $iCreatedBy)
  {
      return \Config::get('scsys.PRIVILEGES.AUTHOR') == $iPrivilegeId
                  && $iCreatedBy == \Auth::user()->id;
  }

  /**
   * Determines if ,based on the privilege received, the user is authorized to destroy
   *
   * @param  int  $iPrivilegeId
   * @return true or false
   */
  public static function canDestroy($iPrivilegeId)
  {
      return \Config::get('scsys.PRIVILEGES.MANAGER') == $iPrivilegeId;
  }

  /**
   * Return true if the user has permission to access to the company.
   *
   * @param  User  $oUser
   * @param  int  $iIdCompany Company id
   *
   * @return true or false
   */
  public static function canAccessToCompany($oUser, $iIdCompany)
  {
      foreach ($oUser->userCompanies as $access)
      {
        if ($access->company_id == $iIdCompany)
        {
          return true;
        }
      }

      return false;
  }

  /**
   * Return true if the user has permission to access to the branch.
   *
   * @param  User  $oUser
   * @param  int  $iIdBranch Company id
   *
   * @return true or false
   */
  public static function canAccessToBranch($oUser, $iIdBranch)
  {
      foreach ($oUser->userBranches as $access)
      {
        if ($access->branch_id == $iIdBranch)
        {
          return true;
        }
      }

      return false;
  }

  /**
   * [getUserTypeByArea description]
   * @return [type] [description]
   */
  public static function getUserTypeByArea()
  {
    // dd('area: '.session('area'));
      switch (session('area')) {
        case \Config::get('scsys.AREA.STANDARD'):
        case \Config::get('scsys.AREA.MANAGER'):
          return \Config::get('scsys.TP_USER.MANAGER');
          break;
        case \Config::get('scsys.AREA.ADMIN'):
          return \Config::get('scsys.TP_USER.ADMIN');
          break;

        default:
          return 0;
          break;
      }
  }

}
