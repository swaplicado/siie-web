<?php namespace App\SCore;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\WMS\SStockController;

use App\SCore\SMovsManagment;

use App\ERP\SYear;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

/**
 *
 */
class SInventoryCore {

    /**
     * obtains the stock from warehouse if this is passed, or the general stock if
     * the warehouse is 0 (all branches)
     *
     * @param  integer $iWarehouse
     * @param  integer $iYear  format yyyy
     * @param  string  $sCutoffDate 'yyy-mm-dd'
     *
     * @return array[Stock]
     */
    public function getStock($iWarehouse = 0, $iYear = 0, $sCutoffDate = '')
    {
        $tCutoffDate = Carbon::parse($sCutoffDate);
        if ($iYear == 0) {
          $iYear = $tCutoffDate->year;
        }

        $oYear = SYear::where('year', $iYear)
                        ->where('is_deleted', false)
                        ->first();

        $sSelect = "ws.branch_id,
                    ws.whs_id,
                    ws.location_id,
                    ws.pallet_id,
                    ws.lot_id,
                    ws.item_id,
                    ws.unit_id,
                    ei.code AS item_code,
                    ei.name AS item,
                    eu.code AS unit,
                    CONCAT(wwl.code, '-', wwl.name) AS location,
                    wp.id_pallet AS pallet,
                    wl.lot,
                    wl.dt_expiry,
                    ei.is_lot,
                    sum(ws.input) as inputs,
                    sum(ws.output) as outputs,
                    (sum(ws.input) - sum(ws.output)) as stock
                    ";
        $aParameters = array();

        $aParameters [\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;

        if ($iWarehouse > 0) {
          $aParameters [\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWarehouse;
        }

        $aParameters [\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oYear->id_year;
        $aParameters [\Config::get('scwms.STOCK_PARAMS.DATE')] = $tCutoffDate->toDateString();

        $aParametersSeg = array();

        $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
        if ($iWarehouse > 0) {
          $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWarehouse;
        }
        else {
          $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ws.whs_id';
        }

        $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oYear->id_year;
        $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.DATE')] = $tCutoffDate->toDateString();
        $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.LOT')] = 'ws.lot_id';
        $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'ws.pallet_id';

        $lStock = session('stock')->getStockResult($aParameters, $aParametersSeg);

        $lStock = $lStock->having('stock', '>', 0)
                            ->groupBy(['ws.branch_id',
                                      'ws.whs_id',
                                      'ws.location_id',
                                      'ws.pallet_id',
                                      'ws.lot_id',
                                      'ws.item_id',
                                      'ws.unit_id'
                                    ])
                            ->orderBy('ws.item_id', 'ASC')
                            ->orderBy('ws.unit_id', 'ASC');

         $lStock = $lStock->get();

         return $lStock;
    }

    /**
     *  generate the initial inventory of the year
     *
     * @param  integer $iYear   format yyyy
     * @param  Illuminate\Http\Request $request
     *
     * @return true if the process was success, array of errors if was there an error
     */
    public function generateInitialInventory($iYear = 0, Request $request)
    {
       $aResult = array();

       $iPastYear = $iYear - 1;
       $tAuxDate = Carbon::createFromDate($iPastYear, 1, 1);
       $tEndOfPastYear = $tAuxDate->endOfYear();

       $result = $this->canBeGenerated($iYear);

       if (is_array($result)) {
         if(sizeof($result) > 0) {
             return $result;
         }
       }

       $lStock = $this->getStock(0, $iPastYear, $tEndOfPastYear);

       if (sizeof($lStock) == 0) {
          return ['No hay existencias para realizar la generación de inventario'];
       }

       $oYear = SYear::where('year', $iYear)
                       ->where('is_deleted', false)
                       ->first();

       $tMovsDate = Carbon::createFromDate($iYear, 1, 1);
       $lMovements = $this->stockToMovements($lStock, $oYear->id_year, $tMovsDate->toDateString());

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
     * determine if the inventory can be generated validating if a previous
     * movements does'nt exist and if these can be erased. After, erase the movements
     *
     * @param  integer $iYear format yyyy
     * @return true or array
     */
    private function canBeGenerated($iYear = 0)
    {
        $lMovementsGenerated = SMovement::where('is_deleted', false)
                                    ->where('mvt_whs_class_id', \Config::get('scwms.MVT_CLS_IN'))
                                    ->where('mvt_adj_type_id', \Config::get('scwms.MVT_ADJ_TP_IFI'))
                                    ->where('is_system', true)
                                    ->where('dt_date', $iYear.'-01-01')
                                    ->where('mvt_whs_type_id', \Config::get('scwms.MVT_TP_IN_ADJ'))
                                    ->get();

        if (sizeof($lMovementsGenerated) > 0) {
           $oMovsMng = new SMovsManagment();
           foreach ($lMovementsGenerated as $genMov) {
             $result = $oMovsMng->canMovBeErasedOrActivated($genMov, \Config::get('scwms.MOV_ACTION.ERASE'));

             if (is_array($result) && sizeof($result)) {
                  array_push($result, $aResult);

                  return $aResult;
             }
           }


           foreach ($lMovementsGenerated as $mov) {
              $res = $this->eraseMovement($mov);

              if (is_array($res) && sizeof($res)) {
                   return $res;
              }
           }
        }

        return true;
    }

    /**
     * erase the movement (is_deleted = true)
     *
     * @param  SMovement $oMov
     *
     * @return true or array
     */
    private function eraseMovement($oMov = null)
    {
      $oMov->is_deleted = \Config::get('scsys.STATUS.DEL');
      $oMov->updated_by_id = \Auth::user()->id;

      $lReferencedMovs = SMovement::where('src_mvt_id', $oMov->id_mvt)
                                  ->where('is_deleted', false)
                                  ->get();

        foreach ($lReferencedMovs as $mov) {
          $mov->is_deleted = \Config::get('scsys.STATUS.DEL');
          $mov->updated_by_id = \Auth::user()->id;
          $errors = $this->deleteMov($mov);

          if (is_array($errors) && sizeof($errors) > 0) {
             return $errors;
          }
        }

        $errors = $this->deleteMov($oMov);
        if (is_array($errors) && sizeof($errors) > 0) {
           return $errors;
        }

        return true;
    }

    /**
     * activate the flag is_deleted of movement
     *
     * @param  SMovement $oMov
     *
     * @return boolean or array
     */
    private function deleteMov($oMov)
    {
      try
      {
        $errors = \DB::connection('company')->transaction(function() use ($oMov) {
          $aErrors = $oMov->save();
          if (is_array($aErrors) && sizeof($aErrors) > 0) {
             return $aErrors;
          }

          $request = new Request();
          $stkController = new SStockController();
          $stkController->store($request, $oMov);
        });

        if (is_array($errors) && sizeof($errors) > 0) {
           return $errors;
        }
      }
      catch (\Exception $e)
      {
         return [$e];
      }
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
    private function stockToMovements($lStock = [], $iYearId = 0, $sDate = '')
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
              $oMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN');
              $oMovement->mvt_whs_type_id = \Config::get('scwms.MVT_TP_IN_ADJ');
              $oMovement->mvt_trn_type_id = 1;
              $oMovement->mvt_adj_type_id = \Config::get('scwms.MVT_ADJ_TP_IFI');
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

    public function generatePhysicalInventory($oMovement = null, $lRows = [])
    {
        $sSelect = 'sum(ws.input) as inputs,
                     sum(ws.output) as outputs,
                     (sum(ws.input) - sum(ws.output)) as stock,
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
        $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oMovement->whs_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oMovement->branch_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.WITHOUT_SEGREGATED')] = true;

        $lStock = session('stock')->getStockResult($aParameters);

        $lStock = $lStock->groupBy('id_item')
                            ->groupBy('id_unit')
                            ->groupBy('lot_id')
                            ->groupBy('pallet_id')
                            ->groupBy('location_id')
                            ->get();

        foreach ($lStock as $oStock) {
          $oStock->dAdded = 0;
        }

        return true;
    }
}
