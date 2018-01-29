<?php namespace App\SUtils;

use Illuminate\Http\Request;
use App\Http\Requests;

class SProcess {

  /**
   * constructor
   *
   * @param  object of controller $obj
   * @param  string $sPermissionCode
   * @param  integer $iModule
   *
   * @return object ofUserPermission
   */
  public static function constructor($obj, $sPermissionCode, $iModule)
  {
      // validate if the user has the permission to acces
      $obj->middleware('mdpermission:'.$sPermissionCode);
      // get the class of nav to menu
      $sNav = SConfiguration::getModuleNav($iModule);

      // set the menu
      $oMenu = new SMenu($iModule, $sNav);
      session(['menu' => $oMenu]);

      // display menu with the middleware
      $obj->middleware('mdmenu:'.(session()->has('menu') ? session('menu')->getMenu() : \Config::get('scsys.UNDEFINED')));

      //return the object associated to permission of user
      return SUtil::getTheUserPermission($sPermissionCode);
  }

}
