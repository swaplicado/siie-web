<?php namespace App\SCore;

/**
 *
 */
class SSegregationCore
{

  /**
   * Segregate unit from movement object
   *
   * @param  Request $request
   * @param  SMovement  $oMovement
   * @param  int  $iSegregationType can be quality, production or shipment
   */
  public function segregate(Request $request, $oMovement, $iSegregationType)
  {
      $this->processSegregation($oMovement, $iSegregationType, false, \Config::get('scqms.TO_EVALUATE'));
  }

  /**
   * Change the quality status of the units.
   *
   * @param  Request $request
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
  public function classify(Request $request, $aParameters)
  {
      \DB::connection('company')->transaction(function() use ($aParameters, $request) {

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

        $oSegregation = new SSegregation();

        $oSegregation->dt_date = session('work_date')->format('Y-m-d');
        $oSegregation->is_deleted = false;
        $oSegregation->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.QUALITY');
        $oSegregation->reference_id = $iIdDocument;
        $oSegregation->created_by_id = \Auth::user()->id;
        $oSegregation->updated_by_id = \Auth::user()->id;

        $oSegregation->save();

        $oSegRow = new SSegregationRow();

        $oSegRow->quantity = $iIdLot > 1 ? 0 : $dQuantity;
        $oSegRow->move_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
        $oSegRow->pallet_id = $iIdPallet;
        $oSegRow->whs_id = $iIdWhs;
        $oSegRow->branch_id = $iIdBranch;
        $oSegRow->year_id = session('work_year');
        $oSegRow->item_id = $iIdItem;
        $oSegRow->unit_id = $iIdUnit;
        $oSegRow->quality_status_id = $iIdQltyPrev;

        if (! session('segregation')->isRelease($iIdQltyNew)) {
          $oSegRowMirror = clone $oSegRow;
          $oSegRowMirror->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
          $oSegRowMirror->quality_status_id = $iIdQltyNew;

          $oSegregation->rows()->save($oSegRowMirror);
        }

        $oSegregation->rows()->save($oSegRow);

        $oSegLotRow = new SSegregationLotRow();

        if ($iIdLot > 1) {
          $oSegLotRow->quantity = $dQuantity;
          $oSegLotRow->move_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
          $oSegLotRow->lot_id = $iIdLot;
          $oSegLotRow->year_id = 1;
          $oSegLotRow->item_id = $iIdItem;
          $oSegLotRow->unit_id = $iIdUnit;
          $oSegLotRow->quality_status_id = $iIdQltyPrev;

          if (! session('segregation')->isRelease($iIdQltyNew)) {
            $oSegLotRowMirror = clone $oSegLotRow;
            $oSegLotRowMirror->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
            $oSegLotRowMirror->quality_status_id = $iIdQltyNew;

            $oSegRowMirror->lotRows()->save($oSegLotRowMirror);
          }
          $oSegRow->lotRows()->save($oSegLotRow);
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
                  qqs.id_status,
                  ei.code as item_code,
                  ei.name as item,
                  eu.code as unit,
                  wsr.quantity AS qty,
                  COALESCE(wl.lot, \'N/A\') AS lot_name,
                  SUM(IF(wsr.move_type_id = 1, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) AS increment,
    SUM(IF(wsr.move_type_id = 2, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) AS decrement,
    SUM(IF(wsr.move_type_id = 1, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) - SUM(IF(wsr.move_type_id = 2, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) AS segregated,
                  wp.pallet,
                  ww.name AS warehouse,
                  qqs.name AS status_qlty,
                  qqs.id_status,
                  ed.num AS num_doc';

      $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_segregations AS ws')
                  ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                  ->leftJoin('wms_seg_lot_rows AS wslr', 'wsr.id_segregation_row', '=', 'wslr.segregation_row_id')
                  ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                  ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                  ->leftJoin('wms_lots AS wl', 'wslr.lot_id', '=', 'wl.id_lot')
                  ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                  ->join('wmsu_whs AS ww', 'wsr.whs_id', '=', 'ww.id_whs')
                  ->join('qmss_quality_status AS qqs', 'wsr.quality_status_id', '=', 'qqs.id_status')
                  ->join('erpu_documents AS ed', 'ws.reference_id', '=', 'ed.id_document')
                  ->where('ei.is_deleted', false)
                  ->where('ws.is_deleted', false)
                  ->where('ws.segregation_type_id', $iSegregationType)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('id_item',
                            'id_unit',
                            'id_lot',
                            'id_pallet',
                            'ww.id_whs',
                            'qqs.id_status'
                            )
                  ->having('segregated', '>', 0);

      switch ($iQualityType) {
        case \Config::get('scqms.QMS_VIEW.BY_STATUS'):
        case \Config::get('scqms.QMS_VIEW.CLASSIFY'):
          $query = $query->get();
          break;
        case \Config::get('scqms.QMS_VIEW.INSPECTION'):
          $query = $query->where('wsr.quality_status_id', \Config::get('scqms.QUARANTINE'))
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
