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

    public static function getIndirectSupplyRow($iMvtRowId = 0, $iDocRowId = 0)
    {
        $sSelect = 'COALESCE(SUM(wisl.quantity), 0) AS qty_ind_supp_row';

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                    ->table('wms_indirect_supply_links as wisl')
                    ->where('mvt_row_id', $iMvtRowId)
                    ->where('des_doc_row_id', $iDocRowId)
                    ->where('is_deleted', false)
                    ->select(\DB::raw($sSelect))
                    ->get();

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
}
