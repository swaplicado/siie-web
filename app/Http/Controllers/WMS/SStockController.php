<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_LOT'):
          $select = $select.', '.'wl.lot as lot_';
          $groupBy = ['ws.lot_id','ws.item_id'];
          $orderBy1 = 'ws.lot_id';
          $orderBy2 = 'ws.item_id';
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION'):
          $select = $select.', '.'wwl.name as location';
          $groupBy = ['ws.location_id','ws.item_id'];
          $orderBy1 = 'ws.location_id';
          $orderBy2 = 'ws.item_id';
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE'):
          $select = $select.', '.'ww.name as warehouse';
          $groupBy = ['ws.whs_id','ws.item_id'];
          $orderBy1 = 'ws.whs_id';
          $orderBy2 = 'ws.item_id';
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE'):
          $select = $select.', '.'wl.lot AS lot_, ww.name as warehouse';
          $groupBy = ['ws.item_id','ws.lot_id','ws.whs_id'];
          $orderBy1 = 'ws.item_id';
          $orderBy2 = 'ws.lot_id';
          break;
        case \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'):
          $select = $select.', '.'wp.pallet as pallet, wl.lot AS lot_';
          $groupBy = ['ws.pallet_id','ws.lot_id','ws.item_id'];
          $orderBy1 = 'ws.pallet_id';
          $orderBy2 = 'ws.lot_id';
          break;

        default:
          # code...
          break;
      }

      $stock = SStockManagment::getStockBaseQuery($select)
                    ->select(\DB::raw($select))
                    ->where('ws.is_deleted', false)
                    ->groupBy($groupBy)
                    ->orderBy($orderBy1)
                    ->orderBy($orderBy2)
                    ->having('stock', '>', '0');

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
                        ->with('data', $stock);
    }


    /**
     * Store stock moves in table.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $oMovement)
    {
        \Debugbar::error($oMovement);
        foreach ($oMovement->rows as $movRow) {
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

            \Debugbar::info($oStock);
            $oStock->save();
            \Debugbar::info("guardado");
          }
        }

        return $request;
    }
}
