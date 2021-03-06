<?php namespace App\SUtils;

use Carbon\Carbon;

use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\ERP\SItemFamily;
use App\ERP\SItemGroup;
use App\ERP\SItemGender;
use App\ERP\SItem;

/**
 * this class contains methods refers to the user interface
 */
class SGuiUtils {

    /**
     * determines if the select of warehouse will be showed based on class
     * and type of movement
     *
     * @param  int  $iMovClassId
     * @param  int $iMovTypeId
     * @param  string $sWhs
     *
     * @return boolean the select will be showed
     */
    public static function isWhsShowed($iMovClassId, $iMovTypeId, $sWhs)
    {
       if ($sWhs == 'whs_src') {
           return $iMovClassId == \Config::get('scwms.MVT_CLS_OUT') ||
   							$iMovTypeId == \Config::get('scwms.MVT_TP_OUT_TRA');
       }

       if ($sWhs == 'whs_des') {
           return $iMovClassId == \Config::get('scwms.MVT_CLS_IN')
              || $iMovTypeId == \Config::get('scwms.MVT_TP_OUT_TRA')
                || $iMovTypeId == \Config::get('scwms.MVT_OUT_RTRN_RM')
                || $iMovTypeId == \Config::get('scwms.MVT_OUT_ASSIGN_PP')
                  || $iMovTypeId == \Config::get('scwms.MVT_OUT_DLVRY_RM');
       }

       return false;
    }

    public static function isProductionMovement($iMovTypeId)
    {
       return $iMovTypeId == \Config::get('scwms.MVT_OUT_DLVRY_RM')
              ||  $iMovTypeId == \Config::get('scwms.MVT_IN_DLVRY_PP')
              ||  $iMovTypeId == \Config::get('scwms.MVT_IN_DLVRY_FP')
              ||  $iMovTypeId == \Config::get('scwms.MVT_OUT_ASSIGN_PP')
                || $iMovTypeId == \Config::get('scwms.MVT_OUT_RTRN_RM');
    }

    public static function isProductionTransfer($iMovTypeId)
    {
       return $iMovTypeId == \Config::get('scwms.MVT_OUT_DLVRY_RM')
              ||  $iMovTypeId == \Config::get('scwms.MVT_OUT_RTRN_RM')
                ||  $iMovTypeId == \Config::get('scwms.MVT_OUT_ASSIGN_PP');
    }

    public static function isProductionDelivery($iMovTypeId)
    {
       return $iMovTypeId == \Config::get('scwms.MVT_IN_DLVRY_PP')
                || $iMovTypeId == \Config::get('scwms.MVT_IN_DLVRY_FP');
    }

    /**
     * returns the label of the div of pallets in pallets reconfigurations
     *
     * @param  int $iMovTypeId receive the mov type
     *
     * @return string the label of pallet in whsmovs view
     */
    public static function getLabelOfPallet($iMovTypeId)
    {
       if ($iMovTypeId == \Config::get('scwms.PALLET_RECONFIG_IN')) {
          return trans('wms.labels.PALLET_TO_DIVIDE');
       }
       else {
         return trans('wms.labels.PALLET_TO_FILL');
       }
    }

    /**
     *  determines if the panel of pallets will be showed to the user based
     *  in the type of movement
     *
     * @param  int $iMovTypeId movement type
     *
     * @return boolean
     */
    public static function showPallet($iMovTypeId)
    {
       return $iMovTypeId == \Config::get('scwms.PALLET_RECONFIG_IN')
              || $iMovTypeId == \Config::get('scwms.PALLET_RECONFIG_OUT');
    }

