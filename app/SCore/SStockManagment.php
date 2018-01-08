<?php namespace App\SCore;

use Carbon\Carbon;

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
    public static function getStockBaseQuery($sSelect = 'ws.item_id')
    {
        $query = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_stock as ws')
                      ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                      ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                      ->join('wms_pallets as wp', 'ws.pallet_id', '=', 'wp.id_pallet')
                      ->join('wms_lots as wl', 'ws.lot_id', '=', 'wl.id_lot')
                      ->join('wmsu_whs_locations as wwl', 'ws.location_id', '=', 'wwl.id_whs_location')
                      ->join('wmsu_whs as ww', 'ws.whs_id', '=', 'ww.id_whs')
                      ->join('erpu_branches as eb', 'ws.branch_id', '=', 'eb.id_branch')
                      ->where('ei.is_deleted', false)
                      ->where('eu.is_deleted', false)
                      ->select(\DB::raw($sSelect));

        return $query;
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
                           (sum(ws.input) - sum(ws.output)) as stock,
                           AVG(ws.cost_unit) as cost_unit,
                           ei.code as item_code,
                           ei.name as item,
                           eu.code as unit';

        $stock = SStockManagment::getStockBaseQuery($select)
                      ->groupBy(['ws.lot_id', 'ws.item_id', 'ws.unit_id'])
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
        * ]
    * @return [array] [aStock]
    */
    public static function getStock($aParameters = []) {

        $select = 'ws.lot_id, wl.lot,
                           sum(ws.input) as inputs,
                           sum(ws.output) as outputs,
                           (sum(ws.input) - sum(ws.output)) as stock,
                           AVG(ws.cost_unit) as cost_unit,
                           ei.code as item_code,
                           ei.name as item,
                           eu.code as unit_code,
                           ws.pallet_id,
                           ws.location_id
                           ';

        $stock = SStockManagment::getStockBaseQuery($select)
                      ->groupBy(['ws.item_id', 'ws.unit_id'])
                      ->where('ws.is_deleted', false);

        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] != 0) {
            $stock->where('ws.item_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')]);
        }
        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] != 0) {
            $stock->where('ws.unit_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')]);
        }
        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] != 0) {
            $stock->where('ws.lot_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')]);
        }
        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] != 0) {
            $stock->where('ws.pallet_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')]);
        }
        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] != 0) {
            $stock->where('ws.location_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')]);
        }
        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] != 0) {
            $stock->where('ws.whs_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
        }
        if ($aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] != 0) {
            $stock->where('ws.branch_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')]);
        }

        $stock = $stock->get();

        if (sizeof($stock) > 0)
        {
          $aStock = array();
          $aStock[\Config::get('scwms.STOCK.RELEASED')] = 0;
          $aStock[\Config::get('scwms.STOCK.SEGREGATED')] = 0.0;
          $aStock[\Config::get('scwms.STOCK.AVAILABLE')] = $stock[0]->stock;
        }
        else
        {
          $aStock = array();
          $aStock[\Config::get('scwms.STOCK.RELEASED')] = 0;
          $aStock[\Config::get('scwms.STOCK.SEGREGATED')] = 0.0;
          $aStock[\Config::get('scwms.STOCK.AVAILABLE')] = 0;
        }

        return $aStock;
    }

    public static function getSupplied($iDocCategory, $iDocClass, $iDocType, $FilterDel, $sFilterDate, $iViewType, $iDocId)
    {
        $lDocuments = SStockManagment::getBaseQuery($iDocCategory, $iDocClass, $iDocType);

        if ($iViewType == \Config::get('scwms.DOC_VIEW.NORMAL'))
        {
            $lDocuments =  $lDocuments->select('id_document',
                                  'dt_date',
                                  'dt_doc',
                                  'num',
                                  'is_closed',
                                  'ed.external_id',
                                  'ed.partner_id',
                                  'ed.is_deleted',
                                  'ep.name',
                                  'ep.fiscal_id',
                                  'ep.code as cve_an',
                                  \DB::raw('SUM(edr.quantity) AS qty_doc'),
                                  \DB::raw('COALESCE(SUM(wmr.quantity), 0) AS qty_sur'),
                                  \DB::raw('(COALESCE(SUM(wmr.quantity), 0) * 100)/SUM(edr.quantity) AS advance'),
                                  \DB::raw('(SUM(edr.quantity) - COALESCE(SUM(wmr.quantity), 0))  AS pending')
                          )
                          ->groupBy('edr.document_id')
                          ->orderBy('dt_doc', 'desc');
        }
        else
        {
            $lDocuments =  $lDocuments->select('id_document',
                                  'dt_date',
                                  'dt_doc',
                                  'num',
                                  'is_closed',
                                  'ed.external_id',
                                  'ed.partner_id',
                                  'ed.is_deleted',
                                  'ep.name',
                                  'ep.fiscal_id',
                                  'ep.code as cve_an',
                                  'ei.code as cve_item',
                                  'ei.name as item',
                                  'eu.code as unit',
                                  'edr.concept_key',
                                  'edr.concept',
                                  'edr.price_unit_cur',
                                  'eic.name as class_name',
                                  \DB::raw('edr.quantity AS qty_row'),
                                  \DB::raw('COALESCE(SUM(wmr.quantity), 0) AS qty_sur'),
                                  \DB::raw('(COALESCE(SUM(wmr.quantity), 0) * 100)/edr.quantity AS advance'),
                                  \DB::raw('(edr.quantity - COALESCE(SUM(wmr.quantity), 0))  AS pending')
                          )
                          ->groupBy('edr.id_document_row')
                          ->groupBy('edr.document_id')
                          ->orderBy('document_id', 'document_id');
        }

        if ($iDocId != 0)
        {
            $lDocuments = $lDocuments->where('id_document', $iDocId);
        }

        $lDocuments = SStockManagment::filterActives($lDocuments, $FilterDel);
        if (! is_null($sFilterDate))
        {
          $lDocuments = SStockManagment::filterDate($lDocuments, $sFilterDate);
        }
        $lDocuments = $lDocuments->get();

        return $lDocuments;
    }

    /**
     * [getBaseQuery description]
     * @param  [type] $iDocCategory [description]
     * @param  [type] $iDocClass    [description]
     * @return [type]               [description]
     */
    private static function getBaseQuery($iDocCategory, $iDocClass, $iDocType)
    {
       $sub = SStockManagment::getSubQuery($iDocCategory, $iDocClass, $iDocType);
       $query = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('erpu_documents AS ed')
                     ->join('erpu_document_rows AS edr', 'ed.id_document', '=', 'edr.document_id')
                     ->join('erpu_items AS ei', 'edr.item_id', '=', 'ei.id_item')
                     ->join('erpu_units AS eu', 'edr.unit_id', '=', 'eu.id_unit')
                     ->join('erpu_item_genders AS eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                     ->join('erps_item_classes AS eic', 'eig.item_class_id', '=', 'eic.id_item_class')
                     ->join('erpu_partners AS ep', 'ed.partner_id', '=', 'ep.id_partner')
                     ->leftJoin('wms_mvt_rows AS wmr',
                       function($join) use ($sub)
                       {
                         $join->on('wmr.doc_order_row_id', '=', 'edr.id_document_row')
                              ->on('edr.document_id', '=', \DB::raw("({$sub->toSql()})"));
                       })
                     ->mergeBindings($sub)
                     ->where('doc_category_id', $iDocCategory)
                     ->where('doc_class_id', $iDocClass)
                     ->where('doc_type_id', $iDocType);

       return $query;
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
              $sub = $sub->select('doc_debit_note_id');
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
        $sFilterDate = $sDtFilter == null ? \Config::get('scsys.FILTER.MONTH') : $sDtFilter;

        if (! is_null($sFilterDate) && $sFilterDate != '') {
            $sStartDate = substr($sFilterDate, 0, 10);
            $sStartDate = str_replace('/', '-', $sStartDate);
            $sEndDate = substr($sFilterDate, -10, 10);
            $sEndDate = str_replace('/', '-', $sEndDate);
            $dt = Carbon::parse($sStartDate);
            $dtF = Carbon::parse($sEndDate);
            $lDocuments =  $lDocuments->whereBetween('dt_doc', [$dt->toDateString(), $dtF->toDateString()]);
        }

        return $lDocuments;
    }

}
