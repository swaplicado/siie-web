<?php namespace App\SUtils;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;

class SConfiguration {

  public static function getModuleNav($iModule)
  {
    $sNav = '';

    switch ($iModule) {
      case \Config::get('scsys.MODULES.ERP'):
        $sNav = \Config::get('scsys.MOD_NAVS.ERP');
        break;

      case \Config::get('scsys.MODULES.MMS'):
        $sNav = \Config::get('scsys.MOD_NAVS.MMS');
        break;

      case \Config::get('scsys.MODULES.QMS'):
        $sNav = \Config::get('scsys.MOD_NAVS.QMS');
        break;

      case \Config::get('scsys.MODULES.WMS'):
        $sNav = \Config::get('scsys.MOD_NAVS.WMS');
        break;

      case \Config::get('scsys.MODULES.TMS'):
        $sNav = \Config::get('scsys.MOD_NAVS.TMS');
        break;

      default:
        # code...
        break;
    }


    return $sNav;
  }
  
}