    /**
     * getTextOfLinkId selected by the user
     *
     * @param  integer $iLinkTypeId
     * @param  integer $iLinkId
     *
     * @return string name of element of link
     */
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
       }
    }

    /**
     * get the name of link selected
     *
     * @param  integer $iLinkTypeId
     *
     * @return string name of link, can be:
     * ALL, CLASS, TYPE, FAMILY, GROUP, GENDER, ITEM
     */
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

    /**
     * return the name of month based in a number
     *
     * @param  int  $iMonth number of month [1=january, 2=february... 12=december]
     *
     * @return string name of month in spanish [enero, febrero, marzo, abril]
     */
    public static function getNameOfMonth($iMonth = 1)
    {
       setlocale(LC_TIME, 'spanish');
       $sName = strftime("%B", mktime(0, 0, 0, $iMonth, 1, 2000));

       return $sName;
    }

    public static function getClassOfPOStatus($iStatus = 0)
    {
        $sClass = '';

        switch ($iStatus) {
          case \Config::get('scmms.PO_STATUS.ST_NEW'):
            $sClass = 'btn btn-default btn-xs';
            break;
          case \Config::get('scmms.PO_STATUS.ST_HEAVY'):
            $sClass = 'btn btn-info btn-xs';
            break;
          case \Config::get('scmms.PO_STATUS.ST_FLOOR'):
            $sClass = 'btn btn-primary btn-xs';
            break;
          case \Config::get('scmms.PO_STATUS.ST_PROCESS'):
            $sClass = 'btn btn-success btn-xs';
            break;
          case \Config::get('scmms.PO_STATUS.ST_ENDED'):
            $sClass = 'btn btn-danger btn-xs';
            break;
          case \Config::get('scmms.PO_STATUS.ST_CLOSED'):
            $sClass = 'btn btn-secondary btn-xs';
            break;
          }

        return $sClass;
    }

    /**
     * return the class of status of quality to show in span
     *
     * @param  int $iStatusQlty
     *
     * @return string name of class to span
     */
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

    /**
     * [getDatesOfFilter description]
     * @param  string $sDtFilter [description]
     * @return [type]            [description]
     */
    public static function getDatesOfFilter($sDtFilter = '')
    {
        $sFilterDate = $sDtFilter == null ? SGuiUtils::getCurrentMonth() : $sDtFilter;
        $aDates = array();

        if (! is_null($sFilterDate) && $sFilterDate != '') {
            $sStartDate = substr($sFilterDate, 0, 10);
            $sStartDate = str_replace('/', '-', $sStartDate);
            $sEndDate = substr($sFilterDate, -10, 10);
            $sEndDate = str_replace('/', '-', $sEndDate);
            $dt = Carbon::parse($sStartDate);
            $dtF = Carbon::parse($sEndDate);

            array_push($aDates, $dt, $dtF);
        }

        return $aDates;
    }

    /**
     * Undocumented function
     *
     * @param Carbon or String 'YYYY-mm-dd' $oDate
     * @param int $nDays number of days to add and substract from date
     * 
     * @return string dd/mm/yyyy - dd/mm/yyyy
     */
    public static function getRangeFromDate($oDate, $nDays) {
        $oDateStart = null;
        if ($oDate instanceof Carbon) {
          $oDateStart = clone $oDate;
        }
        else if (is_string($oDate)) {
          $oDateStart = Carbon::parse($oDate);
        }

        $oAux = clone $oDateStart;

        $sInitialDate = $oAux->subDays($nDays)->format('d/m/Y');
        $sEndDate = $oDateStart->addDays($nDays)->format('d/m/Y');

        return $sInitialDate.' - '.$sEndDate;
    }

    /**
     * get the current month in range format string
     * get the work date in session
     *
     * @return string [dd/mm/yyyy - dd/mm/yyyy]
     */
    public static function getCurrentMonth()
    {
       $month = session('work_date')->format('m');
       $year = session('work_date')->format('Y');

       $first = SGuiUtils::date_first_month_day($month, $year, 'd-m-Y');
       $last = SGuiUtils::date_last_month_day($month, $year, 'd-m-Y');

       $sStartDate = str_replace('-', '/', $first);
       $sEndDate = str_replace('-', '/', $last);

       return $sStartDate.' - '.$sEndDate;
    }

    /**
     * get the current year in range format string
     * get the work date in session
     *
     * @return string [dd/mm/yyyy - dd/mm/yyyy]
     */
    public static function getCurrentYear()
    {
      $year = session('work_date')->format('Y');
      
      $first = SGuiUtils::date_first_month_day(1, $year, 'd-m-Y');
      $last = SGuiUtils::date_last_month_day(12, $year, 'd-m-Y');

      $sStartDate = str_replace('-', '/', $first);
      $sEndDate = str_replace('-', '/', $last);

      return $sStartDate.' - '.$sEndDate;
    }

    /**
     * get the date period based in parameter range format string
     * get the work date in session and add and sustract number of days of parameter
     * work date: 03-04-2019 returns 19/03/2019 - 18/04/2019
     *
     * @return string [dd/mm/yyyy - dd/mm/yyyy]
     */
    public static function getPeriodSinceWorkDate($iDays)
    {
       if (! is_int($iDays)) {
          return SGuiUtils::getCurrentMonth();
       }

       $tWDs = clone session('work_date');
       $tWDe = clone session('work_date');

       $sStartDate = $tWDs->subDays($iDays)->format('d/m/Y');
       $sEndDate = $tWDe->addDays($iDays)->format('d/m/Y');

       return $sStartDate.' - '.$sEndDate;
    }

    /**
     * gets the date of the last day of month and year received
     *
     * @param  integer $iMonth
     * @param  integer $iYear
     * @param  string $sFormat 'd-m-Y' or 'Y-m-d'
     *
     * @return string date based in format received
     */
    public static function date_last_month_day($iMonth = 0, $iYear = 0, $sFormat = '') {
      $day = date("d", mktime(0,0,0, $iMonth + 1, 0, $iYear));

      return date($sFormat, mktime(0,0,0, $iMonth, $day, $iYear));
    }

    /**
     * gets the date of the first day of month and year received
     *
     * @param  integer $iMonth
     * @param  integer $iYear
     * @param  string $sFormat 'd-m-Y' or 'Y-m-d'
     * @return string date based in format received
     */
    public static function date_first_month_day($iMonth = 0, $iYear = 0, $sFormat = '') {
        return date($sFormat, mktime(0,0,0, $iMonth, 1, $iYear));
    }
}
