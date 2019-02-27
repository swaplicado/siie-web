<?php namespace App\SCore;

use App\Http\Controllers\WMS\SStockController;
use App\Http\Controllers\QMS\SSegregationsController;

use App\WMS\SWarehouse;
use App\WMS\SWmsLot;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\WMS\SFolio;
use App\ERP\SDocument;
use App\ERP\SDocumentRow;
use App\WMS\SStock;
use App\WMS\SExternalTransfer;

use App\SUtils\SStockUtils;
use App\SUtils\SGuiUtils;
use App\SCore\SMovsCore;
use App\SCore\SProductionCore;

/**
 * this class manages the movement process
 */
class SMovsManagment {

    public function processTheMovement($iOperation, $oMovement, $aMovementRows, $iMvtClass,
                                            $iMvtType, $iWhsSrc, $iWhsDes, $iPallet, $iPalletLocation, $oRequest)
    {
        $movements = $this->processMovement($oMovement,
                                                    $aMovementRows,
                                                    $iMvtClass,
                                                    $iMvtType,
                                                    $iWhsSrc,
                                                    $iWhsDes,
                                                    $iPallet,
                                                    $iPalletLocation,
                                                    $iOperation);

        foreach ($movements as $mov) {
          foreach ($mov->aAuxRows as $row) {
            $aErrors = SMovsCore::canTheItemBeMoved($row->item_id, $mov->mvt_whs_type_id);

            if(is_array($aErrors) && sizeof($aErrors) > 0)
            {
              return $aErrors;
            }
          }

          if ($mov->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT')) {
            $aErrors = SStockUtils::validateStock($mov);

            if(sizeof($aErrors) > 0)
            {
                return $aErrors;
            }

            foreach ($mov->aAuxRows as $row) {
              $aErrors = SStockUtils::validatePallet($mov->year_id, $mov->branch_id, $mov->whs_id,
                                                      $row, $mov->mvt_whs_type_id, $mov->id_mvt);

              if(sizeof($aErrors) > 0)
              {
                return $aErrors;
              }
            }
          }
          else {
              foreach ($mov->aAuxRows as $row) {
                $aErrors = SStockUtils::validateInputPallet($row,
                                                          $mov->year_id,
                                                          $mov->mvt_whs_type_id,
                                                          $mov->id_mvt);

                if(sizeof($aErrors) > 0)
                {
                  return $aErrors;
                }
              }
          }
        }

        if ($iOperation == \Config::get('scwms.OPERATION_TYPE.CREATION')) {
          foreach ($movements as $mov) {

             $iFolio = $this->getNewFolio($mov->branch_id, $mov->whs_id, $mov->mvt_whs_class_id, $mov->mvt_whs_type_id);
             if ($iFolio > 0)
             {
                $mov->folio = $iFolio;
             }
             else
             {
               $aErrors[0] = "No hay un folio asignado para este tipo de movimiento";

               return $aErrors;
             }
          }
        }

        $this->saveMovement($movements, $oRequest, $iOperation);

        if ($movements[0]->whs_id == session('transit_whs')->id_whs) {
            if (isset($movements[1])) {
              return $movements[1]->folio;
            }
        }

        return $movements[0]->folio;
    }

