<?php namespace App\SUtils;

class SGuiUtils {

    public static function isWhsShowed($iMovClassId, $iMovTypeId, $sWhs)
    {
       if ($sWhs == 'whs_src')
       {
           return $iMovClassId == \Config::get('scwms.MVT_CLS_OUT') ||
   							$iMovTypeId == \Config::get('scwms.MVT_TP_OUT_TRA');
       }
       if ($sWhs == 'whs_des')
       {
           return $iMovClassId == \Config::get('scwms.MVT_CLS_IN') ||
   							$iMovTypeId == \Config::get('scwms.MVT_TP_OUT_TRA');
       }

       return false;
    }

    public static function getLabelOfPallet($iMovTypeId)
    {
       if ($iMovTypeId == \Config::get('scwms.PALLET_RECONFIG_IN'))
       {
          return trans('wms.labels.PALLET_TO_DIVIDE');
       }
       else
       {
         return trans('wms.labels.PALLET_TO_FILL');
       }
    }

    public static function showPallet($iMovTypeId)
    {
       return $iMovTypeId == \Config::get('scwms.PALLET_RECONFIG_IN')
              || $iMovTypeId == \Config::get('scwms.PALLET_RECONFIG_OUT');
    }
}
