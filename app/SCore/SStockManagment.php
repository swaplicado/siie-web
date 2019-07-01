<?php namespace App\SCore;

use Carbon\Carbon;
use App\SUtils\SGuiUtils;

/**
 * this class manages the stock of company
 */
class SStockManagment
{
    /**
     * [getStockBaseQuery description]
     * @param  string $sSelect [description]
     * @return [type]          [description]
     */
    public static function getStockBaseQuery($sSelect = '')
    {
        $query = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_stock as ws')
                      ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                      ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                      ->join('wms_pallets as wp', 'ws.pallet_id', '=', 'wp.id_pallet')
                      ->join('wms_lots as wl', 'ws.lot_id', '=', 'wl.id_lot')
                      ->join('wmsu_whs_locations as wwl', 'ws.location_id', '=', 'wwl.id_whs_location')
                      ->join('wmsu_whs as ww', 'wwl.whs_id', '=', 'ww.id_whs')
                      ->join('erpu_branches as eb', 'ww.branch_id', '=', 'eb.id_branch')
                      ->where('ei.is_inventory', true)
                      ->where('ws.is_deleted', false);

        if ($sSelect != '') {
            $query = $query->select(\DB::raw($sSelect));
        }

        return $query;
    }

    /**
     * get the result of query without get();
     *
     * @param  array $aParameters [
         * \Config::get('scwms.STOCK_PARAMS.SSELECT')
         * \Config::get('scwms.STOCK_PARAMS.ITEM')
         * \Config::get('scwms.STOCK_PARAMS.UNIT')
         * \Config::get('scwms.STOCK_PARAMS.LOT')
         * \Config::get('scwms.STOCK_PARAMS.PALLET')
         * \Config::get('scwms.STOCK_PARAMS.LOCATION')
         * \Config::get('scwms.STOCK_PARAMS.WHS')
         * \Config::get('scwms.STOCK_PARAMS.BRANCH')
         * \Config::get('scwms.STOCK_PARAMS.ID_YEAR')
         * \Config::get('scwms.STOCK_PARAMS.DATE')
         * \Config::get('scwms.STOCK_PARAMS.ID_MVT')
         * ]
         * @param  array $aSegParameters [
             * \Config::get('scwms.STOCK_PARAMS.SSELECT')
             * \Config::get('scwms.STOCK_PARAMS.ITEM')
             * \Config::get('scwms.STOCK_PARAMS.UNIT')
             * \Config::get('scwms.STOCK_PARAMS.LOT')
             * \Config::get('scwms.STOCK_PARAMS.PALLET')
             * \Config::get('scwms.STOCK_PARAMS.LOCATION')
             * \Config::get('scwms.STOCK_PARAMS.WHS')
             * \Config::get('scwms.STOCK_PARAMS.BRANCH')
             * \Config::get('scwms.STOCK_PARAMS.ID_YEAR')
             * \Config::get('scwms.STOCK_PARAMS.DATE')
             * \Config::get('scwms.STOCK_PARAMS.ID_MVT')
             * ]
             *
     * @return Query result of query
     */
    public static function getStockResult($aParameters = [], $aSegParameters = [])
    {
        $aSegregationParameters = array();
        if ($aSegParameters == null || sizeof($aSegParameters) == 0) {
            $aSegregationParameters = $aParameters;
        }
        else {
            $aSegregationParameters = $aSegParameters;
        }

        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.WITHOUT_SEGREGATED'), $aParameters)) {
            if ($aParameters[\Config::get('scwms.STOCK_PARAMS.WITHOUT_SEGREGATED')]) {
                $sSelect = $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')].', ("0") as segregated';
            }
            else {
              $sub = session('stock')->getSubSegregated($aSegregationParameters);
              $sSelect = $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')].', ('.($sub->toSql()).') as segregated';
            }
        }
        else {
          $sub = session('stock')->getSubSegregated($aSegregationParameters);
          $sSelect = $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')].', ('.($sub->toSql()).') as segregated';
        }

