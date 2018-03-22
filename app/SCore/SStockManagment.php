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
                      ->join('wmsu_whs as ww', 'ws.whs_id', '=', 'ww.id_whs')
                      ->join('erpu_branches as eb', 'ws.branch_id', '=', 'eb.id_branch')
                      ->where('ei.is_deleted', false)
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

        // $sub = session('stock')->getSubSegregated($aSegregationParameters);
        // $sSelect = $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')].', ('.($sub->toSql()).') as segregated';
        $sSelect = $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')].', ("0") as segregated';

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
           $stock->where('ws.whs_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.BRANCH'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] != 0) {
           $stock->where('ws.branch_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.ID_YEAR'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] != 0) {
           $stock->where('ws.year_id', $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')]);
       }
       if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.ID_MVT'), $aParameters) &&
             $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] != 0) {
           $stock->where('ws.mvt_id', '!=',$aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')]);
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
                           (sum(ws.input) - sum(ws.output)) as stock,
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
        if (! array_key_exists(\Config::get('scwms.STOCK_PARAMS.SSELECT'), $aParameters)) {
          $sSelect = 'ws.lot_id, wl.lot,
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

        $sSelect = '(COALESCE(
                    SUM(IF(wsr.move_type_id = 1,
                            IF((SELECT is_lot FROM erpu_items WHERE id_item = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')].'),
                                  wslr.quantity,
                                  wsr.quantity),
                        0)) -
                    SUM(IF(wsr.move_type_id = 2,
                            IF((SELECT is_lot FROM erpu_items WHERE id_item = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')].'),
                                  wslr.quantity,
                                  wsr.quantity),
                        0))
                    , 0))';

        $sub = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_segregations AS ws')
                      ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                      ->leftJoin('wms_seg_lot_rows AS wslr', 'wsr.id_segregation_row', '=', 'wslr.segregation_row_id')
                      ->select(\DB::raw($sSelect))
                      // ->whereRaw('ws.segregation_type_id !='.\Config::get('scqms.SEGREGATION_TYPE.QUALITY'))
                      ->whereRaw('wsr.quality_status_id !='.\Config::get('scqms.RELEASED'))
                      ->whereRaw('wsr.quality_status_id !='.\Config::get('scqms.PARTIAL_RELEASED'))
                      ->whereRaw('wsr.quality_status_id !='.\Config::get('scqms.RELEASED_EARLY'))
                      ->whereRaw('wsr.year_id = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')])
                      ->whereRaw('wsr.item_id = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')])
                      ->whereRaw('wsr.unit_id = '.$aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')]);

        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.BRANCH'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] <> '0') {
            $sub = $sub->whereRaw('wsr.branch_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.WHS'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] <> '0') {
            $sub = $sub->whereRaw('wsr.whs_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.PALLET'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] <> '0') {
            $sub = $sub->whereRaw('wsr.pallet_id ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.LOT'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] <> '0') {
            $sub = $sub->whereRaw('COALESCE(wslr.lot_id, 1) ='.$aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')]);
        }
        if (array_key_exists(\Config::get('scwms.STOCK_PARAMS.DATE'), $aParameters) &&
              $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] <> '0') {
            $sub = $sub->whereRaw('ws.dt_date <= \''.$aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')].'\'');
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

        if ($iViewType == \Config::get('scwms.DOC_VIEW.NORMAL'))
        {
            $lDocuments =  $lDocuments->select('ed.id_document',
                                  'ed.dt_date',
                                  'ed.dt_doc',
                                  'ed.num',
                                  'ed.is_closed',
                                  'ed.doc_src_id',
                                  'edsrc.num as num_src',
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
                          ->orderBy('ed.dt_doc', 'DESC');
        }
        else
        {
            $lDocuments =  $lDocuments->select('ed.id_document',
                                  'ed.dt_date',
                                  'ed.dt_doc',
                                  'ed.num',
                                  'edsrc.num as num_src',
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
                                  'edr.concept_key',
                                  'edr.concept',
                                  'edr.price_unit_cur',
                                  'eic.name as class_name',
                                  'edr.item_id',
                                  'edr.unit_id',
                                  'edr.id_document_row',
                                  \DB::raw('edr.quantity AS qty_row'),
                                  \DB::raw('COALESCE(SUM(wmr.quantity), 0) AS qty_sur'),
                                  \DB::raw('(COALESCE(SUM(wmr.quantity), 0) * 100)/edr.quantity AS advance'),
                                  \DB::raw('(edr.quantity - COALESCE(SUM(wmr.quantity), 0))  AS pending')
                          )
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
    private static function getBaseQuery($iDocCategory, $iDocClass, $iDocType)
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
                     ->where('ed.doc_category_id', $iDocCategory)
                     ->where('ed.doc_class_id', $iDocClass)
                     ->where('ed.doc_type_id', $iDocType)
                     ->where('eic.id_item_class', '!=', \Config::get('scsiie.ITEM_CLS.SPENDING'));

       return $query;
    }

    private static function getJoin($iDocClass)
    {
       if ($iDocClass == \Config::get('scsiie.DOC_CLS.DOCUMENT'))
       {
          return 'doc_invoice_row_id';
       }
       else
       {
         return 'doc_order_row_id';
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
        $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);
        $lDocuments =  $lDocuments->whereBetween('ed.dt_doc', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);


        return $lDocuments;
    }

}
