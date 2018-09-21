<?php namespace App\SCore;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Database\Config;
use App\SUtils\SGuiUtils;
use App\SCore\SStockManagment;
use App\SCore\SMovsManagment;

use App\ERP\SYear;
use App\ERP\SItem;
use App\WMS\SPallet;

use App\WMS\SMvtType;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

use App\MMS\SProductionOrder;
use App\MMS\SPoPallet;

/**
 *
 */
class SProductionCore {


    public static function filterForProduction($oElementsQuery = [], $iMvtType = 0,
                                              $iMvtSubType = 0, $iSrcPO = 0, $iDesPO = 0)
    {
        if (! SGuiUtils::isProductionMovement($iMvtType)) {
           return $oElementsQuery;
        }

        switch ($iMvtType) {
          case \Config::get('scwms.MVT_OUT_DLVRY_RM'):
            if ($iMvtSubType == \Config::get('scwms.MVT_MFG_TP_MAT')
                  || $iMvtSubType == \Config::get('scwms.MVT_MFG_TP_PACK')) {
              $oElementsQuery = $oElementsQuery->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_MATERIAL_MATERIAL'));
            }
            // elseif () {
            //   $oElementsQuery = $oElementsQuery->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_PACKING_MATERIAL'));
            // }
            break;

          case \Config::get('scwms.MVT_IN_ASSIGN_PP'):
            $oElementsQuery = $oElementsQuery->whereRaw('ei.id_item IN (
                                                    SELECT item_id FROM
                                                    mms_formula_rows
                                                    WHERE formula_id =
                                                    (SELECT formula_id FROM
                                                    mms_production_orders
                                                    WHERE id_order = '.$iSrcPO.')
                                                  )')
                                                ->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'));
            break;

          case \Config::get('scwms.MVT_OUT_RTRN_RM'):
            $oElementsQuery = $oElementsQuery->where('ws.prod_ord_id', $iSrcPO);
            break;

          case \Config::get('scwms.MVT_IN_DLVRY_PP'):
          case \Config::get('scwms.MVT_IN_DLVRY_FP'):
            $oElementsQuery = $oElementsQuery->whereRaw('ei.id_item = (
                                                    SELECT item_id FROM
                                                    mms_production_orders
                                                    WHERE id_order = '.$iSrcPO.'
                                                  )');
            break;

          default:
            // code...
            break;
        }

        return $oElementsQuery;
    }

    public static function filterProductionWithStock($iMvtType = 0)
    {
        return $iMvtType != \Config::get('scwms.MVT_OUT_RTRN_PP')
               &&  $iMvtType != \Config::get('scwms.MVT_IN_DLVRY_PP')
                &&  $iMvtType != \Config::get('scwms.MVT_IN_DLVRY_FP')
                ;
    }

    public static function prepareReturn($lElements = 0, $iMvtType = 0, $iSrcPO = 0,
                                                  $iDesPO = 0, $iElementType = 0)
    {
       $oSrcPO = SProductionOrder::find($iSrcPO);
       $oFormula = $oSrcPO->formula;
       $oItem = $oSrcPO->item;
       $oUnit = $oSrcPO->unit;

       $lElementsToReturn = array();

       if (sizeof($lElements) > 0) {

       }
       else {
         switch ($iElementType) {
           case \Config::get('scwms.ELEMENTS_TYPE.ITEMS'):
              $aToFor = array();
              $aToFor[0] = 1;
             break;

           case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
              $aToFor = array();
              $aToFor[0] = 'GENERATION';
             break;

           case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
             $aToFor = SProductionCore::getPallets($oItem->id_item, $oUnit->id_unit,
                                                      $iSrcPO = 0, $iDesPO = 0);
             break;

           default:
              $aToFor = array();
             break;
         }

          foreach ($aToFor as $oElem) {
            $oRow = array();

            $oRow['id_item'] = $oSrcPO->item_id;
            $oRow['id_unit'] = $oSrcPO->unit_id;
            $oRow['item_code'] = $oItem->code;
            $oRow['item_name'] = $oItem->name;
            $oRow['unit_code'] = $oUnit->code;
            $oRow['is_lot'] = $oItem->is_lot;
            $oRow['is_bulk'] = $oItem->is_bulk;
            $oRow['without_rotation'] = $oItem->without_rotation;
            $oRow['available_stock'] = $oSrcPO->charges * $oFormula->quantity;
            $oRow['stock'] = $oSrcPO->charges * $oFormula->quantity;
            $oRow['segregated'] = 0;

            switch ($iElementType) {
              case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
                $oRow['lot'] = '';
                $oRow['id_lot'] = 1;
                $oRow['dt_expiry'] = '2017-01-01';
                break;

              case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
                $oRow['pallet'] = $oElem->pallet_id == 1 ? 'SIN TARIMA' : $oElem->pallet_id;
                $oRow['id_pallet'] = $oElem->pallet_id;
                break;

              default:
                break;
            }

            array_push($lElementsToReturn, $oRow);
          }

       }

       return $lElementsToReturn;
    }

