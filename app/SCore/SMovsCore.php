<?php namespace App\SCore;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Database\Config;
use App\SUtils\SGuiUtils;

use App\ERP\SYear;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

/**
 *
 */
class SMovsCore {

    public static function getInventoryDocs($sDtFilter = '', $iFilterWhs = 0)
    {
        $sSelect = '
                wm.id_mvt,
                wmt.code AS mov_code,
                wm.folio AS mov_folio,
                wm.dt_date AS mov_date,
                wm.total_amount,
                wm.mvt_whs_class_id,
                wmt.name AS movement,
                wm.mvt_trn_type_id,
                wmtt.code AS trn_code,
                wmtt.name AS trn_name,
                wm.mvt_adj_type_id,
                wmat.code AS adj_code,
                wmat.name AS adj_name,
                wm.mvt_mfg_type_id,
                wmmt.code AS mfg_code,
                wmmt.name AS mfg_name,
                wm.mvt_exp_type_id,
                wmet.code AS exp_code,
                wmet.name AS exp_name,
                eb.code AS branch_code,
                eb.name AS branch,
                ww.code AS whs_code,
                ww.name AS warehouse,
                ed_ord.num AS num_order,
                ed_ord.service_num AS ser_num_order,
                ed_ord.dt_date AS dt_order,
                ed_ord.doc_category_id AS order_category_id,
                ed_inv.num AS num_invoice,
                ed_inv.service_num AS ser_num_invoice,
                ed_inv.dt_date AS dt_invoice,
                ed_inv.doc_category_id AS invoice_category_id,
                ed_cn.num AS num_cn,
                ed_cn.service_num AS ser_num_cn,
                ed_cn.dt_date AS dt_cn,
                ed_cn.doc_category_id AS cn_category_id,
                wm.doc_order_id,
                wm.doc_invoice_id,
                wm.doc_credit_note_id,
                wm.doc_debit_note_id,
                wm.is_deleted,
                wm.created_at,
                wm.updated_at,
                wm.created_by_id,
                wm.updated_by_id,
                uc.username AS username_creation,
                uu.username AS username_update
                ';

          $movs = \DB::connection(session('db_configuration')->getConnCompany())
                       ->table('wms_mvts as wm')
                       ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                       ->join('wmss_mvt_trn_types as wmtt', 'wm.mvt_trn_type_id', '=', 'wmtt.id_mvt_trn_type')
                       ->join('wmss_mvt_adj_types as wmat', 'wm.mvt_adj_type_id', '=', 'wmat.id_mvt_adj_type')
                       ->join('wmss_mvt_mfg_types as wmmt', 'wm.mvt_mfg_type_id', '=', 'wmmt.id_mvt_mfg_type')
                       ->join('wmss_mvt_exp_types as wmet', 'wm.mvt_exp_type_id', '=', 'wmet.id_mvt_exp_type')
                       ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                       ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                       ->join('erpu_documents as ed_ord', 'wm.doc_order_id', '=', 'ed_ord.id_document')
                       ->join('erpu_documents as ed_inv', 'wm.doc_invoice_id', '=', 'ed_inv.id_document')
                       ->join('erpu_documents as ed_cn', 'wm.doc_credit_note_id', '=', 'ed_cn.id_document')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'wm.created_by_id', '=', 'uc.id')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'wm.updated_by_id', '=', 'uu.id');

           if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
               $movs = $movs->where('wm.whs_id', $iFilterWhs);
           }

           $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);

           $movs = $movs->whereBetween('wm.dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()])
                         ->select(\DB::raw($sSelect))
                         ->where('is_system', false)
                         ->where('wm.branch_id', session('branch')->id_branch)
                         ->groupBy('id_mvt')
                         ->get();

           return $movs;
    }

    public static function getMovementsIndex($sDtFilter = '', $iFilterWhs = 0)
    {
        $sSelect = '
                wm.id_mvt,
                wmr.id_mvt_row,
                wmt.code AS mov_code,
                wm.folio AS mov_folio,
                wm.dt_date AS mov_date,
                ei.code AS item_code,
                ei.name AS item,
                eu.code AS unit_code,
                wmr.quantity,
                eb.code AS branch_code,
                eb.name AS branch,
                ww.code AS whs_code,
                ww.name AS warehouse,
                wmt.name AS movement,
                wm.mvt_whs_class_id,
                wm.mvt_trn_type_id,
                wmtt.code AS trn_code,
                wmtt.name AS trn_name,
                wm.mvt_adj_type_id,
                wmat.code AS adj_code,
                wmat.name AS adj_name,
                wm.mvt_mfg_type_id,
                wmmt.code AS mfg_code,
                wmmt.name AS mfg_name,
                wm.mvt_exp_type_id,
                wmet.code AS exp_code,
                wmet.name AS exp_name,
                ed_ord.num AS num_order,
                ed_ord.service_num AS ser_num_order,
                ed_ord.dt_date AS dt_order,
                ed_ord.doc_category_id AS order_category_id,
                ed_inv.num AS num_invoice,
                ed_inv.service_num AS ser_num_invoice,
                ed_inv.dt_date AS dt_invoice,
                ed_inv.doc_category_id AS invoice_category_id,
                ed_cn.num AS num_cn,
                ed_cn.service_num AS ser_num_cn,
                ed_cn.dt_date AS dt_cn,
                ed_cn.doc_category_id AS cn_category_id,
                wm.doc_order_id,
                wm.doc_invoice_id,
                wm.doc_credit_note_id,
                wm.doc_debit_note_id,
                wm.is_deleted,
                wm.created_at,
                wm.updated_at,
                wm.created_by_id,
                wm.updated_by_id,
                uc.username AS username_creation,
                uu.username AS username_update
                ';

          $movs = \DB::connection(session('db_configuration')->getConnCompany())
                       ->table('wms_mvt_rows as wmr')
                       ->join('wms_mvts as wm', 'wmr.mvt_id', '=', 'wm.id_mvt')
                       ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                       ->join('erpu_items as ei', 'wmr.item_id', '=', 'ei.id_item')
                       ->join('erpu_units as eu', 'wmr.unit_id', '=', 'eu.id_unit')
                       ->join('wms_pallets as wp', 'wmr.pallet_id', '=', 'wp.id_pallet')
                       ->join('wmss_mvt_trn_types as wmtt', 'wm.mvt_trn_type_id', '=', 'wmtt.id_mvt_trn_type')
                       ->join('wmss_mvt_adj_types as wmat', 'wm.mvt_adj_type_id', '=', 'wmat.id_mvt_adj_type')
                       ->join('wmss_mvt_mfg_types as wmmt', 'wm.mvt_mfg_type_id', '=', 'wmmt.id_mvt_mfg_type')
                       ->join('wmss_mvt_exp_types as wmet', 'wm.mvt_exp_type_id', '=', 'wmet.id_mvt_exp_type')
                       ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                       ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                       ->join('erpu_documents as ed_ord', 'wm.doc_order_id', '=', 'ed_ord.id_document')
                       ->join('erpu_documents as ed_inv', 'wm.doc_invoice_id', '=', 'ed_inv.id_document')
                       ->join('erpu_documents as ed_cn', 'wm.doc_credit_note_id', '=', 'ed_cn.id_document')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'wm.created_by_id', '=', 'uc.id')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'wm.updated_by_id', '=', 'uu.id');

           if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
               $movs = $movs->where('wm.whs_id', $iFilterWhs);
           }

           $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);

           $movs = $movs->whereBetween('wm.dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()])
                         ->select(\DB::raw($sSelect))
                         ->where('wmr.is_deleted', false)
                         ->where('wm.is_deleted', false)
                         ->where('wm.branch_id', session('branch')->id_branch)
                         ->groupBy('id_mvt_row')
                         ->get();

           return $movs;
    }

    public static function getMovsDetailIndex($sDtFilter = '', $iFilterWhs = 0)
    {
      $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);

      $sSelect = 'ei.code AS item_code,
                  ei.name AS item,
                  ei.is_lot,
                  wl.id_lot,
                  wl.lot,
                  wl.dt_expiry,
                  eu.code AS unit,
                  eb.code AS branch_code,
                  ww.code AS whs_code,
                  wm.mvt_whs_class_id,
                  wm.dt_date AS mov_date,
                  wm.folio AS mov_folio,
                  wmt.code AS mov_code,
                  wmt.name AS movement,
                  wmr.quantity AS row_quantity,
                  wmrl.quantity AS lot_quantity,
                  ed_ord.num AS num_order,
                  ed_ord.service_num AS ser_num_order,
                  ed_ord.dt_date AS dt_order,
                  ed_inv.num AS num_invoice,
                  ed_inv.service_num AS ser_num_invoice,
                  ed_inv.dt_date AS dt_invoice,
                  ed_cn.num AS num_cn,
                  ed_cn.service_num AS ser_num_cn,
                  ed_cn.dt_date AS dt_cn,
                  wm.doc_order_id,
                  wm.doc_invoice_id,
                  wm.doc_credit_note_id
                  ';

      $movs = \DB::connection(session('db_configuration')->getConnCompany())
                   ->table('wms_mvts as wm')
                   ->join('wms_mvt_rows as wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                   ->join('wms_mvt_row_lots as wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                   ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                   ->join('erpu_items as ei', 'wmr.item_id', '=', 'ei.id_item')
                   ->join('erpu_units as eu', 'wmr.unit_id', '=', 'eu.id_unit')
                   ->join('wms_pallets as wp', 'wmr.pallet_id', '=', 'wp.id_pallet')
                   ->join('wms_lots as wl', 'wmrl.lot_id', '=', 'wl.id_lot')
                   ->join('wmsu_whs_locations as wwl', 'wmr.location_id', '=', 'wwl.id_whs_location')
                   ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                   ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                   ->join('erpu_documents as ed_ord', 'wm.doc_order_id', '=', 'ed_ord.id_document')
                   ->join('erpu_documents as ed_inv', 'wm.doc_invoice_id', '=', 'ed_inv.id_document')
                   ->join('erpu_documents as ed_cn', 'wm.doc_credit_note_id', '=', 'ed_cn.id_document');

       if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
           $movs = $movs->where('wm.whs_id', $iFilterWhs);
       }

       $movs = $movs->whereBetween('wm.dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()])
                     ->select(\DB::raw($sSelect))
                     ->where('wm.is_deleted', false)
                     ->where('wmr.is_deleted', false)
                     ->where('wm.branch_id', session('branch')->id_branch)
                     ->groupBy('id_mvt', 'id_mvt_row', 'id_mvt_row_lot')
                     ->get();

       return $movs;
    }

}
