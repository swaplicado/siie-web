<?php namespace App\SCore;

use Illuminate\Http\Request;

use App\WMS\Segregation\SSegregation;
use App\WMS\Segregation\SSegregationRow;
use App\WMS\Segregation\SSegregationLotRow;
use App\SUtils\SGuiUtils;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\Database\Config;

use App\SCore\SMovsManagment;

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
      $this->processSegregation($oMovement, $iSegregationType, \Config::get('scqms.BYINSPECTING'));
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
        $iIdReference = $aParameters[\Config::get('scwms.SEG_PARAM.ID_REFERENCE')];
        $iIdQltyPrev = $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')];
        $iIdQltyNew = $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')];
        $dQuantity = $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')];
        $idEvent = $aParameters[\Config::get('scwms.SEG_PARAM.EVENT')];

        if ($iIdQltyNew == \Config::get('scqms.RECONDITION') ||
              $iIdQltyNew == \Config::get('scqms.REPROCESS') ||
                $iIdQltyNew == \Config::get('scqms.DESTROY')) {
          $idWarehouse = $aParameters[\Config::get('scwms.SEG_PARAM.WAREHOUSE')];
          $idLocNew = $aParameters[\Config::get('scwms.SEG_PARAM.LOCATION')];
        }

        if($dQuantity ==0){
              $LSegregation = session('segregation')->segregatePallet($iIdPallet,$iIdWhs,$idEvent);
              foreach ($LSegregation as $seg) {
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
              }
              $oSegregation->reference_id = $iIdReference;
              $oSegregation->created_by_id = \Auth::user()->id;
              $oSegregation->updated_by_id = \Auth::user()->id;
              //Clonar el renglon ingresado para hacer el espejo
              $oSegregationMirror = clone $oSegregation;
              //Guardar primer renglon
              $oSegregation->save();

              //Renglon de segregacion de la primera entrada

              $oSegRow->quantity = $seg->qty;
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
                default:
                      $oSegRow->segregation_event_id = 0;
              }

              $oSegRow->branch_id = $seg->branch_id;
              $oSegRow->whs_id = $seg->id_whs;
              $oSegRow->pallet_id = $seg->id_pallet;
              $oSegRow->lot_id = $seg->id_lot;
              $oSegRow->year_id = session('work_year');
              $oSegRow->item_id = $seg->id_item;
              $oSegRow->unit_id = $seg->id_unit;
              $oSegRow->created_by_id = \Auth::user()->id;
              $oSegRow->updated_by_id = \Auth::user()->id;
              //Clonar el renglon para hacer el espejo
              $oSegRowMirror = clone $oSegRow;
              //Guardado primer renglon
              $oSegRow->save();
              $idOriginLoc = session('segregation')->originLocation($seg->id_whs);
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
                      $oMovement->mvt_whs_class_id = 2;
                      $oMovement->mvt_whs_type_id = 11;
                      $oMovement->mvt_trn_type_id = 1;
                      $oMovement->mvt_adj_type_id = 1;
                      $oMovement->mvt_mfg_type_id = 1;
                      $oMovement->mvt_exp_type_id = 1;
                      $oMovement->branch_id = session('branch')->id_branch;
                      $oMovement->whs_id = $seg->id_whs;
                      $oMovement->year_id = session('work_year');
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

                      $oMovementRow = new SMovementRow();
                      $oMovementRow->quantity = $seg->qty;
                      $oMovementRow->item_id = $seg->id_item;
                      $oMovementRow->unit_id = $seg->id_unit;
                      $oMovementRow->pallet_id = $iIdPallet;
                      $oMovementRow->location_id = $idOriginLoc;
                      $oMovementRow->doc_order_row_id = 1;
                      $oMovementRow->doc_invoice_row_id = 1;
                      $oMovementRow->doc_debit_note_row_id = 1;
                      $oMovementRow->doc_credit_note_row_id = 1;
                      $oMovementRow->iAuxLocationDesId = $idLocNew;


                      if($iIdLot != 1){
                      $oMovementRowLots = new SMovementRowLot();
                      $oMovementRowLots->quantity = $seg->qty;
                      $oMovementRowLots->lot_id = $seg->id_lot;
                      $oMovementRow->setAuxLots([$oMovementRowLots]);
                      }

                      $oProcess = new SMovsManagment();
                      $iWhsSrc = $seg->id_whs;
                      $iWhsDes = $idWarehouse;

                      $request = new Request();
                      $oProcess->processTheMovement(\Config::get('scwms.OPERATION_TYPE.CREATION'),
                                                      $oMovement,
                                                      [$oMovementRow],
                                                      $oMovement->mvt_whs_class_id,
                                                      $oMovement->mvt_whs_type_id,
                                                      $iWhsSrc,
                                                      $iWhsDes,
                                                      null, $request);
                      break;
              }


        }

        else{
          if($dQuantity <= session('segregation')->SegregateComprobation($iIdLot,$iIdPallet,$iIdItem,$iIdWhs,$iIdQltyPrev)){
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


          $oSegregation->reference_id = $iIdReference;
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
          $idOriginLoc = session('segregation')->originLocation($oSegRow->pallet_id);
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
                  $oMovement->mvt_whs_class_id = 2;
                  $oMovement->mvt_whs_type_id = 11;
                  $oMovement->mvt_trn_type_id = 1;
                  $oMovement->mvt_adj_type_id = 1;
                  $oMovement->mvt_mfg_type_id = 1;
                  $oMovement->mvt_exp_type_id = 1;
                  $oMovement->branch_id = session('branch')->id_branch;
                  $oMovement->whs_id = $iIdWhs;
                  $oMovement->year_id = session('work_year');
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

                  $oMovementRow = new SMovementRow();
                  $oMovementRow->quantity = $dQuantity;
                  $oMovementRow->item_id = $iIdItem;
                  $oMovementRow->unit_id = $iIdUnit;
                  $oMovementRow->pallet_id = $iIdPallet;
                  $oMovementRow->location_id = $idOriginLoc;
                  $oMovementRow->doc_order_row_id = 1;
                  $oMovementRow->doc_invoice_row_id = 1;
                  $oMovementRow->doc_debit_note_row_id = 1;
                  $oMovementRow->doc_credit_note_row_id = 1;
                  $oMovementRow->iAuxLocationDesId = $idLocNew;


                  if($iIdLot != 1){
                  $oMovementRowLots = new SMovementRowLot();
                  $oMovementRowLots->quantity = $dQuantity;
                  $oMovementRowLots->lot_id = $iIdLot;
                  $oMovementRow->setAuxLots([$oMovementRowLots]);
                  }

                  $oProcess = new SMovsManagment();
                  $iWhsSrc = $iIdWhs;
                  $iWhsDes = $idWarehouse;

                  $request = new Request();
                  $oProcess->processTheMovement(\Config::get('scwms.OPERATION_TYPE.CREATION'),
                                                  $oMovement,
                                                  [$oMovementRow],
                                                  $oMovement->mvt_whs_class_id,
                                                  $oMovement->mvt_whs_type_id,
                                                  $iWhsSrc,
                                                  $iWhsDes,
                                                  null, $request);
                  break;
          }
        }
        }
        });
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
  public function processSegregation($oMovement, $iSegregationType, $iQltyEvent)
  {
    \DB::connection('company')->transaction(function() use ($oMovement, $iSegregationType, $iQltyEvent) {
      $oSegregation = new SSegregation();

      $oSegregation->dt_date = $oMovement->dt_date;
      $oSegregation->is_deleted = false;
      $oSegregation->segregation_type_id = $iSegregationType; // Pendiente constantes

      $iReference = $oMovement->id_mvt;
      // if ($oMovement->doc_order_id > 1) {
      //   $iReference = $oMovement->doc_order_id;
      // }
      // elseif ($oMovement->doc_invoice_id > 1) {
      //   $iReference = $oMovement->doc_invoice_id;
      // }
      // elseif ($oMovement->doc_debit_note_id > 1) {
      //   $iReference = $oMovement->doc_debit_note_id;
      // }
      // elseif ($oMovement->doc_credit_note_id > 1) {
      //   $iReference = $oMovement->doc_credit_note_id;
      // }

      $oSegregation->reference_id = $iReference;
      $oSegregation->created_by_id = \Auth::user()->id;
      $oSegregation->updated_by_id = \Auth::user()->id;
      $oSegregation->save();

      foreach ($oMovement->rows as $movRow) {
        if (sizeof($movRow->lotRows) > 0) {
          $lSegRows = array();
          foreach ($movRow->lotRows as $lotRow) {
            $oSegRow = new SSegregationRow();

            $oSegRow->quantity = $lotRow->quantity;
            $oSegRow->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
            $oSegRow->segregation_event_id = $iQltyEvent;
            $oSegRow->branch_id = $oMovement->branch_id;
            $oSegRow->whs_id = $oMovement->whs_id;
            $oSegRow->pallet_id = $movRow->pallet_id;
            $oSegRow->lot_id = $lotRow->lot_id;
            $oSegRow->year_id = session('work_year');
            $oSegRow->item_id = $movRow->item_id;
            $oSegRow->unit_id = $movRow->unit_id;
            $oSegRow->created_by_id = \Auth::user()->id;
            $oSegRow->updated_by_id = \Auth::user()->id;

            array_push($lSegRows, $oSegRow);
          }

          $oSegregation->rows()->saveMany($lSegRows);
        }
        else {
          $oSegRow = new SSegregationRow();

          $oSegRow->quantity = $movRow->quantity;
          $oSegRow->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
          $oSegRow->segregation_event_id = $iQltyEvent;
          $oSegRow->branch_id = $oMovement->branch_id;
          $oSegRow->whs_id = $oMovement->whs_id;
          $oSegRow->pallet_id = $movRow->pallet_id;
          $oSegRow->lot_id = 1;
          $oSegRow->year_id = session('work_year');
          $oSegRow->item_id = $movRow->item_id;
          $oSegRow->unit_id = $movRow->unit_id;
          $oSegRow->created_by_id = \Auth::user()->id;
          $oSegRow->updated_by_id = \Auth::user()->id;

          $oSegregation->rows()->save($oSegRow);
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
  public function getSegregated($iSegregationType = 0, $iQualityType = 0, $typeView)
  {
      $sSelect = '
                  ei.id_item,
                  eu.id_unit,
                  wl.id_lot,
                  wp.id_pallet,
                  ww.id_whs,
                  ww.branch_id,
                  qse.id_segregation_event,
                  ws.segregation_type_id,
                  ei.code as item_code,
                  ei.name as item,
                  eu.code as unit,
                  wsr.quantity AS qty,
                  COALESCE(wl.lot, \'N/A\') AS lot_name,
                  SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) AS increment,
                  SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS decrement,
                  SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) - SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS segregated,
                  wp.pallet,
                  ww.name AS warehouse,
                  qse.name AS status_qlty,
                  ws.reference_id AS id_reference';

      $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_segregations AS ws')
                  ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                  ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                  ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                  ->leftJoin('wms_lots AS wl', 'wsr.lot_id', '=', 'wl.id_lot')
                  ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                  ->join('wmsu_whs AS ww', 'wsr.whs_id', '=', 'ww.id_whs')
                  ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event');

    $query = $query->where('ei.is_deleted', false)
                  ->where('ws.is_deleted', false)
                  ->select(\DB::raw($sSelect));
    switch ($typeView){
      case 0:
        $query = $query->groupBy('id_item',
                            'id_unit',
                            'id_lot',
                            'id_pallet',
                            'ww.id_whs'
                            )
                  ->having('segregated', '>', 0);
                  break;
      case 1:
        $query = $query->groupBy('id_pallet',
                            'ww.id_whs'
                            )
                  ->having('segregated', '>', 0)
                  ->where('wp.pallet', '!=', 'SIN TARIMA');
                  break;
      default:
        $query = $query->groupBy('id_item',
                          'id_unit',
                          'id_lot',
                          'id_pallet',
                          'ww.id_whs'
                          )
                ->having('segregated', '>', 0);
                break;
    }

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

  public function SegregateComprobation($lot,$pallet,$item,$warehouse,$iQualityType){
    $sSelect = 'SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) - SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS segregated';
    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                ->leftJoin('wms_lots AS wl', 'wsr.lot_id', '=', 'wl.id_lot')
                ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                ->join('wmsu_whs AS ww', 'wsr.whs_id', '=', 'ww.id_whs')
                ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event');

      $query = $query->where('ei.is_deleted', false)
                    ->where('ws.is_deleted', false)
                    ->where('wl.id_lot','=',$lot)
                    ->where('wp.id_pallet','=',$pallet)
                    ->where('ei.id_item','=',$item)
                    ->where('ww.id_whs','=',$warehouse)
                    ->groupBy('id_item',
                                     'id_unit',
                                     'id_lot',
                                     'id_pallet',
                                     'ww.id_whs'
                                     );
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
  public function segregatePallet($pallet,$warehouse,$iQualityType){
    $sSelect = '
                ei.id_item,
                eu.id_unit,
                wl.id_lot,
                wp.id_pallet,
                ww.id_whs,
                ww.branch_id,
                qse.id_segregation_event,
                ws.segregation_type_id,
                ei.code as item_code,
                ei.name as item,
                eu.code as unit,
                wsr.quantity AS qty,
                COALESCE(wl.lot, \'N/A\') AS lot_name,
                SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) AS increment,
                SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS decrement,
                SUM(IF(wsr.segregation_mvt_type_id = 1, wsr.quantity, 0)) - SUM(IF(wsr.segregation_mvt_type_id = 2, wsr.quantity, 0)) AS segregated,
                wp.pallet,
                ww.name AS warehouse,
                qse.name AS status_qlty,
                ws.reference_id AS id_reference';

    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                ->leftJoin('wms_lots AS wl', 'wsr.lot_id', '=', 'wl.id_lot')
                ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                ->join('wmsu_whs AS ww', 'wsr.whs_id', '=', 'ww.id_whs')
                ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event');

  $query = $query->where('ei.is_deleted', false)
                ->where('ws.is_deleted', false)
                ->where('wp.id_pallet','=',$pallet)
                ->select(\DB::raw($sSelect));
  $query = $query->groupBy('id_pallet',
                          'ww.id_whs'
                          )
                ->having('segregated', '>', 0)
                ->where('wp.pallet', '!=', 'SIN TARIMA');

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
  public function segregatebinnacle($sFilterDate,$lot,$pallet,$item,$user,$segregationEvent){
    $sSelect = '
                ei.id_item as item_code,
                ei.name as item,
                eu.code as unit,
                COALESCE(wl.lot, \'N/A\') AS lot_name,
                wp.pallet,
                qse.name as event,
                wsr.updated_at as date,
                wsr.quantity AS qty,
                us.username AS username' ;


    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                ->leftJoin('wms_lots AS wl', 'wsr.lot_id', '=', 'wl.id_lot')
                ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event')
                ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users AS us', 'wsr.updated_by_id', '=', 'us.id');
  $query = $query->where('ei.is_deleted', false)
                ->where('ws.is_deleted', false)
                ->select(\DB::raw($sSelect));


if($sFilterDate != 0) {
    $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);
    $query = $query->whereBetween('wsr.updated_at',[$aDates[0]->toDateString(),$aDates[1]->toDateString()]);
}

if($item != 0) {
   $query = $query->where('ei.id_item','=',$item);
}

if($lot != 0) {
   $query = $query->where('wl.id_lot','=',$lot);
}

if($pallet != 0) {
  $query = $query->where('wp.id_pallet','=',$pallet);
}

if($segregationEvent != 0) {
  $query = $query->where('qse.id_segregation_event','=',$segregationEvent);
}

if($user != 0){
  $query = $query->where('us.id','=',$user);
}
    $query = $query->get();

    return $query;
}

  public function binnacleItem() {
    $sSelect = '
                ei.id_item as item_code,
                ei.name as item';


    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                ;

  $query = $query->where('ei.is_deleted', false)
                ->where('ws.is_deleted', false)
                ->select(\DB::raw($sSelect))
                ->groupBy('id_item')
                ->get();
  return $query;
  }

  public function binnacleLot() {
    $sSelect = '
                id_lot,
                lot';


    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->leftJoin('wms_lots AS wl', 'wsr.lot_id', '=', 'wl.id_lot');

  $query = $query->where('ws.is_deleted', false)
                ->select(\DB::raw($sSelect))
                ->groupBy('id_lot')
                ->get();
  return $query;
  }

  public function binnaclePallet() {
    $sSelect = '
                wp.id_pallet,
                wp.pallet';


    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet');

  $query = $query->where('ws.is_deleted', false)
                ->select(\DB::raw($sSelect))
                ->groupBy('id_pallet')
                ->get();
  return $query;
  }

  public function binnacleUser() {
    $sSelect = '
                  us.id AS id_user,
                  us.username AS username' ;


      $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_segregations AS ws')
                  ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                  ->join('ssystem.users AS us', 'wsr.updated_by_id', '=', 'us.id');
    $query = $query->where('ws.is_deleted', false)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('id_user')
                  ->get();
    return $query;
  }

  public function binnacleEvent() {
    $sSelect = '
                qse.id_segregation_event,
                qse.name as event';

    $query = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('wms_segregations AS ws')
                ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event');

  $query = $query->where('ws.is_deleted', false)
                ->select(\DB::raw($sSelect))
                ->groupBy('id_segregation_event')
                ->get();
  return $query;
  }

  public function isRelease($iStatus)
  {
      return $iStatus == \Config::get('scqms.PARTIAL_RELEASED') ||
              $iStatus == \Config::get('scqms.RELEASED') ||
                $iStatus == \Config::get('scqms.RELEASED_EARLY');
  }

  public function originLocation($warehouse){
      $sSelect = 'id_whs_location';
      $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_whs_locations');
      $query = $query->where('whs_id','=',$warehouse)
                    ->select(\DB::raw($sSelect))
                    ->get();
      return $query;
  }



}
