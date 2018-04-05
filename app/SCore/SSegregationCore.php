<?php namespace App\SCore;

use App\WMS\Segregation\SSegregation;
use App\WMS\Segregation\SSegregationRow;
use App\WMS\Segregation\SSegregationLotRow;

/**
 *
 */
class SSegregationCore
{

  /**
   * Segregate unit from movement object
   *
   * @param  SMovement  $oMovement
   * @param  int  $iSegregationType can be quality, production or shipment
   */
  public function segregate($oMovement, $iSegregationType)
  {
      $this->processSegregation($oMovement, $iSegregationType, false, \Config::get('scqms.TO_EVALUATE'));
  }

  /**
   * Change the quality status of the units.
   *
   * @param  [array] $aParameters [
       * \Config::get('scwms.STOCK_PARAMS.ITEM')
       * \Config::get('scwms.STOCK_PARAMS.UNIT')
       * \Config::get('scwms.STOCK_PARAMS.LOT')
       * \Config::get('scwms.STOCK_PARAMS.PALLET')
       * \Config::get('scwms.STOCK_PARAMS.LOCATION')
       * \Config::get('scwms.STOCK_PARAMS.WHS')
       * \Config::get('scwms.STOCK_PARAMS.BRANCH')
       * \Config::get('scwms.STOCK_PARAMS.ID_YEAR')
       * \Config::get('scwms.STOCK_PARAMS.DATE')
       * ]
   */
  public function classify($aParameters)
  {
      \DB::connection('company')->transaction(function() use ($aParameters) {

        $iIdItem = $aParameters[\Config::get('scwms.SEG_PARAM.ID_ITEM')];
        $iIdUnit = $aParameters[\Config::get('scwms.SEG_PARAM.ID_UNIT')];
        $iIdLot = $aParameters[\Config::get('scwms.SEG_PARAM.ID_LOT')];
        $iIdPallet = $aParameters[\Config::get('scwms.SEG_PARAM.ID_PALLET')];
        $iIdWhs = $aParameters[\Config::get('scwms.SEG_PARAM.ID_WHS')];
        $iIdBranch = $aParameters[\Config::get('scwms.SEG_PARAM.ID_BRANCH')];
        $iIdDocument = $aParameters[\Config::get('scwms.SEG_PARAM.ID_DOCUMENT')];
        $iIdQltyPrev = $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')];
        $iIdQltyNew = $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')];
        $dQuantity = $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')];
        $idEvent = $aParameters[10];
        $idLoc = $aParameters[11];
        $idWarehouse = $aParameters[12];
        $idLocNew = $aParameters[13];

        $oSegregation = new SSegregation();
        $oSegRow = new SSegregationRow();

        $oSegregation->dt_date = session('work_date')->format('Y-m-d');
        $oSegregation->is_deleted = false;

        switch ($iIdQltyPrev) {
          case 3:
                $oSegregation->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.INSPECTED');
                break;
          case 4:
                $oSegregation->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE');
                break;
          case 5:
          case 6:
          case 7:
          case 1:
          case 2:
          case 8:
          case 9:
          case 10:
                break;
        }


        $oSegregation->reference_id = $iIdDocument;
        $oSegregation->created_by_id = \Auth::user()->id;
        $oSegregation->updated_by_id = \Auth::user()->id;
        //Clonar el renglon ingresado para hacer el espejo
        $oSegregationMirror = clone $oSegregation;
        //Guardar primer renglon
        $oSegregation->save();

        //Renglon de segregacion de la primera entrada

        $oSegRow->quantity = $dQuantity;
        $oSegRow->is_deleted = false;
        $oSegRow->segregation_id = $oSegregation->id_segregation;
        $oSegRow->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');

        switch ($iIdQltyNew) {
          case 1:
          case 2:
          case 3:
          case 4:
                $oSegRow->segregation_event_id = $idEvent;
                break;
          case 5:
          case 6:
          case 7:
          case 8:
          case 9:
          case 10:
                $oSegRow->segregation_event_id = $iIdQltyNew;
                break;
        }

        $oSegRow->branch_id = $iIdBranch;
        $oSegRow->whs_id = $iIdWhs;
        $oSegRow->whs_location_id = $idLoc;
        $oSegRow->pallet_id = $iIdPallet;
        $oSegRow->lot_id = $iIdLot;
        $oSegRow->year_id = session('work_year');
        $oSegRow->item_id = $iIdItem;
        $oSegRow->unit_id = $iIdUnit;
        $oSegRow->created_by_id = \Auth::user()->id;
        $oSegRow->updated_by_id = \Auth::user()->id;
        //Clonar el renglon para hacer el espejo
        $oSegRowMirror = clone $oSegRow;
        //Guardado primer renglon
        $oSegRow->save();

        switch ($iIdQltyNew) {
          case 3:
                $oSegregationMirror->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.INSPECTED');

                $oSegregationMirror->save();

                //Insertar segundo registro contrario al Anterior
                $oSegRowMirror->segregation_id = $oSegregationMirror->id_segregation;
                $oSegRowMirror->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
                $oSegRowMirror->segregation_event_id = $iIdQltyNew;
                $oSegRowMirror->save();
                break;
          case 4:
                $oSegregationMirror->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE');

                $oSegregationMirror->save();

                //Insertar segundo registro contrario al Anterior
                $oSegRowMirror->segregation_id = $oSegregationMirror->id_segregation;
                $oSegRowMirror->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
                $oSegRowMirror->segregation_event_id = $iIdQltyNew;
                $oSegRowMirror->save();
                break;
          case 1:
          case 2:
          case 5:
          case 6:
          case 7:
                break;
          case 8:
          case 9:
          case 10:
                $oMovement = new SMovement();
                $lDocData = array();
                $lStock = null;
                $oDocument = 0;
                $oMovType = SMvtType::find($mvtType);
                $iMvtSubType = 1;
                $oMovement->mvt_whs_class_id = $oMovType->mvt_class_id;
                $oMovement->mvt_whs_type_id = $oMovType->id_mvt_type;

                $movTypes = SMvtType::where('is_deleted', false)
                                      ->where('id_mvt_type', $oMovement->mvt_whs_type_id)
                                      ->lists('name', 'id_mvt_type');

                $warehouses = SWarehouse::where('is_deleted', false)
                                        ->where('branch_id', session('branch')->id_branch)
                                        ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as warehouse"))
                                        ->orderBy('name', 'ASC')
                                        ->lists('warehouse', 'id_whs');

                $iWhsSrc = 0;
                $iWhsDes = 0;
                break;
        }

      });

  }

