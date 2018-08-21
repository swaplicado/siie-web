<?php namespace App\SCore;

use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SStock;
use App\WMS\SIndSupplyLink;
use App\WMS\SIndSupplyLinkLot;

use App\ERP\SDocument;
use App\ERP\SDocumentRow;

/**
 *
 */
class SLinkSupplyCore {

    const TP_INVOICE = '2';
    const TP_ORDER = '1';

    public static function getSupplyOfDocument($oDocument = null)
    {
       $query = SMovement::where(''.SLinkSupplyCore::getField($oDocument->doc_class_id), $oDocument->id_document)
                          ->where('is_deleted', false)
                          ->get();

       return $query;
    }

    public static function getIndirectSupplyRow($iMvtRowId = 0, $iDocRowId = 0)
    {
        $sSelect = 'COALESCE(SUM(wisl.quantity), 0) AS qty_ind_supp_row';

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                    ->table('wms_indirect_supply_links as wisl')
                    ->where('des_doc_row_id', $iDocRowId)
                    ->where('is_deleted', false)
                    ->select(\DB::raw($sSelect));

        if ($iMvtRowId > 0) {
          $query = $query->where('mvt_row_id', $iMvtRowId);
        }

        $query = $query->get();

       if (sizeof($query) > 0) {
          return $query[0]->qty_ind_supp_row;
       }

       return 0;
    }

    public static function getIndirectSupplyRowLot($iMvtRowLotId = 0, $iDocRowId = 0)
    {
        $sSelect = 'COALESCE(SUM(wisll.quantity), 0) AS qty_ind_supp_row_lot';

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                    ->table('wms_indirect_supply_links AS wisl')
                    ->join('wms_indirect_supply_link_lots AS wisll', 'id_indirect_supply_link', '=', 'indirect_supply_link_id')
                    ->where('mvt_row_lot_id', $iMvtRowLotId)
                    ->where('des_doc_row_id', $iDocRowId)
                    ->where('wisl.is_deleted', false)
                    ->where('wisll.is_deleted', false)
                    ->select(\DB::raw($sSelect))
                    ->get();

       if (sizeof($query) > 0) {
          return $query[0]->qty_ind_supp_row_lot;
       }

       return 0;
    }

    private static function getField($iDocClass)
    {
        switch ($iDocClass) {
          case \Config::get('scsiie.DOC_CLS.DOCUMENT'):
            return 'doc_invoice_id';
            break;
          case \Config::get('scsiie.DOC_CLS.ORDER'):
            return 'doc_order_id';
            break;
          case \Config::get('scsiie.DOC_CLS.ADJUST'):
            return 'doc_credit_note_id';
            break;

          default:
            # code...
            break;
        }
    }

