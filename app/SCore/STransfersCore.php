<?php namespace App\SCore;

use Illuminate\Http\Request;

use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

/**
 *
 */
class StransfersCore
{

  public function getReceivedFromMovement($iMovement)
  {
     $oMovement = SMovement::find($iMovement);

     $lRes = SMovement::where('src_mvt_id', $iMovement)
                       ->where('is_deleted', false)
                       ->get();

     foreach ($lRes as $oMov) {
       foreach ($oMov->rows as $qRow) {
         foreach ($oMovement->rows as $row) {
            if ($qRow->item_id == $row->item_id && $qRow->unit_id == $row->unit_id) {
                if ($row->dReceived < $row->quantity) {
                    $row->dReceived += $qRow->quantity;
                }

                if ($row->item->is_lot) {
                    foreach ($row->lotRows as $lotRow) {
                       foreach ($qRow->lotRows as $qLotRow) {
                          if ($lotRow->lot_id == $qLotRow->lot_id) {
                              if ($lotRow->dReceived < $lotRow->quantity) {
                                  $lotRow->dReceived += $qLotRow->quantity;
                              }
                          }
                       }
                    }
                }
            }
         }
       }
     }

     return $oMovement;
  }
}