  /**
   * release the units of a segregation from a movement
   *
   * @param  Request $request
   * @param  SMovement  $oMovement
   * @param  int  $iSegregationType
   * @param  int  $iQltyStatus
   */
  public function release(Request $request, $oMovement, $iSegregationType, $iQltyStatus)
  {
      $this->processSegregation($oMovement, $iSegregationType, true, $iQltyStatus);
  }

  /**
   * Segregate or release units depend of the boolean var $bIsRelease
   * From a movement
   *
   * @param  SMovement $oMovement
   * @param  int $iSegregationType
   * @param  boolean $bIsRelease
   * @param  int $iQltyStatus
   */
  public function processSegregation($oMovement, $iSegregationType, $bIsRelease, $iQltyStatus)
  {
    \DB::connection('company')->transaction(function() use ($oMovement, $iSegregationType, $bIsRelease, $iQltyStatus) {
      $oSegregation = new SSegregation();

      $oSegregation->dt_date = $oMovement->dt_date;
      $oSegregation->is_deleted = false;
      $oSegregation->segregation_type_id = $iSegregationType; // Pendiente constantes

      $iReference = 1;
      if ($oMovement->doc_order_id > 1) {
        $iReference = $oMovement->doc_order_id;
      }
      elseif ($oMovement->doc_invoice_id > 1) {
        $iReference = $oMovement->doc_invoice_id;
      }
      elseif ($oMovement->doc_debit_note_id > 1) {
        $iReference = $oMovement->doc_debit_note_id;
      }
      elseif ($oMovement->doc_credit_note_id > 1) {
        $iReference = $oMovement->doc_credit_note_id;
      }

      $oSegregation->reference_id = $iReference;
      $oSegregation->created_by_id = \Auth::user()->id;
      $oSegregation->updated_by_id = \Auth::user()->id;
      $oSegregation->save();

      foreach ($oMovement->rows as $movRow) {
        $oSegRow = new SSegregationRow();

        $oSegRow->quantity = sizeof($movRow->lotRows) > 0 ? 0 : $movRow->quantity;
        if ($bIsRelease) {
            $oSegRow->move_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
        }
        else {
            $oSegRow->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
        }

        $oSegRow->pallet_id = $movRow->pallet_id;
        $oSegRow->whs_id = $oMovement->whs_id;
        $oSegRow->branch_id = $oMovement->branch_id;
        $oSegRow->year_id = session('work_year');
        $oSegRow->item_id = $movRow->item_id;
        $oSegRow->unit_id = $movRow->unit_id;
        $oSegRow->quality_status_id = $iQltyStatus;

        $oSegregation->rows()->save($oSegRow);

        foreach ($movRow->lotRows as $lotRow) {
          $oSegLotRow = new SSegregationLotRow();

          $oSegLotRow->quantity = $lotRow->quantity;
          if ($bIsRelease) {
              $oSegLotRow->move_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
          }
          else {
              $oSegLotRow->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
          }
          $oSegLotRow->lot_id = $lotRow->lot_id;
          $oSegLotRow->year_id = session('work_year');
          $oSegLotRow->item_id = $movRow->item_id;
          $oSegLotRow->unit_id = $movRow->unit_id;
          $oSegLotRow->quality_status_id = $iQltyStatus;

          $oSegRow->lotRows()->save($oSegLotRow);
        }
      }
    });
  }

