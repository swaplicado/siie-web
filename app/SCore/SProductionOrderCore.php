<?php namespace App\SCore;

use App\MMS\SProductionOrder;
use App\MMS\SStatusOrder;
use App\SCore\SProductionCore;
use App\SUtils\SValidation;

class SProductionOrderCore {

    public static function validateNextStatus($iProductionOrder = 0, $iCurrentStatus = 0)
    {
        $iMaxStatus = SStatusOrder::max('id_status');

        if (($iCurrentStatus + 1) > $iMaxStatus) {
            return ['No hay un estatus posterior para la orden de producción'];
        }

        $oPo = SProductionOrder::find($iProductionOrder);

        $iNewStatus = $iCurrentStatus + 1;
        $bValid = false;

        if (! $oPo->folio > 0) {
          return ['La orden de producción no ha sido programada'];
        }

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
            $bValid = SValidation::hasPermission(\Config::get('scperm.PERMISSION.MMS_CLOSE_PO'));
            if (! $bValid) {
               return ['No tiene permiso para cerrar una orden de producción'];
            }
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
              redirect()->route('mms.orders.consumptions', $oProductionOrder->id_order);
            }
            break;

          case \Config::get('scmms.PO_STATUS.ST_CLOSED'):
            $bValid = true;
            break;

          default:

            break;
        }
    }

    public static function scheduleProductionOrder($iProductionOrder = 0)
    {
      $oPo = SProductionOrder::find($iProductionOrder);
    }
}
