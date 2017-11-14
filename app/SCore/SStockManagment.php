<?php namespace App\SCore;

/**
 * this class manages the stock of company
 */
class SStockManagment
{
    /*
    * This method returns an array with the rows of lots in a Pallet
    */
    public static function getLotsOfPallet($iPalletId) {

      $select = 'ws.lot_id, wl.lot,sum(ws.input) as inputs,
                         sum(ws.output) as outputs,
                         (sum(ws.input) - sum(ws.output)) as stock,
                         AVG(ws.cost_unit) as cost_unit,
                         ei.code as item_code,
                         ei.name as item,
                         eu.code as unit';

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
                    ->having('stock', '>', '0')
                    // ->get(['ws.lot_id', 'inputs', 'outputs','stock']);
                    ->get();

    //  \Debugbar::info($stock);

      return $stock;
    }

}
