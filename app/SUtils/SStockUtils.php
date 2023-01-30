<?php namespace App\SUtils;

use App\SUtils\SMovsUtils;
use App\WMS\SLimit;
use App\WMS\SLocation;
use App\WMS\SMovement;

/**
 * this class manages the stock of company
 */
class SStockUtils
{

    /**
     * [validate the stock before the movement has been made]
     *
     * @param  SMovement $oMovement
     * @return [array]  [returns an array with the erros description,
     *                    if the array is empty means that errors not found]
     */
    public static function validateStock($oMovement = '')
    {
        $aErrors = array();

        if ($oMovement == '') {
          array_push($aErrors, "El movimiento está vacío");
          return $aErrors;
        }

        $sSelect = 'sum(ws.input) as inputs,
                     sum(ws.output) as outputs,
                     sum(ws.input - ws.output) as stock,
                     AVG(ws.cost_unit) as cost_unit,
                     ei.code as item_code,
                     ei.name as item,
                     eu.code as unit_code,
                     ei.is_lot,
                     ei.id_item,
                     eu.id_unit,
                     ws.lot_id,
                     wl.lot,
                     ws.pallet_id,
                     ws.location_id
                     ';

        $aParameters = array();
        $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oMovement->year_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] = session('work_date')->toDateString();
        $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oMovement->whs_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oMovement->branch_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.WITHOUT_SEGREGATED')] = true;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.AS_SEGREGATED')] = 'segregated';

        if ($oMovement->id_mvt > 0) {
          $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] = $oMovement->id_mvt;
        }

        $loStock = session('stock')->getStockResult($aParameters);

        $loStock = $loStock->groupBy('id_item')
                            ->groupBy('id_unit')
                            ->groupBy('lot_id')
                            ->groupBy('pallet_id')
                            ->groupBy('location_id');

        $lStock = $loStock->get();

        $lStockC = $lStock;

        $bFound = false;

        foreach ($oMovement->rows as $oRow) {
          if ($oRow->is_deleted) {
              continue;
          }

          foreach ($lStockC as $oStock) {
             if ($oStock->id_item == $oRow->item_id && $oStock->id_unit == $oRow->unit_id) {
                if ($oStock->location_id == $oRow->location_id) {
                   if ($oStock->pallet_id == $oRow->pallet_id) {
                      if ($oRow->item->is_lot) {
                          if (sizeof($oRow->lotRows) == 0) {
                              array_push($aErrors, "El renglón ".$oRow->item->name." no tiene lotes asignados");
                              return $aErrors;
                          }
                          foreach ($oRow->lotRows as $oLotRow) {
                             if ($oLotRow->lot_id == $oStock->lot_id) {
                                 $bFound = true;

                                 $lSegStock = session('stock')->getSubSegregated($aParameters);

                                 $lSegStock = $lSegStock->where('item_id', $oRow->item_id)
                                                     ->where('unit_id', $oRow->unit_id)
                                                     // ->where('whs_location_id', $oRow->location_id)
                                                     ->where('pallet_id', $oRow->pallet_id)
                                                     ->where('lot_id', $oLotRow->lot_id)
                                                     ->groupBy('item_id')
                                                     ->groupBy('unit_id')
                                                     ->groupBy('lot_id')
                                                     ->groupBy('pallet_id');
                                                     // ->groupBy('whs_location_id');
                                 // dd($lSegStock->toSql());
                                $lSegStock = $lSegStock->get();

                                $dSegregated = 0;
                                if (sizeof($lSegStock) > 0 && ! SMovsUtils::canSkipSegregation($oMovement->mvt_whs_type_id)) {
                                  $dSegregated = $lSegStock[0]->segregated;
                                }
                                 if (bccomp($oLotRow->quantity, ($oStock->stock - $dSegregated), session('decimals_qty')) == 1) {
                                   try {
                                     if ($oRow->pallet_id == 1) {
                                       array_push($aErrors, "No hay suficientes existencias
                                                              SIN TARIMA del lote ".$oLotRow->lot->lot."
                                                                en la ubicación: ".$oRow->location->name."\n
                                                                Total:".$oStock->stock."\n
                                                                Segregadas:".$dSegregated."\n
                                                                Disponibles:".($oStock->stock - $dSegregated));
                                     }
                                     else {
                                       array_push($aErrors, "No hay suficientes existencias del lote ".$oLotRow->lot->lot.
                                                              " en la tarima ".$oRow->pallet_id.
                                                                " en la ubicación: ".$oRow->location->name."\n
                                                                Total:".$oStock->stock."\n
                                                                Segregadas:".$dSegregated."\n
                                                                Disponibles:".($oStock->stock - $dSegregated));
                                     }
                                   } catch (\Exception $e) {
                                      \Log::error($e);
                                   }
                                 }
                                 else {
                                   $oStock->stock -= $oLotRow->quantity;
                                 }
                             }
                          }
                      }
                      else {
                        $bFound = true;

                        $lSegStock = session('stock')->getSubSegregated($aParameters);

                        $lSegStock = $lSegStock->where('item_id', $oRow->item_id)
                                            ->where('unit_id', $oRow->unit_id)
                                            // ->where('whs_location_id', $oRow->location_id)
                                            ->where('pallet_id', $oRow->pallet_id)
                                            ->where('lot_id', 1)
                                            ->groupBy('item_id')
                                            ->groupBy('unit_id')
                                            ->groupBy('lot_id')
                                            ->groupBy('pallet_id')
                                            // ->groupBy('whs_location_id')
                                            ->get();

                         $dSegregated = 0;
                         if (sizeof($lSegStock) > 0 && ! SMovsUtils::canSkipSegregation($oMovement->mvt_whs_type_id)) {
                           $dSegregated = $lSegStock[0]->segregated;
                         }

                        if (bccomp($oRow->quantity, ($oStock->stock - $dSegregated), session('decimals_qty')) == 1) {
                            if ($oRow->pallet_id == 1) {
                              array_push($aErrors, "No hay suficientes existencias SIN TARIMA del
                                                      material/producto ".$oRow->item->name.
                                                      " en la ubicación: ".$oRow->location->name."\n
                                                      Total:".$oStock->stock."\n
                                                      Segregadas:".$dSegregated."\n
                                                      Disponibles:".($oStock->stock - $dSegregated));
                            }
                            else {
                              array_push($aErrors, "No hay suficientes existencias del material/producto ".$oRow->item->name.
                                                      " en la tarima ".$oRow->pallet_id."
                                                        en la ubicación: ".$oRow->location->name."\n
                                                        Total:".$oStock->stock."\n
                                                        Segregadas:".$dSegregated."\n
                                                        Disponibles:".($oStock->stock - $dSegregated));
                            }
                        }
                        else {
                          $oStock->stock -= $oRow->quantity;
                        }
                      }
                   }
                }
             }
          }
        }

        return $aErrors;
    }

    public static function validatePallet($iYear = 0, $iBranch = 0, $iWarehouse = 0,
                                  $oRow = null, $iMovementType = 0,
                                  $iMovement = 0)
    {
       $aErrors = array();
       if ($oRow->pallet_id == 1) {
          return $aErrors;
       }

       $sSelect = 'sum(ws.input) as inputs,
                    sum(ws.output) as outputs,
                    sum(ws.input - ws.output) as stock,
                    AVG(ws.cost_unit) as cost_unit,
                    ei.code as item_code,
                    ei.name as item,
                    eu.code as unit_code,
                    ei.is_lot,
                    ei.id_item,
                    eu.id_unit,
                    ws.lot_id,
                    wl.lot,
                    ws.pallet_id,
                    ws.location_id
                    ';

       $aParameters = array();
       $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $iYear;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $oRow->pallet_id;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oRow->unit_id;
       $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oRow->item_id;

       if ($iMovement > 0) {
         $mvts = SMovement::where('src_mvt_id', $iMovement)->where('is_deleted', false)->lists('id_mvt');
         if (sizeof($mvts) > 0) {
          $mvts->push($iMovement);
          $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] = $mvts->toArray();
         }
         else {
          $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] = $iMovement;
         }
       }

       $lStockGral = session('stock')->getStockResult($aParameters);

       $lStockGral = $lStockGral->groupBy('id_branch')
                          ->groupBy('id_whs')
                          ->groupBy('id_whs_location')
                          ->having('stock', '>', '0')
                          ->get();

      $location = 0;
      foreach ($lStockGral as $oStockGral) {
        if ($location == 0) {
           $location = $oStockGral->location_id;
        }
        else if ($location != $oStockGral->location_id){
           array_push($aErrors, '¡LA TARIMA '.$oRow->pallet_id.' TIENE EXISTENCIAS EN DIFERENTES UBICACIONES!');
           return $aErrors;
        }
      }

      $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $iBranch;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWarehouse;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = $oRow->location_id;

      $lPalletStock = session('stock')->getStockResult($aParameters);

      $lPalletStock = $lPalletStock->groupBy('ws.lot_id')
                        ->having('stock', '>', '0')
                        ->get();

      if ($iMovementType == \Config::get('scwms.PALLET_RECONFIG_IN')
          || $iMovementType == \Config::get('scwms.PALLET_RECONFIG_OUT')) {
          return $aErrors;
      }

      $dQuantity = 0;
      foreach ($lPalletStock as $oPalletStock) {
        if ($oPalletStock->segregated > 0 && ! SMovsUtils::canSkipSegregation($iMovementType)) {
          array_push($aErrors, 'La tarima '.$oRow->pallet_id.' tiene unidades segregadas.');
          return $aErrors;
        }
        if ($oRow->item->is_lot) {
          $bCurrentLotFound = false;
          foreach ($oRow->getAuxLots() as $oAuxLot) {
            if ($oAuxLot->is_deleted) {
               continue;
            }
            if ($oAuxLot->lot_id == $oPalletStock->lot_id) {
                if ($oAuxLot->quantity != $oPalletStock->stock && $iMovementType != \Config::get('scwms.MVT_TP_OUT_SAL')) {
                  array_push($aErrors, 'La tarima '.$oRow->pallet_id.' debe moverse completa.');
                  return $aErrors;
                }
                $bCurrentLotFound = true;
            }
          }

          if (! $bCurrentLotFound) {
            array_push($aErrors, 'Los lotes que desea mover no corresponden a los que contiene la tarima '.$oRow->pallet_id.'.');
            return $aErrors;
          }
        }
        else {
          if ($oRow->quantity != $oPalletStock->stock) {
            array_push($aErrors, 'No puede mover más unidades de las que contiene la tarima'.$oRow->pallet_id.'.');
            return $aErrors;
          }
        }
      }
    }

    public static function validateInputPallet($oRow = null, $iYear = 0,
                                            $iMovementType = 0, $iMovement = 0 )
    {
        $aErrors = array();
        if ($oRow->pallet_id == 1) {
           return $aErrors;
        }

        $sSelect = 'sum(ws.input) as inputs,
                     sum(ws.output) as outputs,
                     sum(ws.input - ws.output) as stock,
                     AVG(ws.cost_unit) as cost_unit,
                     ei.code as item_code,
                     ei.name as item,
                     eu.code as unit_code,
                     ei.is_lot,
                     ei.id_item,
                     eu.id_unit,
                     ws.pallet_id,
                     ws.location_id
                     ';

        $aParameters = array();
        $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $iYear;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] = session('work_date')->toDateString();
        $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $oRow->pallet_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oRow->unit_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oRow->item_id;

        if ($iMovement > 0) {
          $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] = $iMovement;
        }

        $lStockGral = session('stock')->getStockResult($aParameters);

        $lStockGral = $lStockGral->groupBy('id_branch')
                           ->groupBy('id_whs')
                           ->groupBy('id_whs_location')
                           ->groupBy('id_pallet')
                           ->having('stock', '>', '0')
                           ->get();

        if (sizeof($lStockGral) > 1) {
            array_push($aErrors, '¡LA TARIMA '.$oRow->pallet_id.' TIENE EXISTENCIAS EN DIFERENTES UBICACIONES!');
        }
        elseif (sizeof($lStockGral) == 1 && $oRow->quantity != $lStockGral[0]->stock
                  && !($iMovementType == \Config::get('scwms.PALLET_RECONFIG_IN')
                        || $iMovementType == \Config::get('scwms.PALLET_RECONFIG_OUT'))) {
            array_push($aErrors, 'La tarima '.$oRow->pallet_id.' no está vacía, no puede agregar unidades');
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

        if ($oMovement == '') {
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
             else {
               $aItems[$movRow->item_id] = $movRow->quantity;
             }
          }

          if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT')) {
              foreach ($aItems as $itemId => $quantity) {
                $aErrors = SStockUtils::validateMin($movRow->item, $oMovement->warehouse, $quantity);
              }
          }
          else {
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

       foreach ($lLimits as $oLimit) {
          if ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.WAREHOUSE')
              && $oLimit->container_id == $oWarehouse->id_whs) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oWarehouse->id_whs;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
                if (($dStock + $dQuantity) > $oLimit->max) {
                   array_push($aErrors, 'El material/producto '.$oItem->name.' excede los límites permitidos en el almacén '.$oWarehouse->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.BRANCH')
              && $oLimit->container_id == $oWarehouse->branch_id) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oWarehouse->branch_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
                if (($dStock + $dQuantity) > $oLimit->max) {
                   array_push($aErrors, 'El material/producto '.$oItem->name.' excede los límites permitidos en la sucursal '.$oWarehouse->branch->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.COMPANY')
              && $oLimit->container_id == session('partner')->id_partner) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
                if (($dStock + $dQuantity) > $oLimit->max) {
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

       foreach ($lLimits as $oLimit) {
          if ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.WAREHOUSE')
              && $oLimit->container_id == $oWarehouse->id_whs) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oWarehouse->id_whs;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
                if (($dStock - $dQuantity) < $oLimit->min) {
                   array_push($aErrors, 'La existencia del material/producto '.$oItem->name.' estaría por debajo del mínimo permitido en el almacén '.$oWarehouse->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.BRANCH')
              && $oLimit->container_id == $oWarehouse->branch_id) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oWarehouse->branch_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
                if (($dStock - $dQuantity) < $oLimit->min) {
                   array_push($aErrors, 'La existencia del material/producto '.$oItem->name.' estaría por debajo del mínimo permitido en la sucursal '.$oWarehouse->branch->name);
                }
          }
          elseif ($oLimit->container_type_id == \Config::get('scwms.CONTAINERS.COMPANY')
              && $oLimit->container_id == session('partner')->id_partner) {

                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oItem->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oItem->unit_id;

                $dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
                if (($dStock - $dQuantity) < $oLimit->min) {
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
                      sum(ws.input - ws.output) as stock';

        try {
          $stock = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('wms_stock as ws')
                        ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                        ->join('erpu_units as eu', 'ws.unit_id', '=', 'eu.id_unit')
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
        catch (Exception $e) {
          \Debugbar::error($e);
        }

        if (sizeof($stock) > 0) {
            return SLocation::find($stock[0]->location_id);
        }

        return SLocation::find(1);

    }

    public static function getPalletStock($iPalletId = 0)
    {
        $select = 'ws.location_id,
                      sum(ws.input) as inputs,
                      sum(ws.output) as outputs,
                      sum(ws.input - ws.output) as stock';

        try {
          $stock = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('wms_stock as ws')
                        ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                        ->join('erpu_units as eu', 'ws.unit_id', '=', 'eu.id_unit')
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
        catch (Exception $e) {
          \Debugbar::error($e);
        }

        if (sizeof($stock) == 1) {
            return $stock[0]->stock;
        }
        else if(sizeof($stock) == 0) {
           return 0;
        }
        else {
          return null;
        }
    }

}
