<?php namespace App\SCore;

use App\WMS\SMovement;

/**
 *
 */
class SLinkSupplyCore {

    public static function getSupplyOfDocument($oDocument = null)
    {
       $query = SMovement::where(''.SLinkSupplyCore::getField($oDocument->doc_class_id), $oDocument->id_document)
                          ->where('is_deleted', false)
                          ->get();

       return $query;
    }

    public static function getField($iDocClass)
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
}
