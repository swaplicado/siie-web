<?php namespace App\SCore;

use App\MMS\SProductionOrder;
use App\MMS\SStatusOrder;
use App\SCore\SProductionCore;

class SProductionOrderCore {

    public static function validateNextStatus($iProductionOrder = 0, $iCurrentStatus = 0)
    {
        $iMaxStatus = SStatusOrder::max('id_status');

        if (($iCurrentStatus + 1) > $iMaxStatus) {
            return ['No hay un estatus posterior para la orden de producción'];
        }

        $iNewStatus = $iCurrentStatus + 1;
        $bValid = false;

        switch ($iNewStatus) {
          case \Config::get('scmms.PO_STATUS.ST_NEW'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_HEAVY'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_FLOOR'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_PROCESS'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_ENDED'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_CLOSED'):
            $bValid = true;
            break;

          default:

            break;
        }

        return $bValid;
    }


    public static function validatePreviousStatus($iProductionOrder = 0, $iCurrentStatus = 0)
    {
        $iMinStatus = SStatusOrder::min('id_status');

        if (($iCurrentStatus - 1) < $iMinStatus) {
            return ['No hay un estatus previo para la orden de producción'];
        }

        $iNewStatus = $iCurrentStatus - 1;
        $bValid = false;

        switch ($iNewStatus) {
          case \Config::get('scmms.PO_STATUS.ST_NEW'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_HEAVY'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_FLOOR'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_PROCESS'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_ENDED'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_CLOSED'):
            $bValid = true;
            break;

          default:

            break;
        }

        return $bValid;
    }

    public static function toChangeStatus($oProductionOrder = null, $iNewStatus = 0)
    {
        switch ($iNewStatus) {
          case \Config::get('scmms.PO_STATUS.ST_NEW'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_HEAVY'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_FLOOR'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_PROCESS'):
            $bValid = true;
            break;

          case \Config::get('scmms.PO_STATUS.ST_ENDED'):
            if ($oProductionOrder->status_id != \Config::get('scmms.PO_STATUS.ST_CLOSED')) {
              $oResult = SProductionCore::makeConsumption($oProductionOrder->id_order);// warehouse??
            }
            break;

          case \Config::get('scmms.PO_STATUS.ST_CLOSED'):
            $bValid = true;
            break;

          default:

            break;
        }
    }
}