    /**
     * [processMovement description]
     * @param  [App\WMS\SMovement] $oMovement
     * @param  [Array of App\WMS\SMovementRow] $aMovementRows
     *          [ this array contains an array of App\WMS\SMovementRowLot]
     * @param  [int] $iClass
     * @param  [int] $iMovType
     * @param  [int] $iWhsSrc
     * @param  [int] $iWhsDes
     * @return [array] [array of App\WMS\SMovement ready to save]
     */
    private function processMovement($oMovement, $aMovementRows, $iClass, $iMovType, $iWhsSrc, $iWhsDes, $iPallet, $iPalletLocation, $iOperation)
    {
      switch ($iMovType) {
          // The movement is adjust or input by purchases
        case \Config::get('scwms.MVT_TP_IN_ADJ'):
        case \Config::get('scwms.MVT_TP_OUT_ADJ'):
        case \Config::get('scwms.MVT_TP_IN_PUR'):
        case \Config::get('scwms.MVT_TP_IN_SAL'):
        case \Config::get('scwms.MVT_TP_OUT_SAL'):
        case \Config::get('scwms.MVT_IN_DLVRY_PP'):
        case \Config::get('scwms.MVT_OUT_CONSUMPTION'):
        case \Config::get('scwms.MVT_IN_DLVRY_FP'):
          return $this->createTheMovement($oMovement, $aMovementRows);

          // The movement is trasfer
        case \Config::get('scwms.MVT_TP_OUT_TRA'):
        case \Config::get('scwms.MVT_OUT_DLVRY_RM'):
        case \Config::get('scwms.MVT_OUT_RTRN_RM'):
        case \Config::get('scwms.MVT_OUT_ASSIGN_PP'):
          return $this->createTransfer($oMovement, $aMovementRows, $iWhsSrc, $iWhsDes, $iOperation);

          // the movement is pallet reconfiguration (pallet division)
        case \Config::get('scwms.PALLET_RECONFIG_IN'):
          return $this->divisionOfPallet($oMovement, $iPallet, $iPalletLocation, $aMovementRows);

          // the movement is pallet reconfiguration (add to pallet)
        case \Config::get('scwms.PALLET_RECONFIG_OUT'):
          return $this->addToPallet($oMovement, $iPallet, $iPalletLocation, $aMovementRows);

          // process of physical inventory
        case \Config::get('scwms.PHYSICAL_INVENTORY'):
          $inventoryCore = new SInventoryCore();
          $oResult = $inventoryCore->generatePhysicalInventory($oMovement, $aMovementRows);
          return [];
          break;

        default:
          // code...
          break;
      }
    }

