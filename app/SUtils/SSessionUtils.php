<?php namespace App\SUtils;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;

class SSessionUtils {

  public function isSuperUser($oUser)
  {
      return $oUser->user_type_id == \Config::get('scsys.TP_USER.MANAGER') ||
              $oUser->user_type_id == \Config::get('scsys.TP_USER.ADMIN');
  }

}
