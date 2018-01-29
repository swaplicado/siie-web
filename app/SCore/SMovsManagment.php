<?php namespace App\SCore;

use App\WMS\SWarehouse;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\WMS\SFolio;
use App\ERP\SDocument;
use App\ERP\SDocumentRow;

use App\SCore\SStockUtils;

/**
 * this class manages the movement process
 */
class SMovsManagment {

    /**
     * get a new folio for the movement
     * if the folio was not found return 0
     *
     * @param  SMovement $oMovement
     *
     * @return int new folio
     */
    public static function getNewFolio($oMovement = '')
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
                                                 $oMovement->whs_id,
                                                 $oMovement->mvt_whs_class_id,
                                                 $oMovement->mvt_whs_type_id);

       // if the configuration was found look for the max folio with this configuration
       // if the query returns a empty result the function return the folio configured as initial
       if ($oRequiredFolio != null) {
           $lFolios = $baseQuery->where('mvt_whs_class_id', $oMovement->mvt_whs_class_id)
                         ->where('mvt_whs_type_id', $oMovement->mvt_whs_type_id)
                         ->where('branch_id', $oMovement->branch_id)
                         ->where('whs_id', $oMovement->whs_id)
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
           $oRequiredFolio = SMovsManagment::existsFolioConfiguration($lFolios,
                                                     \Config::get('scwms.CONTAINERS.BRANCH'),
                                                     $oMovement->branch_id,
                                                     $oMovement->mvt_whs_class_id,
                                                     $oMovement->mvt_whs_type_id);

           // if the configuration was found look for the max folio with this configuration
           // if the query returns a empty result the function return the folio configured as initial
           if ($oRequiredFolio != null) {
               $lFolios = $baseQuery->where('mvt_whs_class_id', $oMovement->mvt_whs_class_id)
                             ->where('mvt_whs_type_id', $oMovement->mvt_whs_type_id)
                             ->where('branch_id', $oMovement->branch_id)
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
               $oRequiredFolio = SMovsManagment::existsFolioConfiguration($lFolios,
                                                         0,
                                                         0,
                                                         $oMovement->mvt_whs_class_id,
                                                         $oMovement->mvt_whs_type_id);
               // if the configuration was found look for the max folio with this configuration
               // if the query returns a empty result the function return the folio configured as initial
               if ($oRequiredFolio != null)
               {
                   $lFolios = $baseQuery->where('mvt_whs_class_id', $oMovement->mvt_whs_class_id)
                                 ->where('mvt_whs_type_id', $oMovement->mvt_whs_type_id)
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
                   $oRequiredFolio = SMovsManagment::existsFolioConfiguration($lFolios,
                                                             0,
                                                             0,
                                                             $oMovement->mvt_whs_class_id,
                                                             0);

                   // if the configuration was found look for the max folio with this configuration
                   // if the query returns a empty result the function return the folio configured as initial
                   if ($oRequiredFolio != null) {
                       $lFolios = $baseQuery->where('mvt_whs_class_id', $oMovement->mvt_whs_class_id)
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
    private static function existsFolioConfiguration($lFolios = null, $iContainerType = 0,
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
    public static function assignForeignDoc($oMovement = null, $iMvtType = 0, $iDocumentId = 0)
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
    public static function assignForeignRow(SMovementRow $oMovementRow, $iMvtType = 0, $iDocRowId = 0)
    {
        $oMovementRow->doc_order_row_id = 1;
        $oMovementRow->doc_invoice_row_id = 1;
        $oMovementRow->doc_debit_note_row_id = 1;
        $oMovementRow->doc_credit_note_row_id = 1;

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
    public static function processMovement($oMovement, $aMovementRows, $iClass, $iMovType, $iWhsSrc, $iWhsDes, $oPalletData)
    {
       // The movement is adjust or input by purchases
       if ($iMovType == \Config::get('scwms.MVT_TP_IN_ADJ') ||
            $iMovType == \Config::get('scwms.MVT_TP_OUT_ADJ') ||
              $iMovType == \Config::get('scwms.MVT_TP_IN_PUR')) {
          return SMovsManagment::createTheMovement($oMovement, $aMovementRows);
       }
       // The movement is trasfer
       else if($iMovType == \Config::get('scwms.MVT_TP_OUT_TRA')) {
          return SMovsManagment::createTransfer($oMovement, $aMovementRows, $iWhsSrc, $iWhsDes);
       }
       // the movement is pallet reconfiguration (pallet division)
       else if ($iMovType == \Config::get('scwms.PALLET_RECONFIG_IN')) {
         return SMovsManagment::divisionOfPallet($oMovement, $oPalletData, $aMovementRows);

       }
       // the movement is pallet reconfiguration (add to pallet)
       else if ($iMovType == \Config::get('scwms.PALLET_RECONFIG_OUT')) {
         return SMovsManagment::addToPallet($oMovement, $oPalletData, $aMovementRows);
       }
    }

    /**
     * [Create a movement type adjust of both classes, input and output]
     * @param  [type] $oMovement     [description]
     * @param  [type] $aMovementRows [description]
     * @return [type]                [description]
     */
    public static function createTheMovement($oMovement, $aMovementRows)
    {
        $aMovements = array();

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
    public static function createTransfer(SMovement $oMovement, $aMovementRows = null,
                                                    $iWhsSrc = 0, $iWhsDes = 0)
    {
        $aMovements = array();

        $oMirrorMovement = clone $oMovement;
        $oMirrorMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN');
        $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.MVT_TP_IN_TRA');
        $oMirrorMovement->whs_id = $iWhsDes;
        $oMirrorMovement->branch_id = $oMirrorMovement->warehouse->branch_id;

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
              $movRow->location_id = 1;
            }

            array_push($oMovement->aAuxRows, $movRow);
            array_push($oMirrorMovement->aAuxRows, $movRowM);
        }

        array_push($aMovements, $oMovement);
        array_push($aMovements, $oMirrorMovement);

        return $aMovements;
    }

    /**
     * [divisionOfPallet description]
     * @param  [type] $oMovement    [description]
     * @param  [type] $oPalletData  [description]
     * @param  [type] $movementRows [description]
     * @return [type]               [description]
     */
    public static function divisionOfPallet($oMovement, $oPalletData, $movementRows)
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


       $oMvtPallet = new SMovementRow();
       // transform the pallet row to row of mirror movement
       $oMvtPallet->quantity = $oPalletData['dQuantity'];
       $oMvtPallet->amount_unit = $oPalletData['dPrice'];
       $oMvtPallet->item_id = $oPalletData['iItemId'];
       $oMvtPallet->unit_id = $oPalletData['iUnitId'];
       $oMvtPallet->pallet_id = $oPalletData['iPalletId'];
       $oMvtPallet->location_id = $oPalletData['iLocationId'];
       $oMvtPallet->doc_order_row_id =1;
       $oMvtPallet->doc_invoice_row_id = 1;
       $oMvtPallet->doc_debit_note_row_id = 1;
       $oMvtPallet->doc_credit_note_row_id = 1;

       $movLotRows = array();
       // process the lots of pallet
       foreach ($oPalletData['lotRows'] as $lotRow) {
           $oMovLotRow = new SMovementRowLot();
           $oMovLotRow->quantity = $lotRow['dQuantity'];
           $oMovLotRow->amount_unit = $lotRow['dPrice'];
           $oMovLotRow->lot_id = $lotRow['iLotId'];

           // adds the row lot to auxiliar array of lots
           array_push($movLotRows, $oMovLotRow);
       }

       $oMvtPallet->setAuxLots($movLotRows); // set the auxiliar array of lots to movement row
       $movementPalletRows = array();
       array_push($movementPalletRows, $oMvtPallet); // add row of pallet to movement rows
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
    public static function addToPallet(SMovement $oMovement, $oPalletData = null, $movementRows)
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

      $oMovement->aAuxRows = $movementRows;
      $oMirrorMovement->aAuxRows = [];
      foreach ($movementRows as $row) {
          $movRowM = clone $row;

          $movRowM->pallet_id = $oPalletData['iPalletId'];
          $movRowM->location_id = $oPalletData['iLocationId'];

          array_push($oMirrorMovement->aAuxRows, $movRowM);
      }

      array_push($aMovements, $oMovement); // add the principal movement
      array_push($aMovements, $oMirrorMovement); // add the mirror movement
      // dd($aMovements);
      return $aMovements;
    }
}
