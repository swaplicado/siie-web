<?php namespace App\SCore;

use Carbon\Carbon;
use App\ERP\SYear;

/**
 *
 */
class SInventoryCore {

    public function getWarehouseStock($iWarehouse = 0) {
      $tToday = Carbon::today();
      $iYear = $tToday->year;
      $oYear = SYear::where('year', $iYear)
                      ->where('is_deleted', false)
                      ->first();

      $sSelect = "ws.branch_id,
                  ws.whs_id,
                  ws.location_id,
                  ws.pallet_id,
                  ws.lot_id,
                  ws.item_id,
                  ws.unit_id,
                  ei.code AS item_code,
                  ei.name AS item,
                  eu.code AS unit,
                  CONCAT(wwl.code, '-', wwl.name) AS location,
                  wp.id_pallet AS pallet,
                  wl.lot,
                  wl.dt_expiry,
                  ei.is_lot,
                  sum(ws.input) as inputs,
                  sum(ws.output) as outputs,
                  (sum(ws.input) - sum(ws.output)) as stock
                  ";
      $aParameters = array();

      $aParameters [\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
      $aParameters [\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWarehouse;
      $aParameters [\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oYear->id_year;
      $aParameters [\Config::get('scwms.STOCK_PARAMS.DATE')] = $tToday->toDateTimeString();

      $aParametersSeg = array();

      $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
      $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWarehouse;
      $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oYear->id_year;
      $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.DATE')] = $tToday->toDateTimeString();
      $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.LOT')] = 'ws.lot_id';
      $aParametersSeg [\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'ws.pallet_id';

      $lStock = session('stock')->getStockResult($aParameters, $aParametersSeg);

      $lStock = $lStock->having('stock', '>', 0)
                          ->groupBy(['ws.branch_id',
                                    'ws.whs_id',
                                    'ws.location_id',
                                    'ws.pallet_id',
                                    'ws.lot_id',
                                    'ws.item_id',
                                    'ws.unit_id'
                                  ])
                          ->orderBy('ws.item_id', 'ASC')
                          ->orderBy('ws.unit_id', 'ASC');

       $lStock = $lStock->get();

       return $lStock;
    }
}
