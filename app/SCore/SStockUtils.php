<?php namespace App\SCore;

use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\WMS\SLocation;
use App\WMS\SLimit;

/**
 * this class manages the stock of company
 */
class SStockUtils
{

    /**
     * [validate the stock before the movement is made]
     *
     * @param  SMovement $oMovement
     * @return [array]  [returns an array with the erros description,
     *                    if the array is empty means that errors not found]
     */
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

    /**
     * [validateLimits validate max and mins in warehouses, branches and company]
     * @param  SMovement $oMovement
     * @return [array]   [returns an array with the erros description,
     *                    if the array is empty means that errors not found]
     */
    public static function validateLimits($oMovement = '')
    {
        $aErrors = array();
        $aItems = array();

        if ($oMovement == '')
        {
          array_push($aErrors, "El movimiento está vacío");
          return $aErrors;
        }


        // ??? The validation of limits is only available for location disabled
        if (! session('location_enabled')) {


          foreach ($oMovement->aAuxRows as $movRow)
          {
             if (array_key_exists($movRow->item_id, $aItems)) {
                $aItems[$movRow->item_id] += $movRow->quantity;
             }
             else
             {
               $aItems[$movRow->item_id] = $movRow->quantity;
             }
          }

          if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT'))
          {
              foreach ($aItems as $itemId => $quantity) {
                $aErrors = SStockUtils::validateMin($movRow->item, $oMovement->warehouse, $quantity);
              }
          }
          else
          {
              foreach ($aItems as $itemId => $quantity) {
                $aErrors = SStockUtils::validateMax($movRow->item, $oMovement->warehouse, $quantity);
              }
          }
        }

        return $aErrors;
    }

    /**
     * [validateMax validate that the movement with input class
     *                           do not exceed the maximum configured]
     * @param  [SItem] $oItem
     * @param  [SWarehouse] $oWarehouse
     * @param  [double] $dQuantity  [quantity to be added to warehouse]
     * @return [array]    [returns an array with the erros description,
     *                    if the array is empty means that errors not found]
     */
    public static function validateMax($oItem, $oWarehouse, $dQuantity)
    {
       $aErrors = array();

       $lLimits = SLimit::where('is_deleted', false)
                          ->where('item_id', $oItem->id_item)
                          ->get();

       if (sizeof($lLimits) == 0) {
         return $aErrors;
       }

       $aParameters = array();
       $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

       foreach ($lLimits as $oLimit)
       {
          if ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.WAREHOUSE')
              && $oLimit->container_id == $oWarehouse->id_whs) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oWarehouse->id_whs;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')];
                if (($dStock + $dQuantity) > $oLimit->max)
                {
                   array_push($aErrors, 'El material/producto '.$oItem->name.' excede los límites permitidos en el almacén '.$oWarehouse->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.BRANCH')
              && $oLimit->container_id == $oWarehouse->branch_id) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oWarehouse->branch_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')];
                if (($dStock + $dQuantity) > $oLimit->max)
                {
                   array_push($aErrors, 'El material/producto '.$oItem->name.' excede los límites permitidos en la sucursal '.$oWarehouse->branch->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.COMPANY')
              && $oLimit->container_id == session('partner')->id_partner) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')];
                if (($dStock + $dQuantity) > $oLimit->max)
                {
                   array_push($aErrors, 'El material/producto '.$oItem->name.' excede los límites permitidos en la empresa actual.');
                }
          }
       }

       return $aErrors;
    }

    /**
     * [validateMin validate that the movement with input class
     *                           do not exceed the min configured]
     * @param  [SItem] $oItem
     * @param  [SWarehouse] $oWarehouse
     * @param  [double] $dQuantity  [quantity to be subtracted from warehouse]
     * @return [array]    [returns an array with the erros description,
     *                    if the array is empty means that errors not found]
     */
    public static function validateMin($oItem, $oWarehouse, $dQuantity)
    {
       $aErrors = array();

       $lLimits = SLimit::where('is_deleted', false)
                          ->where('item_id', $oItem->id_item)
                          ->get();

       if (sizeof($lLimits) == 0) {
         return $aErrors;
       }

       $aParameters = array();
       $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 0;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

       foreach ($lLimits as $oLimit)
       {
          if ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.WAREHOUSE')
              && $oLimit->container_id == $oWarehouse->id_whs) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oWarehouse->id_whs;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')];
                if (($dStock - $dQuantity) < $oLimit->min)
                {
                   array_push($aErrors, 'La existencia del material/producto '.$oItem->name.' estaría por debajo del mínimo permitido en el almacén '.$oWarehouse->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.BRANCH')
              && $oLimit->container_id == $oWarehouse->branch_id) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oWarehouse->branch_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')];
                if (($dStock - $dQuantity) < $oLimit->min)
                {
                   array_push($aErrors, 'La existencia del material/producto '.$oItem->name.' estaría por debajo del mínimo permitido en la sucursal '.$oWarehouse->branch->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.COMPANY')
              && $oLimit->container_id == session('partner')->id_partner) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.AVAILABLE')];
                if (($dStock - $dQuantity) < $oLimit->min)
                {
                   array_push($aErrors, 'La existencia del material/producto '.$oItem->name.' estaría por debajo del mínimo permitido en la empresa actual.');
                }
          }
       }

       return $aErrors;
    }

    /**
     * [getPalletLocation returns an object of Spallet, if the pallet doesn't found
     *                    returns and N/A Pallet object]
     *
     * @param  integer $iPalletId
     * @return [SLocation]  [object of SLocation type]
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
