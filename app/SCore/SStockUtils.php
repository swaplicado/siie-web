<?php namespace App\SCore;

use App\WMS\SWmsLot;
use App\WMS\SPallet;

/**
 * this class manages the stock of company
 */
class SStockUtils
{
    public static function validateStock($aMovement = [], $iWhsId = 0)
    {
        $aErrors = array();

        if (sizeof($aMovement) == 0)
        {
          array_push($aErrors, "El movimiento está vacío");
          return false;
        }

        $aParameters = array();
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWhsId;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

        foreach ($aMovement['rows'] as $movRow)
        {
            if ($movRow['oAuxItem']['is_lot'])
            {
                if (sizeof($movRow['lotRows']) == 0)
                {
                    array_push($aErrors, "El renglón".$movRow['oAuxItem']['name']." no tiene lotes asignados");
                }
                else
                {
                  foreach ($movRow['lotRows'] as $lotRow)
                  {
                      $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $movRow['iItemId'];
                      $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $movRow['iUnitId'];
                      $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = $lotRow['iLotId'];
                      $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $movRow['iPalletId'];

                      if (session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')] < $lotRow['dQuantity'])
                      {
                          $lot = SWmsLot::find($lotRow['iLotId']);
                          if ($movRow['iPalletId'] == 1) {
                            array_push($aErrors, "No hay suficientes existencias sueltas del lote ".$lot->lot);
                          }
                          else {
                            $pallet = SPallet::find($movRow['iPalletId']);
                            array_push($aErrors, "No hay suficientes existencias del lote ".$lot->lot." en la tarima ".$pallet->pallet);
                          }
                          \Debugbar::error($aErrors);
                          return $aErrors;
                      }
                  }
                }
            }
        }

        return $aErrors;
    }

}