    private static function getPallets($iItem = 0, $iUnit = 0, $iSrcPO = 0, $iDesPO = 0)
    {
        $aPallets = SPallet::where('item_id', $iItem)
                              ->where('unit_id', $iUnit)
                              ->where('is_deleted', false);

        $sSelect = 'ws.id_stock,
                    ws.pallet_id,
                    sum(ws.input) as inputs,
                    sum(ws.output) as outputs,
                    (sum(ws.input) - sum(ws.output)) as stock';

        $aSegregationParameters = array();
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 'ws.item_id';
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 'ws.unit_id';
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'ws.lot_id';
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'ws.pallet_id';
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ws.whs_id';
        $aSegregationParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] = session('work_date')->toDateString();

        $sub = session('stock')->getSubSegregated($aSegregationParameters);
        $sSelect = $sSelect.', ('.($sub->toSql()).') as segregated';

        $oQuery = SStockManagment::getStockBaseQuery($sSelect)
                                      ->where('ws.item_id', $iItem)
                                      ->where('ws.unit_id', $iUnit)
                                      ->where('wl.is_deleted', false)
                                      ->having('stock', '=', '0')
                                      ->having('segregated', '=', '0')
                                      ->groupBy('ws.pallet_id')
                                      ->groupBy('ws.item_id')
                                      ->groupBy('ws.unit_id');

        // if ($iSrcPO > 0) {
        //   $oQuery = $oQuery->where('ws.prod_ord_id', $iSrcPO);
        // }

        $oQuery = $oQuery->get();

