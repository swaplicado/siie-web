<?php namespace App\SUtils;

use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\ERP\SItemFamily;
use App\ERP\SItemGroup;
use App\ERP\SItemGender;
use App\ERP\SItem;

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

    public static function getTextOfLinkId($iLinkTypeId = 0, $iLinkId = 0)
    {
       switch ($iLinkTypeId) {
         case \Config::get('scsiie.ITEM_LINK.ALL'):
           return 'TODO';
           break;
         case \Config::get('scsiie.ITEM_LINK.CLASS'):
           return SItemClass::find($iLinkId)->name;
           break;
         case \Config::get('scsiie.ITEM_LINK.TYPE'):
           return SItemType::find($iLinkId)->name;
           break;
         case \Config::get('scsiie.ITEM_LINK.FAMILY'):
           return SItemFamily::find($iLinkId)->name;
           break;
         case \Config::get('scsiie.ITEM_LINK.GROUP'):
           return SItemGroup::find($iLinkId)->name;
           break;
         case \Config::get('scsiie.ITEM_LINK.GENDER'):
           return SItemGender::find($iLinkId)->name;
           break;
         case \Config::get('scsiie.ITEM_LINK.ITEM'):
           $oItem = SItem::find($iLinkId);
           return $oItem->code.'-'.$oItem->name;
           break;

         default:
           return '';
           break;
       }
    }

    public static function getTextOfLinkTypeId($iLinkTypeId = 0)
    {
       switch ($iLinkTypeId) {
         case \Config::get('scsiie.ITEM_LINK.ALL'):
           return 'TODO';
           break;
         case \Config::get('scsiie.ITEM_LINK.CLASS'):
           return 'CLASE';
           break;
         case \Config::get('scsiie.ITEM_LINK.TYPE'):
           return 'TIPO';
           break;
         case \Config::get('scsiie.ITEM_LINK.FAMILY'):
           return 'FAMILIA';
           break;
         case \Config::get('scsiie.ITEM_LINK.GROUP'):
           return 'GRUPO';
           break;
         case \Config::get('scsiie.ITEM_LINK.GENDER'):
           return 'GÉNERO';
           break;
         case \Config::get('scsiie.ITEM_LINK.ITEM'):
           return 'MATERIAL/PRODUCTO';
           break;

         default:
           return '';
           break;
       }
    }

    public static function getNameOfMonth($iMonth){
       setlocale(LC_TIME, 'spanish');
       $sName = strftime("%B", mktime(0, 0, 0, $iMonth, 1, 2000));
       return $sName;
    }

    public static function getClassOfStatus($iStatusQlty)
    {
      $sClass = '';

      switch ($iStatusQlty) {
        case \Config::get('scqms.TO_EVALUATE'):
          $sClass = 'label label-default';
          break;
        case \Config::get('scqms.REJECTED'):
          $sClass = 'label label-danger';
          break;
        case \Config::get('scqms.QUARANTINE'):
          $sClass = 'label label-danger';
          break;
        case \Config::get('scqms.PARTIAL_RELEASED'):
          $sClass = 'label label-primary';
          break;
        case \Config::get('scqms.RELEASED'):
          $sClass = 'label label-success';
          break;
        case \Config::get('scqms.RELEASED_EARLY'):
          $sClass = 'label label-info';
          break;
        case \Config::get('scqms.RET_TO_EVALUATE'):
          $sClass = 'label label-default';
          break;
        case \Config::get('scqms.RECONDITIONING'):
          $sClass = 'label label-default';
          break;
        case \Config::get('scqms.REWORK'):
          $sClass = 'label label-default';
          break;
        case \Config::get('scqms.DESTROY'):
          $sClass = 'label label-danger';
          break;
        case \Config::get('scqms.TO_EVALUATE'):
          $sClass = 'label label-default';
          break;

        default:
          # code...
          break;
      }

      return $sClass;
    }
}
