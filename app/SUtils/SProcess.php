<?php namespace App\SUtils;

use Illuminate\Http\Request;
use App\Http\Requests;

class SProcess {

  public static function constructor($obj, $sPermissionCode, $iModule)
  {
      $obj->middleware('mdpermission:'.$sPermissionCode);
      $sNav = SConfiguration::getModuleNav($iModule);

      $oMenu = new SMenu($iModule, $sNav);
      session(['menu' => $oMenu]);

      $obj->middleware('mdmenu:'.(session()->has('menu') ? session('menu')->getMenu() : \Config::get('scsys.UNDEFINED')));

      return SUtil::getTheUserPermission($sPermissionCode);
  }

}
