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
    public static function processMovement($oMovement, $aMovementRows, $iClass, $iMovType, $iWhsSrc, $iWhsDes)
    {
       $aMovements = array();
       if ($iMovType == \Config::get('scwms.MVT_TP_IN_ADJ') || $iMovType == \Config::get('scwms.MVT_TP_OUT_ADJ'))
       {
          $oMovement->aAuxRows = $aMovementRows;
          array_push($aMovements, $oMovement);
          return $aMovements;
       }
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
    }
}
