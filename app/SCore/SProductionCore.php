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
            if ($iMvtSubType == \Config::get('scwms.MVT_MFG_TP_MAT')) {
              $oElementsQuery = $oElementsQuery->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_MATERIAL_MATERIAL'));
            }
            elseif ($iMvtSubType == \Config::get('scwms.MVT_MFG_TP_PACK')) {
              $oElementsQuery = $oElementsQuery->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_PACKING_MATERIAL'));
            }
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

    public static function makeConsumption($iPO = 0)
    {
        $aResult = array();

        $sSelect = ' sum(ws.input) as inputs,
                     sum(ws.output) as outputs,
                     (sum(ws.input) - sum(ws.output)) as stock,
                     ws.*';

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_stock as ws')
                      ->where('ws.prod_ord_id', $iPO)
                      // ->where('ws.whs_id', $iWhs)
                      ->where('is_deleted', false)
                      ->where('mvt_whs_class_id', \Config::get('scwms.MVT_CLS_IN'))
                      ->select(\DB::raw($sSelect))
                      ->groupBy('ws.branch_id')
                      ->groupBy('ws.whs_id')
                      ->groupBy('ws.location_id')
                      ->groupBy('ws.pallet_id')
                      ->groupBy('ws.lot_id')
                      ->groupBy('ws.item_id')
                      ->groupBy('ws.unit_id');

        $lResult = $query->get();

        $lMovements = SProductionCore::stockToMovements($lResult,
                                                        session('work_year'),
                                                        session('work_date')->toDateString(),
                                                        $iPO);

        $oManagment = new SMovsManagment();

        foreach ($lMovements as $mov) {
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
              $oMovement->mvt_adj_type_id = \Config::get('scwms.MVT_ADJ_TP_PRO');
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

              $aRows = array();
          }

          $oMovementRow = new SMovementRow();

          $oMovementRow->quantity = $oStock->stock;
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
              $oMovementRowLot->quantity = $oStock->stock;
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

}