        return $oQuery;
    }

    public static function getConsumption($iPO = 0, $iItemId = 0, $iUnitId = 0)
    {
        $oQuery = SProductionCore::getConsumptionsQuery($iPO, $iItemId, $iUnitId);

        $lResult = $oQuery->get();
        $lToreturn = array();

        foreach ($lResult as $oStock) {
          $bAddRow = true;

          $bAddRow = ! ($oStock->pallet_id > 1 && $oStock->mpp_pallet_id == null);

          if (! $oStock->is_consumed) {
            $oStock->to_consume = $oStock->delivered - $oStock->returned - $oStock->consumed;

            if ($oStock->to_consume > 0 && $bAddRow) {
              array_push($lToreturn, $oStock);
            }
          }
        }

        return $lToreturn;
    }

    private static function getConsumptionsQuery($iPO, $iItemId = 0, $iUnitId = 0)
    {
        $oPO = SProductionOrder::find($iPO);

        $oItem = $oPO->item;
        $oGender = $oItem->gender;

        $sSelect =
                   'SUM(IF (ws.mvt_whs_type_id = '.\Config::get('scwms.MVT_IN_DLVRY_RM').'
                            OR ws.mvt_whs_type_id = '.\Config::get('scwms.MVT_IN_ASSIGN_PP').', ws.input, 0)) AS delivered,
                   SUM(IF (ws.mvt_whs_type_id = '.\Config::get('scwms.MVT_OUT_RTRN_RM').', ws.output, 0)) AS returned,
                   SUM(IF (ws.mvt_whs_type_id = '.\Config::get('scwms.MVT_OUT_CONSUMPTION').', ws.output, 0)) AS consumed,
                   ei.code AS item_code,
                   ei.name AS item,
                   wl.lot,
                   wl.dt_expiry,
                   wp.id_pallet,
                   wwl.code AS loc_code,
                   ww.code AS whs_code,
                   eb.code AS branch_code,
                   eu.code AS unit_code,
                   ws.item_id,
                   ws.unit_id,
                   ws.lot_id,
                   ws.pallet_id,
                   mpp.is_consumed,
                   mpp.pallet_id AS mpp_pallet_id,
                   ws.location_id,
                   ws.whs_id,
                   ws.branch_id
                   ';

        $oQuery = session('stock')->getStockBaseQuery($sSelect);

        $oQuery = $oQuery->where('ws.prod_ord_id', $iPO)
                          ->leftjoin('mmss_po_pallets as mpp', function($join)
                            {
                                $join->on('ws.pallet_id', '=', 'mpp.pallet_id');
                                $join->on('ws.prod_ord_id', '=', 'mpp.po_id');
                            })
                          ->groupBy('ws.branch_id')
                          ->groupBy('ws.whs_id')
                          ->groupBy('ws.location_id')
                          ->groupBy('ws.pallet_id')
                          ->groupBy('ws.lot_id')
                          ->groupBy('ws.item_id')
                          ->groupBy('ws.unit_id');

        if ($iItemId > 0 && $iUnitId > 0) {
            $oQuery = $oQuery->where('ws.item_id', $iItemId)
                              ->where('ws.unit_id', $iUnitId);
        }

        switch ($oGender->item_type_id) {
          case \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'):
            $oQuery = $oQuery->where(function ($querytemp) {
                                  $querytemp->where('ws.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_RM'))
                                        ->orWhere('ws.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_RTRN_RM'))
                                        ->orWhere('ws.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_CONSUMPTION'));
                              });

            break;

          case \Config::get('scsiie.ITEM_TYPE.FINISHED_PRODUCT'):
            $oQuery = $oQuery->where(function ($querytemp) {
                                  $querytemp->where('ws.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_RM'))
                                        ->orWhere('ws.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_RTRN_RM'))
                                        ->orWhere('ws.mvt_whs_type_id', \Config::get('scwms.MVT_IN_ASSIGN_PP'))
                                        ->orWhere('ws.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_CONSUMPTION'));
                              });
            break;

          default:
            // code...
            break;
        }

        return $oQuery;
    }

    public static function processConsumption(Request $request, $lConsumRows = [], $iProductionOrder)
    {
       $sSelect = 'sum(ws.input) as inputs,
                    sum(ws.output) as outputs,
                    (sum(ws.input) - sum(ws.output)) as stock';

       $bErrorByStock = false;
       $bErrorByConsumption = false;
       $aResult = array();
       $lToConsume = array();
       $iFolio = 0;

       foreach ($lConsumRows as $oConsumRow) {
          if ($oConsumRow->to_consume <= 0) {
              continue;
          }

          $oStockQuery = SStockManagment::getStockBaseQuery($sSelect)
                    ->where('ws.item_id', $oConsumRow->item_id)
                    ->where('ws.unit_id', $oConsumRow->unit_id)
                    ->where('ws.lot_id', $oConsumRow->lot_id)
                    ->where('ws.pallet_id', $oConsumRow->pallet_id)
                    ->where('ws.location_id', $oConsumRow->location_id)
                    ->where('ws.whs_id', $oConsumRow->whs_id)
                    ->where('ws.branch_id', $oConsumRow->branch_id)
                    ->having('stock', '>', '0')
                    ->get();

          if (sizeof($oStockQuery) > 0) {
            if ($oStockQuery[0]->stock < $oConsumRow->to_consume) {
               if ($oConsumRow->pallet_id > 1) {
                 $oResPoPall = SPoPallet::where('pallet_id', $oConsumRow->pallet_id)
                                           ->where('po_id', $iProductionOrder)
                                           ->get();

                 // Si la tarima se reconfiguró pero fue asignada a la orden de producción
                 // entonces se consume toda la tarima
                 if (sizeof($oResPoPall) > 0) {
                    $bConsumed = false;

                    foreach ($oResPoPall as $oPoPall) {
                       $bConsumed = $oPoPall->is_consumed;
                    }

                    if (! $bConsumed) {
                      $oConsumRow->to_consume = $oStockQuery[0]->stock;
                    }
                 }
                 else {
                   //la tarima no está asignada a la orden de producción
                   $bErrorByConsumption = true;
                   array_push($aResult, 'No hay existencias suficientes para hacer el
                                         consumo. Revise el movimiento');
                 }
               }
               else {
                 // no se puede consumir
                 $bErrorByConsumption = true;
                 array_push($aResult, 'No hay existencias suficientes para hacer el
                                       consumo. Revise el movimiento');
               }
            }
          }
          else {
            if ($oConsumRow->pallet_id > 1) {
              $oStockQueryAux = SStockManagment::getStockBaseQuery($sSelect)
                        ->where('ws.item_id', $oConsumRow->item_id)
                        ->where('ws.unit_id', $oConsumRow->unit_id)
                        ->where('ws.lot_id', $oConsumRow->lot_id)
                        ->where('ws.pallet_id', 1)
                        ->where('ws.location_id', $oConsumRow->location_id)
                        ->where('ws.whs_id', $oConsumRow->whs_id)
                        ->where('ws.branch_id', $oConsumRow->branch_id)
                        ->having('stock', '>', '0')
                        ->get();

               if (sizeof($oStockQueryAux) > 0) {
                  if ($oStockQueryAux[0]->stock >= $oConsumRow->to_consume) {
                    $oConsumRow->pallet_id = 1;
                  }
                  else {
                    $bErrorByConsumption = true;
                    array_push($aResult, 'No hay existencias suficientes para hacer el
                                          consumo. Revise el movimiento');
                  }
               }
               else {
                 $bErrorByConsumption = true;
                 array_push($aResult, 'No hay existencias suficientes para hacer el
                                       consumo. Revise el movimiento');
               }
            }
            else {
              $bErrorByConsumption = true;
              array_push($aResult, 'No hay existencias suficientes para hacer el
                                    consumo. Revise el movimiento');
            }
          }

          if (!$bErrorByStock && !$bErrorByConsumption) {
            array_push($lToConsume, $oConsumRow);
          }
       }

       if (!$bErrorByStock && !$bErrorByConsumption) {
          $lConsMovements = SProductionCore::stockToMovements($lToConsume, session('work_year'),
                                        session('work_date')->toDateString(), $iProductionOrder);

          $oManagment = new SMovsManagment();

          foreach ($lConsMovements as $mov) {
              $result = $oManagment->processTheMovement(\Config::get('scwms.OPERATION_TYPE.CREATION'),
                                              $mov,
                                              $mov->aAuxRows,
                                              $mov->mvt_whs_class_id,
                                              $mov->mvt_whs_type_id,
                                              $mov->whs_id,
                                              0,
                                              0,
                                              0,
                                              $request);

              if (is_array($result)) {
                if(sizeof($result) > 0) {
                    array_push($aResult, $result);
                }
              }
              else {
                foreach ($mov->aAuxRows as $oMRow) {
                  if ($oMRow->pallet_id > 1) {
                    $lPoPallets = SPoPallet::where('pallet_id', $oMRow->pallet_id)
                                              ->where('po_id', $iProductionOrder)
                                              ->get();

                    if (sizeof($lPoPallets) > 0) {
                       foreach ($lPoPallets as $oPoPall) {
                          $oPoPall->is_consumed = true;
                          $oPoPall->save();
                       }
                    }
                  }
                }

                $iFolio = $result;
              }
          }
       }
       else {
         array_push($aResult, 'Error');
       }

       if (sizeof($aResult) == 0) {
         $aResult = $iFolio;
       }

       return $aResult;
    }

    /**
     * transform the stock in array of movements
     *
     * @param  array   $lStock list of stock
     * @param  integer $iYearId id of year of the movements
     * @param  string $sDate date of movement
     *
     * @return array[SMovement]
     */
    private static function stockToMovements($lStock = [], $iYearId = 0, $sDate = '', $iPO = 0)
    {
       $lMovements = array();
       $iWhs = 0;
       $iItem = 0;
       $iUnit = 0;
       foreach ($lStock as $oStock) {
           if ($iWhs != $oStock->whs_id) {
              if ($iWhs != 0) {
                 $oMovement->aAuxRows = $aRows;

                 array_push($lMovements, $oMovement);
              }
              $iWhs = $oStock->whs_id;

              $oMovement = new SMovement();
              $oMovement->dt_date = $sDate;
              $oMovement->is_closed_shipment = false;
              $oMovement->is_deleted = false;
              $oMovement->is_system = true;
              $oMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_OUT');
              $oMovement->mvt_whs_type_id = \Config::get('scwms.MVT_OUT_CONSUMPTION');
              $oMovement->mvt_trn_type_id = 1;
              $oMovement->mvt_adj_type_id = 1;
              $oMovement->mvt_mfg_type_id = 1;
              $oMovement->mvt_exp_type_id = 1;
              $oMovement->branch_id = $oStock->branch_id;
              $oMovement->whs_id = $oStock->whs_id;
              $oMovement->year_id = $iYearId;
              $oMovement->auth_status_id = 1;
              $oMovement->src_mvt_id = 1;
              $oMovement->doc_order_id = 1;
              $oMovement->doc_invoice_id = 1;
              $oMovement->doc_debit_note_id = 1;
              $oMovement->doc_credit_note_id = 1;
              $oMovement->prod_ord_id = $iPO;
              $oMovement->mfg_dept_id = 1;
              $oMovement->mfg_line_id = 1;
              $oMovement->mfg_job_id = 1;
              $oMovement->auth_status_by_id = 1;
              $oMovement->closed_shipment_by_id = 1;
              $oMovement->created_by_id = \Auth::user()->id;
              $oMovement->updated_by_id = \Auth::user()->id;

              $oMovement->aAuxPOs[SMovement::SRC_PO] = $iPO;

              $aRows = array();
          }

          $oMovementRow = new SMovementRow();

          $oMovementRow->quantity = $oStock->to_consume;
          $oMovementRow->amount_unit = 0;
          $oMovementRow->amount = 0;
          $oMovementRow->length = 0;
          $oMovementRow->surface = 0;
          $oMovementRow->volume = 0;
          $oMovementRow->mass = 0;
          $oMovementRow->is_deleted = false;
          $oMovementRow->item_id = $oStock->item_id;
          $oMovementRow->unit_id = $oStock->unit_id;
          $oMovementRow->pallet_id = $oStock->pallet_id;
          $oMovementRow->location_id = $oStock->location_id;
          $oMovementRow->doc_order_row_id = 1;
          $oMovementRow->doc_invoice_row_id = 1;
          $oMovementRow->doc_debit_note_row_id = 1;
          $oMovementRow->doc_credit_note_row_id = 1;

          if ($oMovementRow->item->is_lot) {
              $rowLots = array();

              $oMovementRowLot = new SMovementRowLot();
              $oMovementRowLot->quantity = $oStock->to_consume;
              $oMovementRowLot->amount_unit = 0;
              $oMovementRowLot->amount = 0;
              $oMovementRowLot->length = 0;
              $oMovementRowLot->surface = 0;
              $oMovementRowLot->volume = 0;
              $oMovementRowLot->mass = 0;
              $oMovementRowLot->is_deleted = false;
              $oMovementRowLot->lot_id = $oStock->lot_id;

              $rowLots[0] = $oMovementRowLot;
              $oMovementRow->setAuxLots($rowLots);
          }

          array_push($aRows, $oMovementRow);

          if (end($lStock) == $oStock) {
            $oMovement->aAuxRows = $aRows;

            array_push($lMovements, $oMovement);
          }
        }

        return $lMovements;
     }

     public static function getDeliveredByPO($oPO)
     {
         $oItem = $oPO->item;
         $oGender = $oItem->gender;

         $oQuery = \DB::connection(session('db_configuration')->getConnCompany())
                       ->table('wms_mvts as wm')
                       ->join('wms_mvt_rows as wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                       ->selectRaw('COALESCE(SUM(quantity), 0) AS sum_inputs')
                       ->where('wm.is_deleted', false)
                       ->where('wmr.is_deleted', false)
                       ->where('wm.mvt_whs_class_id', \Config::get('scwms.MVT_CLS_IN'))
                       ->where('wmr.item_id', $oPO->item_id)
                       ->where('wmr.unit_id', $oPO->unit_id)
                       ->where('prod_ord_id', $oPO->id_order);

         switch ($oGender->item_type_id) {
           case \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'):
             $oQuery = $oQuery->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_PP'));
             break;

           default:
             // code...
             break;
         }

         $oQuery = $oQuery->get();

         return $oQuery[0]->sum_inputs;
     }

     /**
      * link the assignament movement with pallet and production order
      *
      * @param  integer $iMovementType
      * @param  integer $iProductionOrder (Id of production order)
      * @param  array   $lMovements  (array of SMovementRow)
      *
      * @return boolean success or not
      */

    public static function managePoPallet($iMovementType = 0, $iProductionOrder = 0, $lRows = [])
    {
      $bMade = false;

      try {
        switch ($iMovementType) {
          case \Config::get('scwms.MVT_OUT_DLVRY_RM'):
          case \Config::get('scwms.MVT_OUT_DLVRY_PP'):
          case \Config::get('scwms.MVT_IN_DLVRY_FP'):
            foreach ($lRows as $oRow) {
               if ($oRow->pallet_id > 1) {
                 $oRes = SPoPallet::where('pallet_id', $oRow->pallet_id)
                                           ->where('po_id', $iProductionOrder)
                                           ->get();
                 if (sizeof($oRes) > 0) {
                   continue;
                 }

                 $oPoPallet = new SPoPallet();
                 $oPoPallet->po_id = $iProductionOrder;
                 $oPoPallet->pallet_id = $oRow->pallet_id;
                 $oPoPallet->save();
               }
            }

            $bMade = true;
            break;

          case \Config::get('scwms.MVT_OUT_RTRN_RM'):
            foreach ($lRows as $oRow) {
               if ($oRow->pallet_id > 1) {
                  SPoPallet::where('po_id', $iProductionOrder)
                              ->where('pallet_id', $oRow->pallet_id)
                              ->delete();
               }
            }
            $bMade = true;
            break;

          default:
            $bMade = false;
            break;
        }
      } catch (\Exception $e) {
        $bMade = false;
        \Log::error($e);
      }

      return $bMade;
    }

}
