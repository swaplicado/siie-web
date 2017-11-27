<?php namespace App\SCore;

/**
 * this class manages the stock of company
 */
class SStockManagment
{

   /**
    * [getLotsOfPallet This method returns an array with the rows of lots in a Pallet]
    * @param  integer $iPalletId
    * @param  integer $iWhsId    [This param is send when requires stock of a specific warehouse]
    * @return [result]   [array of result of query]
    */
    public static function getLotsOfPallet($iPalletId = 0, $iWhsId = 0) {

        $select = 'ws.lot_id, wl.lot,sum(ws.input) as inputs,
                           sum(ws.output) as outputs,
                           (sum(ws.input) - sum(ws.output)) as stock,
                           AVG(ws.cost_unit) as cost_unit,
                           ei.code as item_code,
                           ei.name as item,
                           eu.code as unit';

        \Debugbar::info($select);

        $stock = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_stock as ws')
                      ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                      ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                      ->join('wms_pallets as wp', 'ws.pallet_id', '=', 'wp.id_pallet')
                      ->join('wms_lots as wl', 'ws.lot_id', '=', 'wl.id_lot')
                      ->join('wmsu_whs_locations as wwl', 'ws.location_id', '=', 'wwl.id_whs_location')
                      ->join('wmsu_whs as ww', 'ws.whs_id', '=', 'ww.id_whs')
                      ->select(\DB::raw($select))
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

         \Debugbar::info($stock);

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
        \Debugbar::info($select);

        $stock = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_stock as ws')
                      ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                      ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                      ->join('wms_pallets as wp', 'ws.pallet_id', '=', 'wp.id_pallet')
                      ->join('wms_lots as wl', 'ws.lot_id', '=', 'wl.id_lot')
                      ->join('wmsu_whs_locations as wwl', 'ws.location_id', '=', 'wwl.id_whs_location')
                      ->join('wmsu_whs as ww', 'ws.whs_id', '=', 'ww.id_whs')
                      ->select(\DB::raw($select))
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

}