    public static function linkSupply($oMovement = null, $lMovRows = [])
    {
      $aReturn = array();

      // If the supply is an order
      if ($oMovement->doc_order_id > 1) {
          $oOrder = SDocument::find($oMovement->doc_order_id);

          foreach ($lMovRows as $oMovRow) {
            if ($oMovRow->is_deleted) {
              continue;
            }

            $aItemInMov = SLinkSupplyCore::getTimesItemInMovement($lMovRows, $oMovRow->item_id, $oMovRow->unit_id);
            $aByInvoices = SLinkSupplyCore::getInvoices($oMovRow->item_id,
                                                                  $oMovRow->unit_id,
                                                                  $oMovement->doc_order_id);

            if (sizeof($aByInvoices) == 0) {
              continue;
            }
            if (sizeof($aByInvoices) == 1) {
              $oInvoice = SDocument::find($aByInvoices[0]->document_id);

              $aInvoiceItem = SLinkSupplyCore::isItemInDocument($oMovRow->item_id, $oMovRow->unit_id, $oInvoice->rows);
              if (sizeof($aItemInMov) == 1) {
                 if (sizeof($aInvoiceItem) == 1) {
                    $dInvRowSupp = SLinkSupplyCore::getSuppliedOfRow(SLinkSupplyCore::TP_INVOICE,
                                                                    $aInvoiceItem[0]->id_document_row);

                    if ($aInvoiceItem[0]->quantity >= ($oMovRow->quantity + $dInvRowSupp)) {
                      $oMovRow->doc_invoice_row_id = $aInvoiceItem[0]->id_document_row;
                      $oMovement->doc_invoice_id = $aByInvoices[0]->document_id;
                    }
                 }
              }
              else {
                if (sizeof($aInvoiceItem) == 1) {
                  $dCurrentSupply = SLinkSupplyCore::getCurrentSupply($lMovRows,
                                                      $aInvoiceItem[0]->id_document_row,
                                                      SLinkSupplyCore::TP_INVOICE);
                  $dInvRowSupp = SLinkSupplyCore::getSuppliedOfRow(SLinkSupplyCore::TP_INVOICE,
                                                                  $aInvoiceItem[0]->id_document_row);

                  if (($dInvRowSupp + $dCurrentSupply) <= $aInvoiceItem[0]->quantity) {
                      $oMovRow->doc_invoice_row_id = $aInvoiceItem[0]->id_document_row;
                      $oMovement->doc_invoice_id = $aByInvoices[0]->document_id;
                  }
                }
              }
            }
            else {
              if (sizeof($aItemInMov) == 1) {
                $oQtyOnInvoices = SLinkSupplyCore::getTotalSumOnInvoices($oMovRow->item_id,
                                                    $oMovRow->unit_id,
                                                    $oMovement->doc_order_id);

                $dCurrentSupply = SLinkSupplyCore::getCurrentSupply($lMovRows,
                                                    $oMovRow->doc_order_row_id,
                                                    SLinkSupplyCore::TP_ORDER);

                $dQtyOnInvoices = sizeof($oQtyOnInvoices) == 1 ? $oQtyOnInvoices[0]->on_invoice : 0;

                if ($dQtyOnInvoices == $dCurrentSupply) {
                   $lInvoicesRows = SLinkSupplyCore::getInvoicesRows($oMovRow->item_id,
                                                       $oMovRow->unit_id,
                                                       $oMovement->doc_order_id);
                   $bValid = true;
                   foreach ($lInvoicesRows as $oInRow) {
                      $dInvRowSupp = SLinkSupplyCore::getSuppliedOfRow(SLinkSupplyCore::TP_INVOICE,
                                                                     $oInRow->id_document_row);

                      $bValid = ! $dInvRowSupp > 0;
                   }

                   if ($bValid) {
                      // surtido indirecto !!!!
                      // cómo saber qué lotes a qué facturas??
                      // foreach ($lInvoicesRows as $oInRow) {
                      //   $oIndSupply = SLinkSupplyCore::makeIndirectSupply($oMovRow,
                      //                                                     $oInRow->quantity,
                      //                                                     $oMovRow->doc_order_row_id,
                      //                                                     $oInRow->id_document_row);
                      // }
                   }
                }
              }
            }
          }
      }
      // if the supply is a invoice
      elseif ($oMovement->doc_invoice_id > 1) {
          $oInvoice = SDocument::find($oMovement->doc_invoice_id);

          // if invoice has order document source
          if ($oInvoice->doc_src_id > 1) {
             $oOrder = SDocument::find($oInvoice->doc_src_id);

             foreach ($lMovRows as $oMovRow) {
               if ($oMovRow->is_deleted) {
                 continue;
               }

               $aOrderItem = SLinkSupplyCore::isItemInDocument($oMovRow->item_id, $oMovRow->unit_id, $oOrder->rows);
               $aItemInMov = SLinkSupplyCore::getTimesItemInMovement($lMovRows, $oMovRow->item_id, $oMovRow->unit_id);
               $oInvoiceRow = SDocumentRow::find($oMovRow->doc_invoice_row_id);

               if (sizeof($aOrderItem) > 0) {
                  if (sizeof($aOrderItem) == 1) {
                      $dRowSupp = SLinkSupplyCore::getSuppliedOfRow(SLinkSupplyCore::TP_ORDER,
                                                                  $aOrderItem[0]->id_document_row);
                      if (sizeof($aItemInMov) == 1) {
                         if (($dRowSupp + $oMovRow->quantity) >  $oInvoiceRow->quantity) {
                           // ERROR!!!
                           continue;
                         }
                         // if row of order is totally supplied
                         elseif ($dRowSupp ==  $aOrderItem[0]->quantity) {
                            continue;
                         }
                         elseif ($oMovRow->quantity <= ($aOrderItem[0]->quantity - $dRowSupp)) {
                           $oMovRow->doc_order_row_id = $aOrderItem[0]->id_document_row;
                           $oMovement->doc_order_id = $oInvoice->doc_src_id;
                         }
                         else {
                           // Only link a part of movement
                           continue;
                           // $oMovRow->$oAuxSupplyLink = SLinkSupplyCore::makeIndirectSupply($oMovRow,
                           //                 $aOrderItem[0]->quantity - $oSupply->inputs,
                           //                 $oMovRow->doc_invoice_row_id,
                           //                 $oMovRow->doc_order_row_id);
                        }
                      }
                      else {
                        // if has more than one assortment on the invoice
                        // get the quantity of supply on the movement
                        $dCurrentSupply = SLinkSupplyCore::getCurrentSupply($lMovRows,
                                                                            $aOrderItem[0]->id_document_row,
                                                                            SLinkSupplyCore::TP_ORDER);

                        $dSupplied = $dRowSupp + $dCurrentSupply;
                        //If the supplied is less or equal to the quantity that the order has
                        if ($dSupplied <= $aOrderItem[0]->quantity) {
                           $oMovRow->doc_order_row_id = $aOrderItem[0]->id_document_row;
                           $oMovement->doc_order_id = $oInvoice->doc_src_id;
                        }
                      }
                  }
               }
             }
          }
      }

      $aReturn[0] = $oMovement;
      $aReturn[1] = $lMovRows;

      return $aReturn;
    }