    private function saveMovement($movements, $oRequest, $iOperation)
    {
        try
        {
          \DB::connection('company')->transaction(function() use ($movements, $iOperation, $oRequest) {
            $i = 0;
            $iSrcId = 0;
            $lSavedMovements = array();

            foreach ($movements as $mov) {
                $movement = clone $mov;

                if ($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION')) {
                    $res = $this->eraseMovement($movement->id_mvt);
                    if (! true) {
                       throw new \Exception($res, 1);
                    }
                }

                if ($i == 1) {
                  $movement->src_mvt_id = $iSrcId;
                }

                $movement->save();

                if ($i == 0) {
                  $iSrcId = $movement->id_mvt;
                }

                foreach ($mov->aAuxRows as $movRow) {
                  if (! $movRow->is_deleted) {
                    $row = clone $movRow;
                    $row->mvt_id = $movement->id_mvt;
                    $row->save();

                    foreach ($movRow->getAuxLots() as $lotRow) {
                       $lRow = clone $lotRow;
                       $lRow->mvt_row_id = $row->id_mvt_row;
                       $lRow->save();
                    }
                  }
                }

                $movement = SMovement::find($movement->id_mvt);
                foreach ($movement->rows as $row) {
                  $row->lotRows;
                }

                $stkController = new SStockController();
                $stkController->store($oRequest, $movement);

                if (SGuiUtils::isProductionMovement($movement->mvt_whs_type_id)) {
                  $bRes = SProductionCore::managePoPallet($movement->mvt_whs_type_id,
                                                              $movement->prod_ord_id,
                                                              $movement->rows);
                }

                if ($movement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
                  if ($movement->warehouse->is_quality) {
                      session('segregation')->segregate($movement, \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'), $iOperation);
                  }
                }

                $i++;
                array_push($lSavedMovements, $movement);
            }

            $this->createExternalTransfer($lSavedMovements, $iOperation, $movements[0]->iAuxBranchDes);
          });
       }
       catch (\Exception $e)
       {
           \Log::error($e);
       }
    }

    public function saveLots($lNewLots = []) {
      try {
          $lKeys = array();

          $lKeys = \DB::connection('company')->transaction(function() use ($lNewLots, $lKeys) {
            foreach ($lNewLots as $key => $oLotJs) {
              $oLot = new SWmsLot();

              $oLot->lot = $oLotJs->lot;
              $oLot->dt_expiry = $oLotJs->dt_expiry;
              $oLot->item_id = $oLotJs->item_id;
              $oLot->unit_id = $oLotJs->unit_id;
              $oLot->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
              $oLot->created_by_id = \Auth::user()->id;
              $oLot->updated_by_id = \Auth::user()->id;

              $oLot->save();
              $lKeys[$key] = $oLot;
            }

            return $lKeys;
          });
      }
      catch (\Exception $e) {
          \Log::error($e);
      }
      finally {
          return $lKeys;
      }
    }

    /**
     * get a new folio for the movement
     * if the folio was not found return 0
     *
     * @param  integer $iBranchId       [description]
     * @param  integer $iWhsId          [description]
     * @param  integer $iClassId        [description]
     * @param  integer $iMovementTypeId [description]
     *
     * @return int new folio
     */
    public function getNewFolio($iBranchId = 0, $iWhsId = 0, $iClassId = 0, $iMovementTypeId = 0)
    {
      // define the base query to search the max folio for a configuration
      $baseQuery = \DB::connection(session('db_configuration')->getConnCompany())
                   ->table('wms_mvts as wm')
                   ->select(\DB::raw('MAX(folio) as max_folio, count(*) as num_movs'))
                   ->where('is_deleted', false)
                   ->orderBy('max_folio', 'desc');

       // get all folio configurations that are not deleted
       $lFolios = SFolio::where('is_deleted', false)->get();

       // look for a configuration to warehouse
       $oRequiredFolio = SMovsManagment::existsFolioConfiguration($lFolios,
                                                 \Config::get('scwms.CONTAINERS.WAREHOUSE'),
                                                 $iWhsId,
                                                 $iClassId,
                                                 $iMovementTypeId);

       // if the configuration was found look for the max folio with this configuration
       // if the query returns a empty result the function return the folio configured as initial
       if ($oRequiredFolio != null) {
           $lFolios = $baseQuery->where('mvt_whs_class_id', $iClassId)
                         ->where('mvt_whs_type_id', $iMovementTypeId)
                         ->where('branch_id', $iBranchId)
                         ->where('whs_id', $iWhsId)
                         ->where('folio', '>=', $oRequiredFolio->folio_start)
                         ->take(1)
                         ->get();

           if ($lFolios[0]->num_movs > 0) {
             return $lFolios[0]->max_folio + 1;
           }

           return $oRequiredFolio->folio_start;
       }
        // look for a configuration to branch
       else {
           $oRequiredFolio = $this->existsFolioConfiguration($lFolios,
                                                     \Config::get('scwms.CONTAINERS.BRANCH'),
                                                     $iBranchId,
                                                     $iClassId,
                                                     $iMovementTypeId);

           // if the configuration was found look for the max folio with this configuration
           // if the query returns a empty result the function return the folio configured as initial
           if ($oRequiredFolio != null) {
               $lFolios = $baseQuery->where('mvt_whs_class_id', $iClassId)
                             ->where('mvt_whs_type_id', $iMovementTypeId)
                             ->where('branch_id', $iBranchId)
                             ->where('folio', '>=', $oRequiredFolio->folio_start)
                             ->take(1)
                             ->get();

               if ($lFolios[0]->num_movs > 0) {
                 return $lFolios[0]->max_folio + 1;
               }

               return $oRequiredFolio->folio_start;
           }
           // look for a configuration to company for movement type and movement class
           else {
               $oRequiredFolio = $this->existsFolioConfiguration($lFolios,
                                                         0,
                                                         0,
                                                         $iClassId,
                                                         $iMovementTypeId);
               // if the configuration was found look for the max folio with this configuration
               // if the query returns a empty result the function return the folio configured as initial
               if ($oRequiredFolio != null)
               {
                   $lFolios = $baseQuery->where('mvt_whs_class_id', $iClassId)
                                 ->where('mvt_whs_type_id', $iMovementTypeId)
                                 ->where('folio', '>=', $oRequiredFolio->folio_start)
                                 ->take(1)
                                 ->get();

                   if ($lFolios[0]->num_movs > 0) {
                     return $lFolios[0]->max_folio + 1;
                   }

                   return $oRequiredFolio->folio_start;
               }
               // look for a configuration to movement class
               else {
                   $oRequiredFolio = $this->existsFolioConfiguration($lFolios,
                                                             0,
                                                             0,
                                                             $iClassId,
                                                             0);

                   // if the configuration was found look for the max folio with this configuration
                   // if the query returns a empty result the function return the folio configured as initial
                   if ($oRequiredFolio != null) {
                       $lFolios = $baseQuery->where('mvt_whs_class_id', $iClassId)
                                     ->where('folio', '>=', $oRequiredFolio->folio_start)
                                     ->take(1)
                                     ->get();

                       if ($lFolios[0]->num_movs > 0) {
                         return $lFolios[0]->max_folio + 1;
                       }

                       return $oRequiredFolio->folio_start;
                   }
                   // if no configuration was found return 0
                   else {
                     return 0;
                   }
               }
           }
       }
    }

    /**
     * determines if the configuration for the parameters exists in the list of
     * configurations
     *
     * @param  collection  $lFolios
     * @param  integer $iContainerType can be warehouse, branch, company
     * @param  integer $iContainerId
     * @param  integer $iMvtClass class of movement
     * @param  integer $iMvtType type of movement
     *
     * @return SFolio object configuration if the configuration was not found return null
     */
    private function existsFolioConfiguration($lFolios = null, $iContainerType = 0,
                                                      $iContainerId = 0, $iMvtClass = 0,
                                                      $iMvtType = 0)
    {
        $bConfigExists = false;
        $oRequiredFolio = '';
        foreach ($lFolios as $oFolio) {
           if ($oFolio->mvt_class_id == $iMvtClass) {
              if ($iMvtType != 0) {
                  if ($oFolio->mvt_type_id == $iMvtType) {
                      if ($iContainerType != 0) {
                          if ($oFolio->container_type_id == $iContainerType &&
                                    $oFolio->container_id == $iContainerId) {
                            $oRequiredFolio = $oFolio;
                            $bConfigExists = true;
                            break;
                          }
                      }
                      else {
                        $oRequiredFolio = $oFolio;
                        $bConfigExists = true;
                        break;
                      }
                  }
              }
           }
        }

        if ($bConfigExists) {
          return $oRequiredFolio;
        }

        return null;
    }

    /**
     * assign to correspond var the id of document
     * depends of type of document
     *
     * @param  SMovement  $oMovement
     * @param  integer $iMvtType
     * @param  integer $iDocumentId
     *
     * @return SMovement with the foreign key assigned
     */
    public function assignForeignDoc($oMovement = null, $iMvtType = 0, $iDocumentId = 0)
    {
        $oMovement->doc_order_id = 1;
        $oMovement->doc_invoice_id = 1;
        $oMovement->doc_debit_note_id = 1;
        $oMovement->doc_credit_note_id = 1;

        $oDocument = SDocument::find($iDocumentId);

        if (is_null($oDocument)) {
          return $oMovement;
        }

        if (\Config::get('scsiie.DOC_CLS.ORDER') == $oDocument->doc_class_id &&
                \Config::get('scsiie.DOC_TYPE.ORDER') == $oDocument->doc_type_id) {
            $oMovement->doc_order_id = $iDocumentId;
        }
        if (\Config::get('scsiie.DOC_CLS.DOCUMENT') == $oDocument->doc_class_id &&
              \Config::get('scsiie.DOC_TYPE.INVOICE') == $oDocument->doc_type_id) {
            $oMovement->doc_invoice_id = $iDocumentId;
        }
        if (\Config::get('scsiie.DOC_CAT.PURCHASES') == $oDocument->doc_category_id &&
            \Config::get('scsiie.DOC_CLS.ADJUST') == $oDocument->doc_class_id &&
            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE') == $oDocument->doc_type_id) {
              $oMovement->doc_debit_note_id = $iDocumentId;
        }
        if (\Config::get('scsiie.DOC_CAT.SALES') == $oDocument->doc_category_id &&
            \Config::get('scsiie.DOC_CLS.ADJUST') == $oDocument->doc_class_id &&
            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE') == $oDocument->doc_type_id) {
              $oMovement->doc_credit_note_id = $iDocumentId;
        }

        return $oMovement;
    }

    /**
     * assign to correspond var the id of document row
     * depends of type of document
     *
     * @param  SMovementRow $oMovementRow
     * @param  integer      $iMvtType
     * @param  integer      $iDocRowId  primary key of row
     *
     * @return SMovementRow with the foreign key of document row assigned
     */
    public function assignForeignRow(SMovementRow $oMovementRow, $iMvtType = 0, $iDocRowId = 0)
    {
        $oMovementRow->doc_order_row_id = 1;
        $oMovementRow->doc_invoice_row_id = 1;
        $oMovementRow->doc_debit_note_row_id = 1;
        $oMovementRow->doc_credit_note_row_id = 1;

        if (is_null($iDocRowId) || $iDocRowId == 0) {
          return $oMovementRow;
        }

        $oRow = SDocumentRow::find($iDocRowId);
        $oDocument = $oRow->document;

        if (\Config::get('scsiie.DOC_CLS.ORDER') == $oDocument->doc_class_id &&
                \Config::get('scsiie.DOC_TYPE.ORDER') == $oDocument->doc_type_id) {
            $oMovementRow->doc_order_row_id = $iDocRowId;
        }
        if (\Config::get('scsiie.DOC_CLS.DOCUMENT') == $oDocument->doc_class_id &&
              \Config::get('scsiie.DOC_TYPE.INVOICE') == $oDocument->doc_type_id) {
            $oMovementRow->doc_invoice_row_id = $iDocRowId;
        }
        if (\Config::get('scsiie.DOC_CAT.PURCHASES') == $oDocument->doc_category_id &&
            \Config::get('scsiie.DOC_CLS.ADJUST') == $oDocument->doc_class_id &&
            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE') == $oDocument->doc_type_id) {
            $oMovementRow->doc_debit_note_row_id = $iDocRowId;
        }
        if (\Config::get('scsiie.DOC_CAT.SALES') == $oDocument->doc_category_id &&
            \Config::get('scsiie.DOC_CLS.ADJUST') == $oDocument->doc_class_id &&
            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE') == $oDocument->doc_type_id) {
            $oMovementRow->doc_credit_note_row_id = $iDocRowId;
        }

        return $oMovementRow;
    }

    /**
     * [Create a movement type adjust of both classes, input and output]
     * @param  [type] $oMovement     [description]
     * @param  [type] $aMovementRows [description]
     * @return [type]                [description]
     */
    private function createTheMovement($oMovement, $aMovementRows)
    {
        $aMovements = array();

        switch ($oMovement->mvt_whs_type_id) {
          case \Config::get('scwms.MVT_TP_IN_PUR'):
          case \Config::get('scwms.MVT_TP_OUT_SAL'):
                $aRes = SLinkSupplyCore::linkSupply($oMovement, $aMovementRows);

                $oMovement = $aRes[0];
                $aMovementRows = $aRes[1];
            break;

          case \Config::get('scwms.MVT_IN_DLVRY_PP'):
          case \Config::get('scwms.MVT_IN_DLVRY_FP'):
          case \Config::get('scwms.MVT_OUT_CONSUMPTION'):
                $oMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
            break;

          default:
                $oMovement->prod_ord_id = 1;
            break;
        }

        $oMovement->aAuxRows = $aMovementRows;
        array_push($aMovements, $oMovement);

        return $aMovements;
    }

    /**
     * Create a transfer movement, the user only can create a output transfer
     *
     * @param  SMovement $oMovement
     * @param  array    $aMovementRows
     * @param  integer   $iWhsSrc
     * @param  integer   $iWhsDes
     *
     * @return array with SMovement objects
     */
    private function createTransfer(SMovement $oMovement, $aMovementRows = null,
                                                    $iWhsSrc = 0, $iWhsDes = 0, $iOperation = 0)
    {
        $aMovements = array();

        if ($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION')) {
            $oMirrorMovement = SMovement::find($oMovement->src_mvt_id);
        }
        else {
            $oMirrorMovement = clone $oMovement;

            switch ($oMovement->mvt_whs_type_id) {
              case \Config::get('scwms.MVT_TP_OUT_TRA'):
                $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.MVT_TP_IN_TRA');
                break;

              case \Config::get('scwms.MVT_OUT_DLVRY_RM'):
                $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.MVT_IN_DLVRY_RM');
                $oMirrorMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
                $oMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
                break;

              case \Config::get('scwms.MVT_OUT_RTRN_RM'):
                $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.MVT_IN_RTRN_RM');
                $oMirrorMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
                $oMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
                break;

              case \Config::get('scwms.MVT_OUT_ASSIGN_PP'):
                $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.MVT_IN_ASSIGN_PP');
                $oMirrorMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
                $oMovement->prod_ord_id = $oMovement->aAuxPOs[SMovement::SRC_PO];
                break;

              default:
                // code...
                break;
            }

            $oMirrorMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN');
            $oMirrorMovement->whs_id = $iWhsDes;
            $oMirrorMovement->is_system = true;
            $oWhs = SWarehouse::find($iWhsDes);
            $oMirrorMovement->branch_id = $oWhs->branch_id;
        }

        $iWhsSrcDefLocation = 0;
        $iWhsDesDefLocation = 0;

        if (! session('location_enabled')) {
           $oSrcLocation = SWarehouse::find($iWhsSrc)->getDefaultLocation();
           $oDesLocation = SWarehouse::find($iWhsDes)->getDefaultLocation();

           $iWhsSrcDefLocation = $oSrcLocation->id_whs_location;
           $iWhsDesDefLocation = $oDesLocation->id_whs_location;
        }

        $oMovement->aAuxRows = [];
        $oMirrorMovement->aAuxRows = [];
        foreach ($aMovementRows as $row) {
            $movRow = clone $row;
            $movRowM = clone $row;

            if (! session('location_enabled')) {
                $movRow->location_id = $iWhsSrcDefLocation;
                $movRowM->location_id = $iWhsDesDefLocation;
            }
            else {
                $movRowM->location_id = $row->iAuxLocationDesId;
            }

            array_push($oMovement->aAuxRows, $movRow);
            array_push($oMirrorMovement->aAuxRows, $movRowM);
        }

        array_push($aMovements, $oMovement);
        array_push($aMovements, $oMirrorMovement);

        return $aMovements;
    }

    private function createExternalTransfer($lMovements = [], $iOperation = 0, $iBranchDes = 0)
    {
       if (sizeof($lMovements) > 1) {
         if ($lMovements[1]->whs_id == session('transit_whs')->id_whs
              && $iOperation == \Config::get('scwms.OPERATION_TYPE.CREATION')) {
            $extTransfer = new SExternalTransfer();
            $extTransfer->is_deleted = false;
            $extTransfer->src_branch_id = $lMovements[0]->branch_id;
            $extTransfer->des_branch_id = $iBranchDes;
            $extTransfer->mvt_reference_id = $lMovements[1]->id_mvt;
            $extTransfer->created_by_id = \Auth::user()->id;
            $extTransfer->updated_by_id = \Auth::user()->id;

            $extTransfer->save();
         }
       }
    }

    /**
     * [divisionOfPallet description]
     * @param  [type] $oMovement    [description]
     * @param  [type] $oPalletData  [description]
     * @param  [type] $movementRows [description]
     * @return [type]               [description]
     */
    private function divisionOfPallet($oMovement, $iPallet, $iPalletLocation, $movementRows)
    {
      $aMovements = array();

      $oMirrorMovement = clone $oMovement;
      // creation of mirror movement
      $oMirrorMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_OUT'); // the object is cloned and set the opposite class
      $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.PALLET_RECONFIG_OUT'); // set the opposite type of movement
      $oMirrorMovement->mvt_trn_type_id = 1;
      $oMirrorMovement->mvt_adj_type_id = 1;
      $oMirrorMovement->mvt_mfg_type_id = 1;
      $oMirrorMovement->mvt_exp_type_id = 1;
      $oMirrorMovement->is_system = true;

       $movementPalletRows = array();
       foreach ($movementRows as $oMovRow) {
          $palletRow = clone $oMovRow;

          $palletRow->pallet_id = $iPallet;
          $palletRow->location_id = $iPalletLocation;

          array_push($movementPalletRows, $palletRow);
       }

       $oMovement->aAuxRows = $movementRows; // set the auxiliar array of movements to principal movement
       $oMirrorMovement->aAuxRows = $movementPalletRows; // set the auxiliar array of movements to mirror movement

       array_push($aMovements, $oMovement); // add the principal movement
       array_push($aMovements, $oMirrorMovement); // add the mirror movement
       // dd($aMovements);
       return $aMovements;
    }

    /**
     * create a movement when elements were added to a pallet
     *
     * @param SMovement $oMovement
     * @param Object $oPalletData
     * @param array $movementRows
     */
    private function addToPallet(SMovement $oMovement = null, $iPallet = 0, $iPalletLocation = 0, $movementRows = [])
    {
      $aMovements = array();

      $oMirrorMovement = clone $oMovement;
      // creation of mirror movement
      $oMirrorMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN'); // the object is cloned and set the opposite class
      $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.PALLET_RECONFIG_IN'); // set the opposite type of movement
      $oMirrorMovement->mvt_trn_type_id = 1;
      $oMirrorMovement->mvt_adj_type_id = 1;
      $oMirrorMovement->mvt_mfg_type_id = 1;
      $oMirrorMovement->mvt_exp_type_id = 1;
      $oMirrorMovement->is_system = true;

      $oMovement->aAuxRows = $movementRows;
      $oMirrorMovement->aAuxRows = [];
      foreach ($movementRows as $row) {
          $movRowM = clone $row;

          $movRowM->pallet_id = $iPallet;
          $movRowM->location_id = $iPalletLocation;

          array_push($oMirrorMovement->aAuxRows, $movRowM);
      }

      array_push($aMovements, $oMovement); // add the principal movement
      array_push($aMovements, $oMirrorMovement); // add the mirror movement
      // dd($aMovements);
      return $aMovements;
    }

    /**
     * Determine if the movement can be erased or activated depending of validations of stock
     * and referenced movements
     *
     * @param  SMovement $oMovement
     * @param  integer $iAction  \Config::get('scwms.MOV_ACTION.ACTIVATE')
     *                           \Config::get('scwms.MOV_ACTION.ERASE')
     *
     * @return array if the size of array is grater than 0 the movement can't be erased
     */
    public function canMovBeErasedOrActivated($oMovement = null, $iAction = 0)
    {
        $aErrors = array();
        $lReferencedMovs = SMovement::where('src_mvt_id', $oMovement->id_mvt)
                                      ->where('is_deleted', $iAction == \Config::get('scwms.MOV_ACTION.ACTIVATE'))
                                      ->get();

        foreach ($lReferencedMovs as $oMov) {
           $aInternalErrors = $this->canMovBeErasedOrActivated($oMov, $iAction);

           if (sizeof($aInternalErrors) > 0) {
              array_push($aInternalErrors, 'Problema con movimiento referenciado.');
              return $aInternalErrors;
           }
        }

        if ($iAction == \Config::get('scwms.MOV_ACTION.ERASE')) {
          if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
              $oAuxMov = clone $oMovement;
              $oAuxMov->id_mvt = 0;
              $aErrors = SStockUtils::validateStock($oAuxMov);

              if(sizeof($aErrors) > 0)
              {
                 return $aErrors;
              }
          }
          else {
              foreach ($oMovement->rows as $row) {
                $aErrors = SStockUtils::validateInputPallet($row,
                                                        $oMovement->year_id,
                                                        $oMovement->mvt_whs_type_id,
                                                        0);

                if(sizeof($aErrors) > 0)
                {
                  return $aErrors;
                }
              }
          }
        }
        else {
          if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT')) {
              $oAuxMov = clone $oMovement;
              $oAuxMov->id_mvt = 0;
              $aErrors = SStockUtils::validateStock($oAuxMov);

              if(sizeof($aErrors) > 0)
              {
                 return $aErrors;
              }
          }
          else {
              foreach ($oMovement->rows as $row) {
                $aErrors = SStockUtils::validateInputPallet($row,
                                                        $oMovement->year_id,
                                                        $oMovement->mvt_whs_type_id,
                                                        0);

                if(sizeof($aErrors) > 0)
                {
                  return $aErrors;
                }
              }
          }
        }

