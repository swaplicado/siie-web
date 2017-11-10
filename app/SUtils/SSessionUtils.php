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

  public function formatNumber($value = '0', $type = '1')
  {
      try
      {
        switch ($type) {
          case \Config::get('scsiie.FRMT.AMT'):
            $iDecimals = session('decimals_amt');
            break;
          case \Config::get('scsiie.FRMT.QTY'):
            $iDecimals = session('decimals_qty');
            break;

          default:
            $iDecimals = 1;
            break;
        }
        \Debugbar::info($iDecimals);
        return number_format($value, $iDecimals, '.', ',');

      }
      catch (Exception $e)
      {
        return number_format(0, 1, '.', ',');
      }
  }

}
