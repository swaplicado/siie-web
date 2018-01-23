<?php namespace App\SCore;

/**
 *
 */
class SSegregationCore
{

  function __construct()
  {
    # code...
  }

  public function getSegregated($iSegregationType)
  {
      $sSelect = '
                  ei.id_item,
                  eu.id_unit,
                  wl.id_lot,
                  wp.id_pallet,
                  ww.id_whs,
                  ww.branch_id,
                  ed.id_document,
                  qqs.id_status,
                  ei.code as item_code,
                  ei.name as item,
                  eu.code as unit,
                  wsr.quantity AS qty,
                  COALESCE(wl.lot, \'N/A\') AS lot_name,
                  SUM(IF(wsr.move_type_id = 1, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) AS increment,
    SUM(IF(wsr.move_type_id = 2, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) AS decrement,
    SUM(IF(wsr.move_type_id = 1, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) - SUM(IF(wsr.move_type_id = 2, IF(id_lot is null, wsr.quantity, wslr.quantity), 0)) AS segregated,
                  wp.pallet,
                  ww.name AS warehouse,
                  qqs.name AS status_qlty,
                  qqs.id_status,
                  ed.num AS num_doc';

      $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_segregations AS ws')
                  ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                  ->leftJoin('wms_seg_lot_rows AS wslr', 'wsr.id_segregation_row', '=', 'wslr.segregation_row_id')
                  ->join('erpu_items AS ei', 'wsr.item_id', '=', 'ei.id_item')
                  ->join('erpu_units AS eu', 'wsr.unit_id', '=', 'eu.id_unit')
                  ->leftJoin('wms_lots AS wl', 'wslr.lot_id', '=', 'wl.id_lot')
                  ->join('wms_pallets AS wp', 'wsr.pallet_id', '=', 'wp.id_pallet')
                  ->join('wmsu_whs AS ww', 'wsr.whs_id', '=', 'ww.id_whs')
                  ->join('qmss_quality_status AS qqs', 'wsr.quality_status_id', '=', 'qqs.id_status')
                  ->join('erpu_documents AS ed', 'ws.reference_id', '=', 'ed.id_document')
                  ->where('ei.is_deleted', false)
                  ->where('ws.is_deleted', false)
                  ->where('ws.segregation_type_id', $iSegregationType)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('id_item',
                            'id_unit',
                            'id_lot',
                            'id_pallet',
                            'ww.id_whs',
                            'qqs.id_status'
                            )
                  ->having('segregated', '>', 0)
                  ->get();

      return $query;
  }

  public function isRelease($iStatus)
  {
      return $iStatus == \Config::get('scqms.PARTIAL_RELEASED') ||
              $iStatus == \Config::get('scqms.RELEASED') ||
                $iStatus == \Config::get('scqms.RELEASED_EARLY');
  }


}
