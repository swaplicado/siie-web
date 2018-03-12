<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;
use App\SUtils\SConnectionUtils;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\WMS\SStock;
use App\SCore\SStockManagment;

class SStockController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

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
    public function index(Request $request, $iStockType = 1)
    {
      $select = 'sum(ws.input) as inputs,
                         sum(ws.output) as outputs,
                         (sum(ws.input) - sum(ws.output)) as stock,
                         ei.code as item_code,
                         ei.name as item,
                         eu.code as unit';

     $sFilterDate = $request->filterDate == null ? session('work_date')->format('Y-m-d') : $request->filterDate;
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
          $select = $select.', '.'wp.pallet as pallet, ww.name as warehouse';
          $groupBy = ['ws.pallet_id','ws.item_id','ws.whs_id'];
          $orderBy1 = 'ws.pallet_id';
          $orderBy2 = 'ws.item_id';
          $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'wp.id_pallet';
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_LOT'):
          $select = $select.', '.'wl.lot as lot_';
          $groupBy = ['ws.lot_id','ws.item_id'];
          $orderBy1 = 'ws.lot_id';
          $orderBy2 = 'ws.item_id';
          $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION'):
          $select = $select.', '.'wwl.name as location'.', '.'eb.id_branch as branchid'.', '.'ei.id_item as itemid'.', '.', '.'(SELECT * FROM wmsu_container_max_min
          INNER JOIN wmss_container_types ON wmss_container_types.id_container_type = wmsu_container_max_min.container_type_id
          INNER JOIN wmsu_whs_locations ON wmsu_whs_locations.id_whs_location = wmsu_container_max_min.container_id) as maxmin';
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
          $select = $select.', '.'wl.lot AS lot_, ww.name as warehouse'.', '.'ww.name as warehouse'.', '.'ww.id_whs as whsid'.', '.'ei.id_item as itemid'.', '.'(SELECT max FROM wmsu_container_max_min
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
          $select = $select.', '.'wp.pallet as pallet, wl.lot AS lot_';
          $groupBy = ['ws.pallet_id','ws.lot_id','ws.item_id'];
          $orderBy1 = 'ws.pallet_id';
          $orderBy2 = 'ws.lot_id';
          $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
          $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'wp.id_pallet';
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
                    ->where('ws.is_deleted', false)
                    ->where('ws.dt_date', '<=', $sFilterDate)
                    ->groupBy($groupBy)
                    ->orderBy($orderBy1)
                    ->orderBy($orderBy2)
                    ->having('stock', '>', '0');

      if (\Auth::user()->user_type_id != \Config::get('scsys.TP_USER.MANAGER')) {
          $stock = $stock->where('ww.id_whs', session()->has('whs') ? session('whs')->id_whs : 1);
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

      return view('wms.stock.stock')
                        ->with('iStockType', $iStockType)
                        ->with('tfilterDate', Carbon::parse($sFilterDate))
                        ->with('data', $stock);
    }


    /**
     * Store stock moves in table.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $oMovement)
    {
        foreach ($oMovement->rows as $movRow) {
          if (sizeof($movRow->lotRows) > 0)
          {
            foreach ($movRow->lotRows as $lotRow) {
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
              $oStock->year_id = session('work_year');

              $oStock->save();
            }
        }
        else
        {
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

        return $request;
    }
}
