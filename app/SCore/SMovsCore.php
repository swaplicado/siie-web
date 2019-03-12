<?php namespace App\SCore;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Database\Config;
use App\SUtils\SGuiUtils;

use App\ERP\SYear;
use App\ERP\SItem;

use App\WMS\SMvtType;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

/**
 *
 */
class SMovsCore {

    public static function getInventoryDocs($sDtFilter = '', $iFilterWhs = 0, $iFilter = 0)
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
                epo.folio AS prod_ord_folio,
                wm.doc_order_id,
                wm.doc_invoice_id,
                wm.doc_credit_note_id,
                wm.doc_debit_note_id,
                wm.prod_ord_id,
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
                       ->join('mms_production_orders as epo', 'wm.prod_ord_id', '=', 'epo.id_order')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'wm.created_by_id', '=', 'uc.id')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'wm.updated_by_id', '=', 'uu.id');

           if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
               $movs = $movs->where('wm.whs_id', $iFilterWhs);
           }

           switch ($iFilter) {
             case \Config::get('scsys.FILTER.ACTIVES'):
                 $movs = $movs->where('wm.is_deleted', \Config::get('scsys.STATUS.ACTIVE'));
               break;

             case \Config::get('scsys.FILTER.DELETED'):
                 $movs = $movs->where('wm.is_deleted', \Config::get('scsys.STATUS.DEL'));
               break;

             default:
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
                wm.prod_ord_id,
                mpo.folio AS po_folio,
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
                       ->join('mms_production_orders as mpo', 'wm.prod_ord_id', '=', 'mpo.id_order')
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
                  wmr.pallet_id AS pallet,
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
                  wm.prod_ord_id,
                  mpo.folio AS po_folio,
                  wm.doc_order_id,
                  wm.doc_invoice_id,
                  wm.doc_credit_note_id,
                  wm.created_at,
                  wm.updated_at
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
                   ->join('mms_production_orders as mpo', 'wm.prod_ord_id', '=', 'mpo.id_order')
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

    public static function canTheItemBeMoved($iItem = 0, $iMovType = 0)
    {
        $lErrors = array();
        $oItem = SItem::find($iItem);
        $oMovementType = SMvtType::find($iMovType);

        switch ($oItem->item_status_id) {
          case \Config::get('scsiie.ITEM_STATUS.ACTIVE'):
            return true;

          case \Config::get('scsiie.ITEM_STATUS.LOCKED'):
            return ['Error, el material/producto '.$oItem->name.' está bloqueado'];

          case \Config::get('scsiie.ITEM_STATUS.RESTRICTED'):
            if ($oMovementType->mvt_class_id == \Config::get('scwms.MVT_CLS_IN')
                  && $oMovementType->id_mvt_type != \Config::get('scwms.MVT_TP_IN_ADJ')
                    && $oMovementType->id_mvt_type != \Config::get('scwms.MVT_TP_IN_TRA')
                      && $oMovementType->id_mvt_type != \Config::get('scwms.MVT_TP_IN_SAL')) {
              return ['Error, el material/producto '.$oItem->name.' está restringido'];
            }

            return true;

          default:
            break;
        }

        return ['Error en el estatus del material/producto'];
    }

    /**
     * Undocumented function
     *
     * @param SMovement $oMovement
     * 
     * @return array or null 0 => (Movement to Pallet reconfiguration)
     *                       1 => (Supply movement with rows modifications)
     */
    public static function processDivision(SMovement $oMovement = null)
    {
      if ($oMovement->mvt_whs_type_id != \Config::get('scwms.MVT_TP_OUT_SAL')) {
        return null;
      }

      $sSelect = 'sum(ws.input) as inputs,
                    sum(ws.output) as outputs,
                    sum(ws.input - ws.output) as stock,
                    AVG(ws.cost_unit) as cost_unit,
                    ei.code as item_code,
                    ei.name as item,
                    eu.code as unit_code,
                    ei.is_lot,
                    ei.id_item,
                    eu.id_unit,
                    ws.lot_id,
                    wl.lot,
                    ws.pallet_id,
                    ws.location_id
                    ';

      $aParameters = array();
      $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oMovement->year_id;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = $oMovement->branch_id;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $oMovement->whs_id;

      if ($oMovement->id_mvt > 0) {
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] = $oMovement->id_mvt;
      }

      $lMovementRows = array();

      $lPalletsStock = array();

      foreach ($oMovement->aAuxRows as $row) {
        if ($row->pallet_id == 1) {
          continue;
        }

        $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $row->pallet_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $row->unit_id;
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $row->item_id;

        $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = $row->location_id;

        $lPalletStock = session('stock')->getStockResult($aParameters);

        $lPalletStk = $lPalletStock->having('stock', '>', '0')
                                    ->get();
        
        $bAllPallet = false;
        foreach ($lPalletStk as $oStk) {
          if ($oStk->stock == $row->quantity) {
            $bAllPallet = true;
            break;
          }
        }

        if ($bAllPallet) {
          continue;
        }

        $lPalletStock = $lPalletStock->groupBy('ws.lot_id')
                                      ->having('stock', '>', '0')
                                      ->get();

        foreach ($lPalletStock as $oPalletStock) {
          if (! array_key_exists($row->pallet_id, $lPalletsStock)) {
            $lPalletsStock[$row->pallet_id] = $oPalletStock->stock;
          }

          if ($row->item->is_lot) {
            $bCurrentLotFound = false;

            foreach ($row->getAuxLots() as $oAuxLot) {
              if ($oAuxLot->is_deleted) {
                  continue;
              }
              if ($oAuxLot->lot_id == $oPalletStock->lot_id) {
                  if ($oAuxLot->quantity <= $lPalletsStock[$row->pallet_id]) {
                    // creación de movimiento de división de tarima
                    // actualización del stock de la tarima
                    $oDivRow = SMovsCore::createDivisionRow($row->pallet_id, $row->item_id, $row->unit_id, $row->location_id, $oAuxLot->quantity, $oAuxLot->lot_id);
                    array_push($lMovementRows, $oDivRow);

                    $lPalletsStock[$row->pallet_id] -= $oAuxLot->quantity;
                    $row->setWithDivision(true);
                  }

                  $bCurrentLotFound = true;
              }
            }
          }
          else {
            if ($oAuxLot->quantity <= $lPalletsStock[$row->pallet_id]) {
              $oDivRow = SMovsCore::createDivisionRow($row->pallet_id, $row->item_id, $row->unit_id, $row->quantity, 0);
              array_push($lMovementRows, $oDivRow);
  
              $lPalletsStock[$row->pallet_id] -= $row->quantity;
              $row->setWithDivision(true);
            }
          }
        }
      }

      if (sizeof($lMovementRows) > 0) {
        $oDivMov = SMovsCore::createDivisionMov($oMovement->dt_date, $oMovement->branch_id, $oMovement->whs_id, $oMovement->year_id);

        $oDivMov->aAuxRows = $lMovementRows;

        return [$oDivMov, $oMovement];
      }
      else {
        return null;
      }
    }

    private static function createDivisionRow(int $iPallet, int $iItem, int $iUnit, int $iLocation, float $dQuantity, int $iLot)
    {
      $oRow = new SMovementRow();

      $oRow->quantity = $dQuantity;
      $oRow->amount_unit = 0;
      $oRow->amount = 0;
      $oRow->length = 0;
      $oRow->surface = 0;
      $oRow->volume = 0;
      $oRow->mass = 0;
      $oRow->is_deleted = 0;
      $oRow->item_id = $iItem;
      $oRow->unit_id = $iUnit;
      $oRow->pallet_id = $iPallet;
      $oRow->location_id = $iLocation;
      $oRow->doc_order_row_id = 1;
      $oRow->doc_invoice_row_id = 1;
      $oRow->doc_debit_note_row_id = 1;
      $oRow->doc_credit_note_row_id = 1;

      if ($iLot > 0) {
        $oLotRow = new SMovementRowLot();

        $oLotRow->quantity = $dQuantity;
        $oLotRow->amount_unit = 0;
        $oLotRow->amount = 0;
        $oLotRow->length = 0;
        $oLotRow->surface = 0;
        $oLotRow->volume = 0;
        $oLotRow->mass = 0;
        $oLotRow->is_deleted = 0;
        $oLotRow->lot_id = $iLot;

        $oRow->setAuxLots([0 => $oLotRow]);
      }
      
      return $oRow;
    }

    private static function createDivisionMov(string $sDate, int $iBranch, int $iWhs, int $iYear) {
      $oDivMov = new SMovement();

      $oDivMov->dt_date = $sDate;
      $oDivMov->total_amount = 0;
      $oDivMov->total_length = 0;
      $oDivMov->total_surface = 0;
      $oDivMov->total_volume = 0;
      $oDivMov->total_mass = 0;
      $oDivMov->is_closed_shipment = 0;
      $oDivMov->is_deleted = false;
      $oDivMov->is_system = true;
      $oDivMov->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN');
      $oDivMov->mvt_whs_type_id = \Config::get('scwms.PALLET_RECONFIG_IN');
      $oDivMov->mvt_trn_type_id = 1;
      $oDivMov->mvt_adj_type_id = 1;
      $oDivMov->mvt_mfg_type_id = 1;
      $oDivMov->mvt_exp_type_id = 1;
      $oDivMov->branch_id = $iBranch;
      $oDivMov->whs_id = $iWhs;
      $oDivMov->year_id = $iYear;
      $oDivMov->auth_status_id = 1;
      $oDivMov->src_mvt_id = 1;
      $oDivMov->doc_order_id = 1;
      $oDivMov->doc_invoice_id = 1;
      $oDivMov->doc_debit_note_id = 1;
      $oDivMov->doc_credit_note_id = 1;
      $oDivMov->prod_ord_id = 1;
      $oDivMov->mfg_dept_id = 1;
      $oDivMov->mfg_line_id = 1;
      $oDivMov->mfg_job_id = 1;
      $oDivMov->auth_status_by_id = 1;
      $oDivMov->closed_shipment_by_id = 1;
      $oDivMov->created_by_id = \Auth::user()->id;
      $oDivMov->updated_by_id = \Auth::user()->id;

      return $oDivMov;
    }
}