    private static function isItemInDocument($iItem = 0, $iUnit = 0, $lDocRows = [])
    {
       $aCount = array();
       foreach ($lDocRows as $oDocRow) {
          if ($oDocRow->is_deleted) {
             continue;
          }

          if ($oDocRow->item_id == $iItem && $oDocRow->unit_id == $iUnit) {
            array_push($aCount, $oDocRow);
          }
       }

       return $aCount;
    }

    private static function getTimesItemInMovement($lMovRows = [], $iItem = 0, $iUnit = 0)
    {
       $aCount = array();
       foreach ($lMovRows as $oMovRow) {
          if (!$oMovRow->is_deleted && $oMovRow->item_id == $iItem && $oMovRow->unit_id == $iUnit) {
            array_push($aCount, $oMovRow);
          }
       }

       return $aCount;
    }

    private static function getSuppliedOfRow($iDocT = 0, $iRow = 0)
    {
        $oRes = SStock::where('is_deleted', false)
                      ->selectRaw('sum(input) AS inputs, sum(output) AS outputs')
                      ->where(function ($query) {
                            $query->where('mvt_whs_type_id', \Config::get('scwms.MVT_TP_IN_PUR'))
                                  ->orWhere('mvt_whs_type_id', \Config::get('scwms.MVT_TP_IN_SAL'));
                        });


        switch ($iDocT) {
          case SLinkSupplyCore::TP_ORDER:
            $oRes = $oRes->where('doc_order_row_id', $iRow);
            break;

          case SLinkSupplyCore::TP_INVOICE:
            $oRes = $oRes->where('doc_invoice_row_id', $iRow);
            break;

          default:
            // code...
            break;
        }

        $oRes = $oRes->get();

        $dSupplied = 0;
        if (sizeof($oRes) > 0) {
          $dSupplied = $oRes[0]->inputs;
        }

        $dIndirectSupplied = SLinkSupplyCore::getIndirectSupplyRow(0, $iRow);

        return ($dSupplied + $dIndirectSupplied);
    }

