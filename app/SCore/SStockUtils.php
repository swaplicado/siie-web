<?php namespace App\SCore;

use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\WMS\SLocation;

/**
 * this class manages the stock of company
 */
class SStockUtils
{
    public static function validateStock($oMovement = '')
    {
        $aErrors = array();

        if ($oMovement == '')
        {
          array_push($aErrors, "El movimiento está vacío");
          return $aErrors;
        }

        $aParameters = array();
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

        foreach ($oMovement->aAuxRows as $movRow)
        {
            $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $movRow->item_id;
            $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $movRow->unit_id;
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $movRow->pallet_id;
            $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $movRow->whs_id;

            if ($movRow->item->is_lot)
            {
                if (sizeof($movRow->getAuxLots()) == 0)
                {
                    array_push($aErrors, "El renglón ".$movRow['oAuxItem']['name']." no tiene lotes asignados");
                }
                else
                {
                  foreach ($movRow->getAuxLots() as $lotRow)
                  {
                      $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = $lotRow->lot_id;

                      if (session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')] < $lotRow->quantity)
                      {
                          if ($movRow->pallet_id == 1) {
                            array_push($aErrors, "No hay suficientes existencias SIN TARIMA del lote ".$lotRow->lot->lot);
                          }
                          else {
                            array_push($aErrors, "No hay suficientes existencias del lote ".$lotRow->lot->lot." en la tarima ".$movRow->pallet->pallet);
                          }
                          return $aErrors;
                      }
                  }
                }
            }
            else
            {
                if (session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')] < $movRow->quantity)
                {
                    if ($movRow->pallet_id == 1)
                    {
                      array_push($aErrors, "No hay suficientes existencias SIN TARIMA del material/producto ".$movRow->item->name);
                    }
                    else
                    {
                      array_push($aErrors, "No hay suficientes existencias del material/producto ".$movRow->item->name." en la tarima ".$movRow->pallet->pallet);
                    }
                    return $aErrors;
                }
            }
        }

        return $aErrors;
    }


    public static function validateStock2($aMovement = [], $iWhsId = 0)
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
            $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $movRow['iItemId'];
            $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $movRow['iUnitId'];
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $movRow['iPalletId'];

            if ($movRow['oAuxItem']['is_lot'])
            {
                if (sizeof($movRow['lotRows']) == 0)
                {
                    array_push($aErrors, "El renglón ".$movRow['oAuxItem']['name']." no tiene lotes asignados");
                }
                else
                {
                  foreach ($movRow['lotRows'] as $lotRow)
                  {
                      $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = $lotRow['iLotId'];

                      if (session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')] < $lotRow['dQuantity'])
                      {
                          $lot = SWmsLot::find($lotRow['iLotId']);
                          if ($movRow['iPalletId'] == 1) {
                            array_push($aErrors, "No hay suficientes existencias SIN TARIMA del lote ".$lot->lot);
                          }
                          else {
                            $pallet = SPallet::find($movRow['iPalletId']);
                            array_push($aErrors, "No hay suficientes existencias del lote ".$lot->lot." en la tarima ".$pallet->pallet);
                          }
                          return $aErrors;
                      }
                  }
                }
            }
            else
            {
                if (session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')] < $movRow['dQuantity'])
                {
                    if ($movRow['iPalletId'] == 1)
                    {
                      array_push($aErrors, "No hay suficientes existencias SIN TARIMA del material/producto ".$movRow['oAuxItem']['name']);
                    }
                    else
                    {
                      $pallet = SPallet::find($movRow['iPalletId']);
                      array_push($aErrors, "No hay suficientes existencias del material/producto ".$movRow['oAuxItem']['name']." en la tarima ".$pallet->pallet);
                    }
                    return $aErrors;
                }
            }
        }

        return $aErrors;
    }

    /**
     * [getPalletLocation description]
     * @param  integer $iPalletId [description]
     * @return [type]             [description]
     */
    public static function getPalletLocation($iPalletId = 0)
    {
        $select = 'ws.location_id,
                      sum(ws.input) as inputs,
                      sum(ws.output) as outputs,
                      (sum(ws.input) - sum(ws.output)) as stock';

        \Debugbar::info($select);
        try
        {
          $stock = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('wms_stock as ws')
                        ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                        ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                        ->join('wms_pallets as wp', 'ws.pallet_id', '=', 'wp.id_pallet')
                        ->join('wms_lots as wl', 'ws.lot_id', '=', 'wl.id_lot')
                        ->join('wmsu_whs_locations as wwl', 'ws.location_id', '=', 'wwl.id_whs_location')
                        ->join('wmsu_whs as ww', 'ws.whs_id', '=', 'ww.id_whs')
                        ->select(\DB::raw($select))
                        ->groupBy(['ws.location_id','ws.pallet_id', 'ws.item_id', 'ws.unit_id'])
                        ->orderBy('ws.location_id')
                        ->where('ws.is_deleted', false)
                        ->where('ws.pallet_id', $iPalletId)
                        ->take(1)
                        ->having('stock', '>', '0')
                        ->get();
        }
        catch (Exception $e)
        {
          \Debugbar::error($e);
        }

        if (sizeof($stock) > 0) {
            return SLocation::find($stock[0]->location_id);
        }
        else {
            return SLocation::find(1);
        }

        return $stock;
    }

}