  /**
   * get the query of segregated with data of items, units, pallets,
   * warehouses, lots, status and documents
   *
   * @param  int $iSegregationType
   * @return query of segregated
   */
  public function getSegregated($iSegregationType = 0, $iQualityType = 0)
  {
      $sSelect = '
                  ei.id_item,
                  eu.id_unit,
                  wl.id_lot,
                  wp.id_pallet,
                  ww.id_whs,
                  ww.branch_id,
                  ed.id_document,
                  qse.id_segregation_event,
                  ws.segregation_type_id,
                  ei.code as item_code,
                  ei.name as item,
                  eu.code as unit,
                  wsr.quantity AS qty,
                  wsr.whs_location_id,
                  COALESCE(wl.lot, \'N/A\') AS lot_name,
                  SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) AS increment,
                  SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS decrement,
                  SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) - SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS segregated,
                  wp.pallet,
                  ww.name AS warehouse,
                  qse.name AS status_qlty,
                  ed.num AS num_doc';

      $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_segregations AS ws')
                  ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                  ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                  ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                  ->leftJoin('wms_lots AS wl', 'wsr.lot_id', '=', 'wl.id_lot')
                  ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                  ->join('wmsu_whs AS ww', 'wsr.whs_id', '=', 'ww.id_whs')
                  ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event')
                  ->join('erpu_documents AS ed', 'ws.reference_id', '=', 'ed.id_document')
                  ->where('ei.is_deleted', false)
                  ->where('ws.is_deleted', false)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('id_item',
                            'id_unit',
                            'id_lot',
                            'id_pallet',
                            'ww.id_whs'
                            )
                  ->having('segregated', '>', 0);
      switch ($iQualityType) {
        case \Config::get('scqms.QMS_VIEW.BY_STATUS'):
        case \Config::get('scqms.QMS_VIEW.CLASSIFY'):
          $query = $query->get();
          break;
        case \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'):
          $query = $query->where('ws.segregation_type_id', \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'))
                          ->get();
          break;
        case \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'):
          $query = $query->where('ws.segregation_type_id', \Config::get('scqms.SEGREGATION_TYPE.QUARANTINE'))
                        ->get();
        break;
        default:
          break;
      }

      return $query;
  }

  public function isRelease($iStatus)
  {
      return $iStatus == \Config::get('scqms.PARTIAL_RELEASED') ||
              $iStatus == \Config::get('scqms.RELEASED') ||
                $iStatus == \Config::get('scqms.RELEASED_EARLY');
  }


}