    private static function getCurrentSupply($lMovRows = [], $iDocRow = 0, $iDocT = 0)
    {
       $dSum = 0;
       foreach ($lMovRows as $oMovRow) {
         switch ($iDocT) {
           case SLinkSupplyCore::TP_ORDER:
             if ($oMovRow->doc_order_row_id == $iDocRow) {
               $dSum += $oMovRow->quantity;
             }

             break;

           case SLinkSupplyCore::TP_INVOICE:
             if ($oMovRow->doc_invoice_row_id == $iDocRow) {
               $dSum += $oMovRow->quantity;
             }

             break;

           default:
             // code...
             break;
         }
       }

       return $dSum;
    }

    private static function makeIndirectSupply($oMovRow = null, $dQuantityToLink = 0, $iSrcRow = 0, $iDesRow = 0)
    {
       $oIndirectSupply = new SIndSupplyLink();

       $oIndirectSupply->quantity = $dQuantityToLink;
       $oIndirectSupply->is_deleted = false;
       $oIndirectSupply->src_doc_row_id = $iSrcRow;
       $oIndirectSupply->des_doc_row_id = $iDesRow;
       $oIndirectSupply->mvt_row_id = 1;
       $oIndirectSupply->pallet_id = $oMovRow->pallet_id;
       $oIndirectSupply->created_by_id = $oMovRow->created_by_id;
       $oIndirectSupply->updated_by_id = $oMovRow->updated_by_id;

       $lAuxLots = $oMovRow->getAuxLots();

       $lAuxLotLinks = array();
       $index = 0;
       foreach ($lAuxLots as $oMovLotRow) {
         $oLotLink = new SIndSupplyLinkLot();
         $oLotLink->quantity = $oMovLotRow->quantity;
         $oLotLink->is_deleted = false;
         $oLotLink->lot_id = $oMovLotRow->lot_id;
         $oLotLink->mvt_row_lot_id = 1;

         $lAuxLotLinks[$index++] = $oLotLink;
       }

       $oIndirectSupply->lAuxLotLinks;

       return $oIndirectSupply;

    }

    private static function getItemInInvoices($iItem = 0, $iUnit = 0, $iOrder = 0)
    {
        $oRes = SDocumentRow::where('is_deleted', false)
                    ->where('item_id', $iItem)
                    ->where('unit_id', $iUnit)
                    ->whereRaw('document_id IN (SELECT
                                                    id_document
                                                FROM
                                                    erpu_documents
                                                WHERE
                                                    doc_src_id = '.$iOrder.'
                                                AND is_deleted = FALSE)');

        return $oRes;
    }

    private static function getTotalSumOnInvoices($iItem = 0, $iUnit = 0, $iOrder = 0)
    {
        $oQuery = SLinkSupplyCore::getItemInInvoices($iItem, $iUnit, $iOrder);

        $oQuery = $oQuery->selectRaw('SUM(quantity) AS on_invoice')
                          ->get();

        return $oQuery;
    }

    private static function getInvoices($iItem = 0, $iUnit = 0, $iOrder = 0)
    {
        $oQuery = SLinkSupplyCore::getItemInInvoices($iItem, $iUnit, $iOrder);

        $oQuery = $oQuery->groupBy('document_id')
                          ->get();

        return $oQuery;
    }

    private static function getInvoicesRows($iItem = 0, $iUnit = 0, $iOrder = 0)
    {
        $oQuery = SLinkSupplyCore::getItemInInvoices($iItem, $iUnit, $iOrder);

        $oQuery = $oQuery->groupBy('document_id')
                          ->groupBy('id_document_row')
                          ->get();

        return $oQuery;
    }
}