       $stock = SStockManagment::getStockBaseQuery($sSelect);

       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.ITEM'), $aParameters) &&
               $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] != 0) {
           $stock->where('ws.item_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.UNIT'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] != 0) {
           $stock->where('ws.unit_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.LOT'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] != 0) {
           $stock->where('ws.lot_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.PALLET'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] != 0) {
           $stock->where('ws.pallet_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.LOCATION'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] != 0) {
           $stock->where('ws.location_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.WHS'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] != 0) {
           if (is_array($aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')])) {
             $stock->whereIn('ws.whs_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
           }
           else {
             $stock->where('ws.whs_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
           }
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.BRANCH'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] != 0) {
           $stock->where('ws.branch_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.ID_YEAR'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] != 0) {
           $stock->where('ws.year_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.DATE'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] != 0) {
           $stock->where('ws.dt_date', '<=', $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.ID_MVT'), $aParameters)) {
               if (is_array($aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')])) {
                foreach ($aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] as $value) {
                  $stock->where('ws.mvt_id', '!=', $value);
                }
               }
               else if ($aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] != 0) {
                 $stock->where('ws.mvt_id', '!=',$aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')]);
               }
       }

       return $stock;
    }

   /**
    * [getLotsOfPallet This method returns an array with the rows of lots in a Pallet]
    * @param  integer $iPalletId
    * @param  integer $iWhsId    [This param is send when requires stock of a specific warehouse]
    * @return [result]   [array of result of query]
    */
    public static function getLotsOfPallet($iPalletId, $iWhsId = 0) {

        $select = 'ws.lot_id, wl.lot,sum(ws.input) as inputs,
                           sum(ws.output) as outputs,
                           sum(ws.input - ws.output) as stock,
                           AVG(ws.cost_unit) as cost_unit,
                           ei.code as item_code,
                           ei.name as item,
                           eu.code as unit';

        $stock = SStockManagment::getStockBaseQuery($select)
                      ->groupBy(['wp.id_pallet','ws.lot_id', 'ws.item_id', 'ws.unit_id'])
                      ->orderBy('ws.lot_id')
                      ->orderBy('ws.item_id')
                      ->where('ws.is_deleted', false)
                      ->where('ws.pallet_id', $iPalletId)
                      ->having('stock', '>', '0');

        if ($iWhsId != 0) {
            $stock->where('ws.whs_id', $iWhsId);
        }

        $stock = $stock->get();

        return $stock;
    }

   /**
    * [getStock description]
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
        * \Config::get('scwms.STOCK_PARAMS.SSELECT')
        * ]
    * @return [array] [aStock]
    */
    public static function getStock($aParameters = []) {
        if (! array_key_exists(\Config::get('scwms.STOCK_PARAMS.SSELECT'), $aParameters) ||
            (array_key_exists(\Config::get('scwms.STOCK_PARAMS.SSELECT'), $aParameters)
                && $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] == '')) {
          $sSelect = 'ws.lot_id, wl.lot,
                           sum(ws.input) as inputs,
                           sum(ws.output) as outputs,
                           sum(ws.input - ws.output) as stock,
                           AVG(ws.cost_unit) as cost_unit,
                           ei.code as item_code,
                           ei.name as item,
                           eu.code as unit_code,
                           ws.pallet_id,
                           ws.location_id
                           ';
          $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
        }

        $stock = session('stock')->getStockResult($aParameters, null)
                      ->groupBy(['ws.item_id', 'ws.unit_id']);
        $stock = $stock->get();

        if (sizeof($stock) > 0)
        {
          $aStock = array();
          $aStock[\Config::get('scwms.STOCK.GROSS')] = $stock[0]->stock;
          $aStock[\Config::get('scwms.STOCK.RELEASED')] = 0;
          $aStock[\Config::get('scwms.STOCK.SEGREGATED')] = $stock[0]->segregated;
          $aStock[\Config::get('scwms.STOCK.AVAILABLE')] = $stock[0]->stock - $stock[0]->segregated;
        }
        else
        {
          $aStock = array();
          $aStock[\Config::get('scwms.STOCK.GROSS')] = 0;
          $aStock[\Config::get('scwms.STOCK.RELEASED')] = 0;
          $aStock[\Config::get('scwms.STOCK.SEGREGATED')] = 0;
          $aStock[\Config::get('scwms.STOCK.AVAILABLE')] = 0;
        }

        return $aStock;
    }

    /**
     * [getSubSegregated description]
     * @param  [array] $aParameters [
         * \Config::get('scwms.STOCK_PARAMS.ITEM') String value
         * \Config::get('scwms.STOCK_PARAMS.UNIT') String value
         * \Config::get('scwms.STOCK_PARAMS.LOT')
         * \Config::get('scwms.STOCK_PARAMS.PALLET')
         * \Config::get('scwms.STOCK_PARAMS.LOCATION')
         * \Config::get('scwms.STOCK_PARAMS.WHS')
         * \Config::get('scwms.STOCK_PARAMS.BRANCH')
         * \Config::get('scwms.STOCK_PARAMS.ID_YEAR')
         * \Config::get('scwms.STOCK_PARAMS.DATE')
         * ]
     * @return [type]              [description]
     */
    public function getSubSegregated($aParameters = []) {

        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.AS_SEGREGATED'), $aParameters)) {
          $sSelect = '(COALESCE(
                      SUM(IF(wsr.segregation_mvt_type_id = '.\Config::get('scqms.SEGREGATION.INCREMENT').', wsr.quantity, 0)) -
                      SUM(IF(wsr.segregation_mvt_type_id = '.\Config::get('scqms.SEGREGATION.DECREMENT').', wsr.quantity, 0))
                      , 0)) AS '.$aParameters[\Config::get('scwms.STOCK_PARAMS.AS_SEGREGATED')];
        }
        else {
          $sSelect = '(COALESCE(
                      SUM(IF(wsr.segregation_mvt_type_id = '.\Config::get('scqms.SEGREGATION.INCREMENT').', wsr.quantity, 0)) -
                      SUM(IF(wsr.segregation_mvt_type_id = '.\Config::get('scqms.SEGREGATION.DECREMENT').', wsr.quantity, 0))
                      , 0))';
        }

        $sub = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_segregations AS wss')
                      ->join('wms_segregation_rows AS wsr', 'wss.id_segregation', '=', 'wsr.segregation_id')
                      ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event')
                      ->select(\DB::raw($sSelect))
                      ->whereRaw('wsr.year_id = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')]
                                  .' AND wsr.is_deleted = false');

        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.ITEM'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] <> '0') {
            $sub = $sub->whereRaw('wsr.item_id = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.UNIT'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] <> '0') {
            $sub = $sub->whereRaw('wsr.unit_id = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.BRANCH'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] <> '0') {
            $sub = $sub->whereRaw('wsr.branch_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.WHS'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] <> '0') {
            if (is_array($aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')])) {
              $sub = $sub->whereIn('wsr.whs_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
            }
            else {
              $sub = $sub->whereRaw('wsr.whs_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
            }
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.PALLET'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] <> '0') {
            $sub = $sub->whereRaw('wsr.pallet_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.LOT'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] <> '0') {
            $sub = $sub->whereRaw('wsr.lot_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.DATE'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] <> '0') {
            // $sub = $sub->whereRaw('wss.dt_date <= \''.$aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')].'\'');
        }

        return $sub;
    }

    /**
     * [getSuppliedRes description]
     * @param  [type] $iDocCategory [description]
     * @param  [type] $iDocClass    [description]
     * @param  [type] $iDocType     [description]
     * @param  [type] $FilterDel    [description]
     * @param  [type] $sFilterDate  [description]
     * @param  [type] $iViewType    [description]
     * @param  [type] $iDocId       [description]
     * @param  [type] $bWithPending [description]
     * @return [type]               [description]
     */
    public static function getSuppliedRes($iDocCategory, $iDocClass, $iDocType,
                                        $FilterDel, $sFilterDate, $iViewType,
                                        $iDocId, $bWithPending)
    {
        $lDocuments = SStockManagment::getBaseQuery($iDocCategory, $iDocClass, $iDocType);

        $sSubQueryInvoices = "(SELECT COUNT(*) supp_inv
                                FROM
                                wms_mvts WHERE doc_invoice_id IN
                                (SELECT id_document
                                FROM erpu_documents
                                WHERE doc_src_id = ed.id_document)
                                AND NOT is_deleted)";

        $sSubQueryOrders = "(SELECT COUNT(*) supp_ord
                                FROM
                                 wms_mvts
                                 WHERE doc_order_id = ed.doc_src_id
                                AND NOT is_deleted)";

        $sSubQueryCreditNotes = "(SELECT COUNT(*) supp_cn
                                FROM
                                wms_mvts
                                WHERE doc_invoice_id = ed.doc_src_id
                                AND NOT is_deleted)";

        if ($iViewType == \Config::get('scwms.DOC_VIEW.NORMAL'))
        {
            $bOrder = $iDocClass == \Config::get('scsiie.DOC_CLS.ORDER') &&
                          $iDocType == \Config::get('scsiie.DOC_TYPE.ORDER');

            $bInvoice = $iDocClass == \Config::get('scsiie.DOC_CLS.DOCUMENT') &&
                            $iDocType == \Config::get('scsiie.DOC_TYPE.INVOICE');

            $bCreditNote = $iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') &&
                            $iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE');

            $lDocuments =  $lDocuments->select('ed.id_document',
                                  'ed.dt_date',
                                  'ed.dt_doc',
                                  \DB::raw('CONCAT(ed.service_num, IF(ed.service_num = "", "", "-"), ed.num) as folio'),
                                  \DB::raw('CONCAT(edsrc.service_num, IF(edsrc.service_num = "", "", "-"), edsrc.num) as num_src'),
                                  'ed.is_closed',
                                  'ed.doc_src_id',
                                  'ed.doc_sys_status_id',
                                  'ed.external_id',
                                  'ed.partner_id',
                                  'ed.is_deleted',
                                  'ep.name',
                                  'ep.fiscal_id',
                                  'ep.code as cve_an',
                                  \DB::raw(($bOrder ? $sSubQueryInvoices : "'0'")." AS supp_inv"),
                                  \DB::raw(($bInvoice ? $sSubQueryOrders : "'0'")." AS supp_ord"),
                                  \DB::raw(($bCreditNote ? $sSubQueryCreditNotes : "'0'")." AS supp_cn"),
                                  \DB::raw('(SELECT SUM(quantity) FROM erpu_document_rows where document_id = ed.id_document) AS qty_doc'),
                                  \DB::raw('COALESCE(SUM(
                                              IF (wm.is_deleted IS NULL
                                              OR (wm.is_deleted IS NOT NULL
                                              AND wm.is_deleted = FALSE
                                              AND wmr.is_deleted = FALSE),
                                              wmr.quantity, 0)), 0) AS qty_sur'),
                                  \DB::raw('COALESCE(SUM(wisl.quantity), 0) AS qty_sur_ind'),
                                  \DB::raw('(SELECT 
                                                SUM(quantity)
                                            FROM
                                                erpu_document_rows
                                            WHERE
                                                document_id = ed.id_document) - (IF(wm.is_deleted = FALSE
                                                AND wmr.is_deleted = FALSE,
                                            SUM(wmr.quantity),
                                            0) + COALESCE(SUM(wisl.quantity), 0)) AS pending')
                          )
                          ->where('edr.is_deleted', false)
                          ->groupBy('edr.document_id')
                          ->orderBy('ed.dt_doc', 'DESC');
        }
        else
        {
            $lDocuments =  $lDocuments->select('ed.id_document',
                                  'ed.dt_date',
                                  'ed.dt_doc',
                                  \DB::raw('CONCAT(ed.service_num, IF(ed.service_num = "", "", "-"), ed.num) as folio'),
                                  \DB::raw('CONCAT(edsrc.service_num, IF(edsrc.service_num = "", "", "-"), edsrc.num) as num_src'),
                                  'ed.doc_src_id',
                                  'ed.doc_sys_status_id',
                                  'ed.is_closed',
                                  'ed.external_id',
                                  'ed.partner_id',
                                  'ed.is_deleted',
                                  'ep.name',
                                  'ep.fiscal_id',
                                  'ep.code as cve_an',
                                  'ei.code as cve_item',
                                  'ei.name as item',
                                  'eu.code as unit',
                                  'ei.is_lot',
                                  'ei.is_bulk',
                                  'ei.without_rotation',
                                  'edr.concept_key',
                                  'edr.concept',
                                  'edr.price_unit_cur',
                                  'eic.name as class_name',
                                  'edr.item_id',
                                  'edr.unit_id',
                                  'edr.id_document_row',
                                  \DB::raw("'0' AS supp_inv"),
                                  \DB::raw("'0' AS supp_ord"),
                                  \DB::raw("'0' AS supp_cn"),
                                  \DB::raw("'0' AS qty_ind_supp_row"),
                                  \DB::raw('edr.quantity AS qty_row'),
                                  \DB::raw('COALESCE(SUM(
                                              IF (wm.is_deleted IS NULL
                                              OR (wm.is_deleted IS NOT NULL
                                              AND wm.is_deleted = FALSE
                                              AND wmr.is_deleted = FALSE),
                                              wmr.quantity, 0)), 0) AS qty_sur'),
                                  \DB::raw('COALESCE(SUM(wisl.quantity), 0) AS qty_sur_ind'),
                                  \DB::raw('(edr.quantity - (COALESCE(SUM(
                                              IF (wm.is_deleted IS NULL
                                              OR (wm.is_deleted IS NOT NULL
                                              AND wm.is_deleted = FALSE
                                              AND wmr.is_deleted = FALSE),
                                              wmr.quantity, 0)), 0) + COALESCE(SUM(wisl.quantity), 0)))  AS pending')
                          )
                          ->where('edr.is_deleted', false)
                          ->groupBy('edr.id_document_row')
                          ->groupBy('edr.document_id')
                          ->orderBy('ed.dt_doc', 'DESC');
        }

        if ($iDocId != 0)
        {
            $lDocuments = $lDocuments->where('ed.id_document', $iDocId);
        }

        if (! $bWithPending)
        {
            $lDocuments = $lDocuments->havingRaw('pending > 0');
        }

        $lDocuments = SStockManagment::filterActives($lDocuments, $FilterDel);
        if (! is_null($sFilterDate))
        {
          $lDocuments = SStockManagment::filterDate($lDocuments, $sFilterDate);
        }

        return $lDocuments;
    }

    /**
     * [getBaseQuery description]
     * @param  [type] $iDocCategory [description]
     * @param  [type] $iDocClass    [description]
     * @return [type]               [description]
     */
    public static function getBaseQuery($iDocCategory, $iDocClass, $iDocType)
    {
       $sub = SStockManagment::getSubQuery($iDocCategory, $iDocClass, $iDocType);
       $query = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('erpu_documents AS ed')
                     ->join('erpu_documents AS edsrc', 'ed.doc_src_id', '=', 'edsrc.id_document')
                     ->join('erpu_document_rows AS edr', 'ed.id_document', '=', 'edr.document_id')
                     ->join('erpu_items AS ei', 'edr.item_id', '=', 'ei.id_item')
                     ->join('erpu_units AS eu', 'edr.unit_id', '=', 'eu.id_unit')
                     ->join('erpu_item_genders AS eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                     ->join('erps_item_classes AS eic', 'eig.item_class_id', '=', 'eic.id_item_class')
                     ->join('erpu_partners AS ep', 'ed.partner_id', '=', 'ep.id_partner')
                     ->leftJoin('wms_mvt_rows AS wmr',
                       function($join) use ($sub, $iDocClass)
                       {
                         $join->on('wmr.'.SStockManagment::getJoin($iDocClass), '=', 'edr.id_document_row')
                              ->on('edr.document_id', '=', \DB::raw("({$sub->toSql()})"));
                       })
                     ->mergeBindings($sub)
                     ->leftJoin('wms_indirect_supply_links AS wisl', 'edr.id_document_row', '=', 'wisl.des_doc_row_id')
                     ->leftJoin('wms_mvts AS wm', 'wmr.mvt_id', '=', 'wm.id_mvt')
                     ->where('ed.doc_category_id', $iDocCategory)
                     ->where('ed.doc_class_id', $iDocClass)
                     ->where('ed.doc_type_id', $iDocType)
                     // ->where(function ($query) {
                     //      $query->whereNull('wm.is_deleted')
                     //          ->orWhere(function ($query) {
                     //               $query->WhereNotNull('wm.is_deleted')
                     //                   ->where('wm.is_deleted', false);
                     //           });
                     //  })
                     ->where('eic.id_item_class', '!=', \Config::get('scsiie.ITEM_CLS.SPENDING'));

       return $query;
    }

    private static function getJoin($iDocClass)
    {
       switch ($iDocClass) {
         case \Config::get('scsiie.DOC_CLS.DOCUMENT'):
           return 'doc_invoice_row_id';
           break;
         case \Config::get('scsiie.DOC_CLS.ORDER'):
           return 'doc_order_row_id';
           break;
         case \Config::get('scsiie.DOC_CLS.ADJUST'):
           return 'doc_credit_note_row_id';
           break;

         default:
           return '';
           break;
       }
    }

    /**
     * [getSubQuery description]
     * @return [type] [description]
     */
    private static function getSubQuery($iDocCategory, $iDocClass, $iDocType)
    {
        $sub = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_mvts')
                      ->whereRaw('id_mvt = wmr.mvt_id');

        if (\Config::get('scsiie.DOC_CLS.ORDER') == $iDocClass && \Config::get('scsiie.DOC_TYPE.ORDER') == $iDocType) {
          $sub = $sub->select('doc_order_id');
        }
        if (\Config::get('scsiie.DOC_CLS.DOCUMENT') == $iDocClass && \Config::get('scsiie.DOC_TYPE.INVOICE') == $iDocType) {
          $sub = $sub->select('doc_invoice_id');
        }
        if (\Config::get('scsiie.DOC_CAT.PURCHASES') == $iDocCategory &&
            \Config::get('scsiie.DOC_CLS.ADJUST') == $iDocClass &&
            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE') == $iDocType) {
              $sub = $sub->select('doc_credit_note_id');
        }
        if (\Config::get('scsiie.DOC_CAT.SALES') == $iDocCategory &&
            \Config::get('scsiie.DOC_CLS.ADJUST') == $iDocClass &&
            \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE') == $iDocType) {
              $sub = $sub->select('doc_credit_note_id');
        }

        return $sub;
    }

    /**
     * [filterActives description]
     * @param  [type] $lDocuments [description]
     * @param  [type] $iFilterDel [description]
     * @return [type]             [description]
     */
    private static function filterActives($lDocuments, $iFilterDel)
    {
        switch ($iFilterDel)
        {
          case \Config::get('scsys.FILTER.ACTIVES'):
            $lDocuments = $lDocuments->where('ed.is_deleted', '=', \Config::get('scsys.STATUS.ACTIVE'));
            break;

          case \Config::get('scsys.FILTER.DELETED'):
            $lDocuments = $lDocuments->where('ed.is_deleted', '=', \Config::get('scsys.STATUS.DEL'));
            break;

          case \Config::get('scsys.FILTER.ALL'):
          // return $lDocuments;
            break;
        }

        return $lDocuments;
    }

    /**
     * [filterDate description]
     * @param  [type] $lDocuments [description]
     * @param  string $sDtFilter  [description]
     * @return [type]             [description]
     */
    public static function filterDate($lDocuments, $sDtFilter = '')
    {
        $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);
        $lDocuments =  $lDocuments->whereBetween('ed.dt_doc', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);


        return $lDocuments;
    }

    public static function getMovementsLots($lot){
      $sJanuary = session('work_date')->year.'-01-01';
      $sCutoffDate = session('work_date')->toDateString();
      $sSelect = 'wm.dt_date,
                  CONCAT(wmt.code, "-", wm.folio) AS folio,
                  wmt.code AS mvt_code,
                  wmt.name AS mvt_name,
                  wmr.pallet_id AS pallet,
                  eb.code AS branch_code,
                  ww.code AS whs_code,
                  wwl.code AS loc_code,
                  eu.code AS unit_code,
                  ed_ord.num AS num_order,
                  ed_ord.service_num AS ser_num_order,
                  ed_ord.dt_date AS dt_order,
                  ed_inv.num AS num_invoice,
                  ed_inv.service_num AS ser_num_invoice,
                  ed_inv.dt_date AS dt_invoice,
                  ed_cn.num AS num_cn,
                  ed_cn.service_num AS ser_num_cn,
                  ed_cn.dt_date AS dt_cn
                 ';

      $query = \DB::connection(session('db_configuration')->getConnCompany())
                    ->table('wms_mvts as wm')
                    ->join('wms_mvt_rows as wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                    ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                    ->join('erpu_items as ei', 'wmr.item_id', '=', 'ei.id_item')
                    ->join('erpu_units as eu', 'wmr.unit_id', '=', 'eu.id_unit')
                    ->join('wms_pallets as wp', 'wmr.pallet_id', '=', 'wp.id_pallet')
                    ->join('wmsu_whs_locations as wwl', 'wmr.location_id', '=', 'wwl.id_whs_location')
                    ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                    ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                    ->join('erpu_documents as ed_ord', 'wm.doc_order_id', '=', 'ed_ord.id_document')
                    ->join('erpu_documents as ed_inv', 'wm.doc_invoice_id', '=', 'ed_inv.id_document')
                    ->join('erpu_documents as ed_cn', 'wm.doc_credit_note_id', '=', 'ed_cn.id_document')
                    ->whereBetween('wm.dt_date', [$sJanuary, $sCutoffDate])
                    ->where('wm.is_deleted', false)
                    ->where('wmr.is_deleted', false);
      $query = $query->join('wms_mvt_row_lots as wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                     ->join('wms_lots as wl', 'wmrl.lot_id', '=', 'wl.id_lot');

      $sSelect = $sSelect.', wl.lot, wl.dt_expiry';
      $sSelect = $sSelect.', IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmrl.quantity, 0) AS inputs,
                             IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmrl.quantity, 0) AS outputs,
                             IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmrl.amount, 0) AS credit,
                             IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmrl.amount, 0) AS debit';

      $query->where('wmrl.lot_id', $lot->id_lot);
      $query = $query->select(\DB::raw($sSelect));

      $query = $query->orderBy('dt_date', 'ASC')
                      ->orderBy('id_mvt', 'ASC')
                      ->get();

      return $query;
      }

}