        return $aErrors;
    }

    public function canMovBeModified($oMovement = null)
    {
      $aErrors = array();
      $lReferencedMovs = SMovement::where('src_mvt_id', $oMovement->id_mvt)
                                    ->where('is_system', false)
                                    ->where('is_deleted', false)
                                    ->get();

      if (sizeof($lReferencedMovs) > 0) {
         array_push($aErrors, 'No se puede modificar el movimiento, ya hay otros movimientos asociados');
      }

      return $aErrors;
    }

    public function eraseMov($oMov, $request)
    {
      try
      {
        $errors = \DB::connection('company')->transaction(function() use ($oMov, $request) {

          $lReferencedMovs = SMovement::where('src_mvt_id', $oMov->id_mvt)
                                      ->where('is_deleted', false)
                                      ->get();

          foreach ($lReferencedMovs as $refMov) {
             $aRes = $this->eraseMov($refMov, $request);

             if (is_array($aRes) && sizeof($aRes) > 0) {
                return $aRes;
             }
          }

          $oMov->is_deleted = true;
          $oMov->updated_by_id = \Auth::user()->id;
          $aErrors = $oMov->save();

          if (is_array($aErrors) && sizeof($aErrors) > 0) {
             return $aErrors;
          }

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

      return true;
    }

    private function eraseMovement($iMovement)
    {
        try
        {
          \DB::connection('company')->transaction(function() use ($iMovement) {
            $oMovement = SMovement::find($iMovement);

            foreach ($oMovement->rows as $oRow) {
               if (! $oRow->is_deleted) {
                 $oRow->is_deleted = true;
                 $oRow->save();
               }

               foreach ($oRow->lotRows as $lotRow) {
                  $lotRow->is_deleted = true;
                  $lotRow->save();
               }
            }

          });

          return true;
       }
       catch (\Exception $e)
       {
           return $e;
       }
    }

}
