<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\SUtils\SUtil;
use App\SUtils\SGuiUtils;
use App\SUtils\SMenu;
use App\SUtils\SProcess;
use App\SUtils\SConnectionUtils;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\WMS\SStock;
use App\WMS\SPallet;
use App\WMS\SWmsLot;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\SCore\SStockManagment;
use App\WMS\Data\SData;

class SStockController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    private $MOV = 1;
    private $ROW = 2;
    private $LOT = 3;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.STK_MOVS'), \Config::get('scsys.MODULES.WMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Request
     */
    public function index(Request $request, $iStockType = 1, $sTitle = '')
    {
         $select = 'ws.item_id,
                    ws.unit_id,
                    ws.pallet_id,
                    ws.lot_id,
                    sum(ws.input) as inputs,
                    sum(ws.output) as outputs,
                    (sum(ws.input) - sum(ws.output)) as stock,
                    ei.code as item_code,
                    ei.name as item,
                    eu.code as unit';

         $sFilterDate = $request->filterDate == null ? session('work_date')->format('Y-m-d') : $request->filterDate;
         $iFilterWhs = $request->warehouse == null ? session('whs')->id_whs : $request->warehouse;
         $oFilterDate = Carbon::parse($sFilterDate);
         $iYearId = session('utils')->getYearId($oFilterDate->year);

         $aParameters = array();
         $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 'ei.id_item';
         $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 'eu.id_unit';
         $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = ''.$iYearId;
         $aParameters[\Config::get('scwms.STOCK_PARAMS.DATE')] = $sFilterDate;

        switch ($iStockType) {
          case \Config::get('scwms.STOCK_TYPE.STK_BY_ITEM'):
            $groupBy = 'ws.item_id';
            $orderBy1 = 'ws.item_id';
            $orderBy2 = 'ws.item_id';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET'):
            $select = $select.', '.'wp.id_pallet as pallet, ww.name as warehouse';
            $groupBy = ['ws.pallet_id','ws.item_id','ws.whs_id'];
            $orderBy1 = 'ws.pallet_id';
            $orderBy2 = 'ws.item_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'wp.id_pallet';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ww.id_whs';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_LOT'):
            $select = $select.', wl.lot as lot_, wl.dt_expiry';
            $groupBy = ['ws.lot_id','ws.item_id'];
            $orderBy1 = 'ws.lot_id';
            $orderBy2 = 'ws.item_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION'):
            $select = $select.', wwl.name as location, eb.id_branch as branchid, ei.id_item as itemid, ("0") as maxmin';
            $groupBy = ['ws.location_id','ws.item_id'];
            $orderBy1 = 'ws.location_id';
            $orderBy2 = 'ws.item_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ww.id_whs';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 'eb.id_branch';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE'):
            $select = $select.', '.'ww.name as warehouse'.', '.'ww.id_whs as whsid'.', '.'ei.id_item as itemid'.', '.'(SELECT max FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN wmsu_whs ON wmsu_whs.id_whs = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = whsid) as maxi'.', '.'(SELECT min FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN wmsu_whs ON wmsu_whs.id_whs = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = whsid) as mini'.', '.'(SELECT reorder FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN wmsu_whs ON wmsu_whs.id_whs = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = whsid) as reorder';
            $groupBy = ['ws.whs_id','ws.item_id'];
            $orderBy1 = 'ws.whs_id';
            $orderBy2 = 'ws.item_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ww.id_whs';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 'eb.id_branch';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'):
            $select = $select.', '.'eb.name as branch_'.', '.'eb.id_branch as branchid'.', '.'ei.id_item as itemid'.', '.'(SELECT max FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN erpu_branches ON erpu_branches.id_branch = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = branchid) as maxi'.', '.'(SELECT min FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN erpu_branches ON erpu_branches.id_branch = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = branchid) as mini'.', '.'(SELECT reorder FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN erpu_branches ON erpu_branches.id_branch = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = branchid) as reorder';
            $groupBy = ['ws.branch_id','ws.item_id'];
            $orderBy1 = 'ws.branch_id';
            $orderBy2 = 'ws.item_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 'eb.id_branch';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE'):
            $select = $select.', '.'wl.lot AS lot_, wl.dt_expiry, ww.name as warehouse'.', '.'ww.name as warehouse'.', '.'ww.id_whs as whsid'.', '.'ei.id_item as itemid'.', '.'(SELECT max FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN wmsu_whs ON wmsu_whs.id_whs = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = whsid) as maxi'.', '.'(SELECT min FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN wmsu_whs ON wmsu_whs.id_whs = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = whsid) as mini'.', '.'(SELECT reorder FROM wmsu_container_max_min
            INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
            INNER JOIN wmsu_whs ON wmsu_whs.id_whs = wmsu_container_max_min.container_id WHERE item_id = itemid AND container_id = whsid) as reorder';
            $groupBy = ['ws.item_id','ws.lot_id','ws.whs_id'];
            $orderBy1 = 'ws.item_id';
            $orderBy2 = 'ws.lot_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ww.id_whs';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 'eb.id_branch';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'):
            $select = $select.', '.'wp.id_pallet as pallet, wl.lot AS lot_, wl.dt_expiry';
            $groupBy = ['ws.pallet_id','ws.lot_id','ws.item_id'];
            $orderBy1 = 'ws.pallet_id';
            $orderBy2 = 'ws.lot_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'wp.id_pallet';
            break;
          case \Config::get('scwms.STOCK_TYPE.STK_GENERAL'):
            $select = $select.' , wl.lot AS lot_, wl.dt_expiry ,
                                  wp.id_pallet as pallet,
                                  wwl.name as location,
                                  ww.name as warehouse
                                  ';

            $groupBy = ['ws.whs_id', 'ws.location_id', 'ws.pallet_id', 'ws.lot_id', 'ws.item_id', 'ws.unit_id'];
            $orderBy1 = 'ws.pallet_id';
            $orderBy2 = 'ws.lot_id';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'wp.id_pallet';
            $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ww.id_whs';
            break;

          default:
            # code...
            break;
        }

        $sub = session('stock')->getSubSegregated($aParameters);
        $select = $select.', ('.($sub->toSql()).') as segregated';

        $stock = SStockManagment::getStockBaseQuery($select)
                      // ->select(\DB::raw($select))
                      // ->mergeBindings($sub)
                      ->where('ws.year_id', $iYearId)
                      ->where('ws.dt_date', '<=', $sFilterDate)
                      ->where('eb.id_branch', session('branch')->id_branch)
                      ->whereIn('ws.whs_id', session('utils')->getUserWarehousesArray())
                      ->groupBy($groupBy)
                      ->orderBy($orderBy1)
                      ->orderBy($orderBy2)
                      ->having('stock', '>', '0');

        if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
            $stock = $stock->where('ws.whs_id', $iFilterWhs);
        }

        if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE'))
        {
            $stock = $stock->orderBy('ws.whs_id');
        }
        elseif ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'))
        {
            $stock = $stock->orderBy('ws.item_id');
        }

        $stock = $stock->get();

        $lWhss = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, true);
        $lWhss['0'] = 'TODOS';

        return view('wms.stock.stock')
                          ->with('iStockType', $iStockType)
                          ->with('sTitle', $sTitle)
                          ->with('tfilterDate', Carbon::parse($sFilterDate))
                          ->with('lWarehouses', $lWhss)
                          ->with('iFilterWhs', $iFilterWhs)
                          ->with('data', $stock);
    }

    public function getMovements(Request $request)
    {
       $iType = $request->iType;
       $iId = $request->iId;
       $iWhsOption = $request->iWhsOption;

       $oData = new SData();

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

       if ($iWhsOption != \Config::get('scwms.FILTER_ALL_WHS')) {
           $query = $query->where('wm.whs_id', $iWhsOption);
       }

       switch ($iType) {
         case \Config::get('scwms.ELEMENTS_TYPE.ITEMS'):
           $sSelect = $sSelect.', IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmr.quantity, 0) AS inputs,
                                IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmr.quantity, 0) AS outputs,
                                IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmr.amount, 0) AS credit,
                                IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmr.amount, 0) AS debit';
           $aIds = explode("-", $iId);
           $query->where('wmr.item_id', $aIds[0])
                 ->where('wmr.unit_id', $aIds[1]);
           $oData->oItem = SItem::find($aIds[0]);
           $oData->oUnit = SUnit::find($aIds[1]);
           $oData->oElement = $oData->oItem;

           break;

         case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
           $query = $query->join('wms_mvt_row_lots as wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                          ->join('wms_lots as wl', 'wmrl.lot_id', '=', 'wl.id_lot');

           $sSelect = $sSelect.', wl.lot, wl.dt_expiry';
           $sSelect = $sSelect.', IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmrl.quantity, 0) AS inputs,
                              IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmrl.quantity, 0) AS outputs,
                              IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmrl.amount, 0) AS credit,
                              IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmrl.amount, 0) AS debit';

           $query->where('wmrl.lot_id', $iId);
           $oData->oItem = SWmsLot::find($iId)->item;
           $oData->oUnit = SWmsLot::find($iId)->unit;
           $oData->oElement = SWmsLot::find($iId);

           break;

         case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
           $sSelect = $sSelect.', IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmr.quantity, 0) AS inputs,
                              IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmr.quantity, 0) AS outputs,
                              IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmr.amount, 0) AS credit,
                              IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmr.amount, 0) AS debit';
           $query->where('wmr.pallet_id', $iId);
           $oData->oItem = SPallet::find($iId)->item;
           $oData->oUnit = SPallet::find($iId)->unit;
           $oData->oElement = SPallet::find($iId);

           break;

         default:
           return json_encode($oData);
       }

       $query = $query->select(\DB::raw($sSelect));

       $query = $query->orderBy('dt_date', 'ASC')
                       ->orderBy('id_mvt', 'ASC')
                       ->get();

       $iIndex = 1;
       $dInputs = 0;
       $dOutputs = 0;
       $dStock = 0;
       $dBalance = 0;
       foreach ($query as $row) {
          $dInputs += $row->inputs;
          $dOutputs += $row->outputs;
          $dStock += $row->inputs - $row->outputs;
          $row->stock = $dStock;

          $dBalance += $row->debit - $row->credit;
          $row->balance = $dBalance;

          $row->index = $iIndex++;
       }

       $oData->lMovements = $query;
       $oData->dInputs = $dInputs;
       $oData->dOutputs = $dOutputs;
       $oData->dStock = $dStock;

       return json_encode($oData);
    }


    /**
     * Store stock moves in table.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $oMovement)
    {
        if ($oMovement->is_deleted) {
            $this->eraseStock($this->MOV, $oMovement->id_mvt);
            return $request;
        }

        foreach ($oMovement->rows as $movRow) {
          if (sizeof($movRow->lotRows) > 0) {
            foreach ($movRow->lotRows as $lotRow) {
              if ($lotRow->is_deleted) {
                  $this->eraseStock($this->LOT, $lotRow->id_mvt_row_lot);
              }
              else {
                $lStock = SStock::where('mvt_row_lot_id', $lotRow->id_mvt_row_lot)
                            ->where('is_deleted', true)
                            ->get();

                if (sizeof($lStock) > 0) {
                    SStock::where('mvt_row_lot_id', $lotRow->id_mvt_row_lot)
                              ->where('is_deleted', true)
                              ->update(['is_deleted' => false]);
                }
                else {
                  $oStock = new SStock();

                  // $oStock->id_stock = 0;
                  $oStock->dt_date = $oMovement->dt_date;
                  $oStock->cost_unit = $lotRow->amount_unit;

                  if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN'))
                  {
                    $oStock->input = $lotRow->quantity;
                    $oStock->output = 0;
                    $oStock->debit = 0;
                    $oStock->credit = $oStock->cost_unit * $lotRow->quantity;
                  }
                  else
                  {
                    $oStock->input = 0;
                    $oStock->output = $lotRow->quantity;
                    $oStock->debit = $oStock->cost_unit * $lotRow->quantity;
                    $oStock->credit = 0;
                  }

                  $oStock->is_deleted = $oMovement->is_deleted;
                  $oStock->mvt_whs_class_id = $oMovement->mvt_whs_class_id;
                  $oStock->mvt_whs_type_id = $oMovement->mvt_whs_type_id;
                  $oStock->mvt_trn_type_id = $oMovement->mvt_trn_type_id;
                  $oStock->mvt_adj_type_id = $oMovement->mvt_adj_type_id;
                  $oStock->mvt_mfg_type_id = $oMovement->mvt_mfg_type_id;
                  $oStock->mvt_exp_type_id = $oMovement->mvt_exp_type_id;
                  $oStock->branch_id = $oMovement->branch_id;
                  $oStock->whs_id = $oMovement->whs_id;
                  $oStock->location_id = $movRow->location_id;
                  $oStock->mvt_id = $oMovement->id_mvt;
                  $oStock->mvt_row_id = $movRow->id_mvt_row;
                  $oStock->mvt_row_lot_id = $lotRow->id_mvt_row_lot;
                  $oStock->item_id = $movRow->item_id;
                  $oStock->unit_id = $movRow->unit_id;
                  $oStock->lot_id = $lotRow->lot_id;
                  $oStock->pallet_id = $movRow->pallet_id;
                  $oStock->doc_order_row_id = $movRow->doc_order_row_id;
                  $oStock->doc_invoice_row_id = $movRow->doc_invoice_row_id;
                  $oStock->doc_debit_note_row_id = $movRow->doc_debit_note_row_id;
                  $oStock->doc_credit_note_row_id = $movRow->doc_credit_note_row_id;
                  $oStock->mfg_dept_id = $oMovement->mfg_dept_id;
                  $oStock->mfg_line_id = $oMovement->mfg_line_id;
                  $oStock->mfg_job_id = $oMovement->mfg_job_id;
                  $oStock->year_id = $oMovement->year_id;

                  $oStock->save();
                }

              }
            }
          }
          else {
            if ($movRow->is_deleted) {
                $this->eraseStock($this->ROW, $movRow->id_mvt_row);
            }
            else {
              $lStock = SStock::where('mvt_row_id', $movRow->id_mvt_row)
                          ->where('is_deleted', true)
                          ->get();

              if (sizeof($lStock) > 0) {
                  SStock::where('mvt_row_id', $movRow->id_mvt_row)
                            ->where('is_deleted', true)
                            ->update(['is_deleted' => false]);
              }
              else {
                $oStock = new SStock();

                // $oStock->id_stock = 0;
                $oStock->dt_date = $oMovement->dt_date;
                $oStock->cost_unit = $movRow->amount_unit;

                if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN'))
                {
                  $oStock->input = $movRow->quantity;
                  $oStock->output = 0;
                  $oStock->debit = 0;
                  $oStock->credit = $oStock->cost_unit * $movRow->quantity;
                }
                else
                {
                  $oStock->input = 0;
                  $oStock->output = $movRow->quantity;
                  $oStock->debit = $oStock->cost_unit * $movRow->quantity;
                  $oStock->credit = 0;
                }

                $oStock->is_deleted = $oMovement->is_deleted;
                $oStock->mvt_whs_class_id = $oMovement->mvt_whs_class_id;
                $oStock->mvt_whs_type_id = $oMovement->mvt_whs_type_id;
                $oStock->mvt_trn_type_id = $oMovement->mvt_trn_type_id;
                $oStock->mvt_adj_type_id = $oMovement->mvt_adj_type_id;
                $oStock->mvt_mfg_type_id = $oMovement->mvt_mfg_type_id;
                $oStock->mvt_exp_type_id = $oMovement->mvt_exp_type_id;
                $oStock->branch_id = $oMovement->branch_id;
                $oStock->whs_id = $oMovement->whs_id;
                $oStock->location_id = $movRow->location_id;
                $oStock->mvt_id = $oMovement->id_mvt;
                $oStock->mvt_row_id = $movRow->id_mvt_row;
                $oStock->mvt_row_lot_id = 1;
                $oStock->item_id = $movRow->item_id;
                $oStock->unit_id = $movRow->unit_id;
                $oStock->lot_id = 1;
                $oStock->pallet_id = $movRow->pallet_id;
                $oStock->doc_order_row_id = $movRow->doc_order_row_id;
                $oStock->doc_invoice_row_id = $movRow->doc_invoice_row_id;
                $oStock->doc_debit_note_row_id = $movRow->doc_debit_note_row_id;
                $oStock->doc_credit_note_row_id = $movRow->doc_credit_note_row_id;
                $oStock->mfg_dept_id = $oMovement->mfg_dept_id;
                $oStock->mfg_line_id = $oMovement->mfg_line_id;
                $oStock->mfg_job_id = $oMovement->mfg_job_id;
                $oStock->year_id = session('work_year');

                $oStock->save();
              }
            }
          }
        }

        return $request;
    }

    public function eraseStock($iType, $iId)
    {
       $sField = '';
       switch ($iType) {
         case $this->MOV:
           $sField = 'mvt_id';
           break;
         case $this->ROW:
           $sField = 'mvt_row_id';
           break;
         case $this->LOT:
           $sField = 'mvt_row_lot_id';
           break;

         default:
           # code...
           break;
       }

       $lStockRows = SStock::where($sField, $iId)
                              ->where('is_deleted', false)
                              ->get();

       foreach ($lStockRows as $rStock) {
          $rStock->is_deleted = true;
          $rStock->save();
       }
    }


}
