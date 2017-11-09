<?php
namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WMS\SStockController;

use App\Http\Requests\WMS\SWhsRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\WMS\SPallet;
use App\WMS\SMvtClass;
use App\WMS\SMvtType;
use App\WMS\SMvtTrnType;
use App\WMS\SMvtAdjType;
use App\WMS\SMvtMfgType;
use App\WMS\SMvtExpType;
use App\WMS\SWhsType;
use App\WMS\SWmsLot;
use App\ERP\SBranch;
use App\ERP\SItem;
use App\SUtils\SProcess;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;

class SMovsController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $mvtType = 0)
    {
        $movement = new SMovement();

        // $movClasses = SMvtClass::where('is_deleted', false)->lists('name', 'id_mvt_class');
        $oMovType = SMvtType::find($mvtType);
        $movTypes = SMvtType::where('is_deleted', false)->where('id_mvt_type', $mvtType)->lists('name', 'id_mvt_type');
        $warehouses = SWarehouse::where('is_deleted', false)->lists('name', 'id_whs');
        $locations = SLocation::where('is_deleted', false)->get();
        $lots = SWmsLot::where('is_deleted', false)->get();
        $pallets = SPallet::where('is_deleted', false)->get();

        $movement->rows;
        $mvtComp = NULL;

        switch ($mvtType) {
          case \Config::get('scwms.MVT_TP_IN_SAL'):
          case \Config::get('scwms.MVT_TP_IN_PUR'):
          case \Config::get('scwms.MVT_TP_OUT_SAL'):
          case \Config::get('scwms.MVT_TP_OUT_PUR'):
            $mvtComp = SMvtTrnType::where('is_deleted', false)->lists('name', 'id_mvt_trn_type');
            break;

          case \Config::get('scwms.MVT_TP_IN_ADJ'):
          case \Config::get('scwms.MVT_TP_OUT_ADJ'):
            $mvtComp = SMvtAdjType::where('is_deleted', false)->lists('name', 'id_mvt_adj_type');
            break;

          case \Config::get('scwms.MVT_TP_IN_TRA'):
          case \Config::get('scwms.MVT_TP_IN_CON'):
          case \Config::get('scwms.MVT_TP_IN_PRO'):
          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_OUT_CON'):
          case \Config::get('scwms.MVT_TP_OUT_PRO'):
            $mvtComp = SMvtMfgType::where('is_deleted', false)->lists('name', 'id_mvt_adj_type');
            break;

          case \Config::get('scwms.MVT_TP_IN_EXP'):
          case \Config::get('scwms.MVT_TP_OUT_EXP'):
            $mvtComp = SMvtExpType::where('is_deleted', false)->lists('name', 'id_mvt_exp_type');
            break;

          default:
            # code...
            break;
        }

        return view('wms.movs.whsmovs')
                          ->with('oMovType', $oMovType)
                          ->with('movTypes', $movTypes)
                          ->with('mvtComp', $mvtComp)
                          ->with('warehouses', $warehouses)
                          ->with('locations', $locations)
                          ->with('lots', $lots)
                          ->with('pallets', $pallets)
                          ->with('movement', $movement);
    }


    public function getTable(Request $request)
    {
      \Debugbar::info($request->value);
      session(['data' => $request->value]);
    }

    public function store(Request $request)
    {
        \Debugbar::info('Store');

        $movement = new SMovement($request->all());

        $movement->mvt_trn_type_id = 1;
        $movement->mvt_adj_type_id = 1;
        $movement->mvt_mfg_type_id = 1;
        $movement->mvt_exp_type_id = 1;

        switch ($movement->mvt_whs_type_id) {
          case \Config::get('scwms.MVT_TP_IN_SAL'):
          case \Config::get('scwms.MVT_TP_IN_PUR'):
          case \Config::get('scwms.MVT_TP_OUT_SAL'):
          case \Config::get('scwms.MVT_TP_OUT_PUR'):
            $movement->mvt_trn_type_id = $request->input('mvt_com');
            break;

          case \Config::get('scwms.MVT_TP_IN_ADJ'):
          case \Config::get('scwms.MVT_TP_OUT_ADJ'):
            $movement->mvt_adj_type_id = $request->input('mvt_com');
            break;

          case \Config::get('scwms.MVT_TP_IN_TRA'):
          case \Config::get('scwms.MVT_TP_IN_CON'):
          case \Config::get('scwms.MVT_TP_IN_PRO'):
          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_OUT_CON'):
          case \Config::get('scwms.MVT_TP_OUT_PRO'):
            $movement->mvt_mfg_type_id = $request->input('mvt_com');
            break;

          case \Config::get('scwms.MVT_TP_IN_EXP'):
          case \Config::get('scwms.MVT_TP_OUT_EXP'):
            $movement->mvt_exp_type_id = $request->input('mvt_com');
            break;

          default:
            # code...
            break;
        }

        $whsId = 0;
        if ($movement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT'))
        {
          $whsId = $request->input('whs_src');
        }
        else
        {
          $whsId = $request->input('whs_des');
        }

        $movement->whs_id = $whsId;
        $movement->branch_id = $movement->whs->branch_id;
        $movement->auth_status_id = 1; // ??? pendientes constantes de status
        $movement->src_mvt_id = 1;
        $movement->auth_status_by_id = 1;
        $movement->closed_shipment_by_id = 1;
        $movement->created_by_id = \Auth::user()->id;
        $movement->updated_by_id = \Auth::user()->id;

        $movementRows = array();
        foreach (session('data')['movementTable'] as $row) {
           $oMvtRow = new SMovementRow();
           $oMvtRow->quantity = $row['qty'];
           $oMvtRow->amount_unit = $row['price'];
          //  $oMvtRow->mvt_id = 1;
           $oMvtRow->item_id = $row['id_item'];
           $oMvtRow->unit_id = $row['id_unit'];
           $oMvtRow->pallet_id = 1;
           $oMvtRow->location_id = 1;
           $oMvtRow->doc_order_row_id =1;
           $oMvtRow->doc_invoice_row_id = 1;
           $oMvtRow->doc_debit_note_row_id = 1;
           $oMvtRow->doc_credit_note_row_id = 1;

           $movLotRows = array();
           foreach (session('data')['lotTables'] as $table) {
             foreach ($table as $lotRow) {
               if ($lotRow['id_parent'] == $row['id_tr']) {
                 $oMovLotRow = new SMovementRowLot();
                 $oMovLotRow->quantity = $lotRow['qty'];
                 $oMovLotRow->amount_unit = $lotRow['price'];
                 $oMovLotRow->lot_id = $lotRow['lot_value'];

                 array_push($movLotRows, $oMovLotRow);
               }
             }
           }

           $oMvtRow->setAuxLots($movLotRows);
           array_push($movementRows, $oMvtRow);
        }

        // \DB::beginTransaction();
        // try {
        //     $movement->save();
        //     $movement->rows()->saveMany($movementRows);
        //
        //     $success = true;
        // } catch (\Exception $e) {
        //     \Debugbar::warning('No se guardó');
        //     $success = false;
        //     \DB::rollback();
        // }
        //
        // \DB::commit();
        //
        // if ($success) {
        //     // the transaction worked ...
        //     \Debugbar::warning('Se guardó');
        // }

        \DB::connection('company')->transaction(function() use ($movement, $movementRows, $request) {
          try
          {

            $movement->save();
            foreach ($movementRows as $movRow)
            {
              $row = $movement->rows()->save($movRow);
              $row->lotRows()->saveMany($movRow->getAuxLots());
            }

            $movement = SMovement::find($movement->id_mvt);
            foreach ($movement->rows as $row) {
              $row->lotRows;
            }
            \Debugbar::info($movement);

            $stkController = new SStockController();
            $stkController->store($request, $movement);
          }
          catch (\Exception $e) {
            \Debugbar::warning('Not saved!');
            \Debugbar::info($e);
          }
       });

        // return redirect()->route('wms.home');
        return view('wms.index');
    }

    public function children(Request $request)
    {
        $item = SItem::find($request->parent);
        $item->unit->code;

        $arra = array();
        $arra[0] = $item;

        return $arra;
    }
}
