<?php namespace App\SCore;

use App\WMS\SWarehouse;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

use App\SCore\SStockUtils;

/**
 * this class manages the movement process
 */
class SMovsManagment
{
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
       $aMovements = array();
       // The movement is adjust
       if ($iMovType == \Config::get('scwms.MVT_TP_IN_ADJ') || $iMovType == \Config::get('scwms.MVT_TP_OUT_ADJ'))
       {
          $oMovement->aAuxRows = $aMovementRows;
          array_push($aMovements, $oMovement);
          return $aMovements;
       }
       // The movement is trasfer
       else if($iMovType == \Config::get('scwms.MVT_TP_OUT_TRA'))
       {
          $oMirrorMovement = clone $oMovement;
          $oMirrorMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN');
          $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.MVT_TP_IN_TRA');
          $oMirrorMovement->whs_id = $iWhsDes;
          $oMirrorMovement->branch_id = $oMirrorMovement->whs->branch_id;

          $iWhsSrcDefLocation = 0;
          $iWhsDesDefLocation = 0;
          if (! session('location_enabled'))
          {
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

              if (! session('location_enabled'))
              {
                  $movRow->location_id = $iWhsSrcDefLocation;
                  $movRowM->location_id = $iWhsDesDefLocation;
              }
              else
              {
                $movRow->location_id = 1;
              }

              array_push($oMovement->aAuxRows, $movRow);
              array_push($oMirrorMovement->aAuxRows, $movRowM);
          }

          array_push($aMovements, $oMovement);
          array_push($aMovements, $oMirrorMovement);

          return $aMovements;
       }
       // the movement is pallet reconfiguration (pallet division)
       else if ($iMovType == \Config::get('scwms.PALLET_RECONFIG_IN'))
       {
         $oMirrorMovement = clone $oMovement;
         // creation of mirror movement
         $oMirrorMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_OUT'); // the object is cloned and set the opposite class
         $oMirrorMovement->mvt_whs_type_id = \Config::get('scwms.PALLET_RECONFIG_OUT'); // set the opposite type of movement
         $oMirrorMovement->mvt_trn_type_id = 1;
         $oMirrorMovement->mvt_adj_type_id = 1;
         $oMirrorMovement->mvt_mfg_type_id = 1;
         $oMirrorMovement->mvt_exp_type_id = 1;

         $movementRows = array();
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
          array_push($movementRows, $oMvtPallet); // add row of pallet to movement rows
          $oMovement->aAuxRows = $aMovementRows; // set the auxiliar array of movements to principal movement
          $oMirrorMovement->aAuxRows = $movementRows; // set the auxiliar array of movements to mirror movement

          array_push($aMovements, $oMovement); // add the principal movement
          array_push($aMovements, $oMirrorMovement); // add the mirror movement
          // dd($aMovements);
          return $aMovements;
       }
    }
}
