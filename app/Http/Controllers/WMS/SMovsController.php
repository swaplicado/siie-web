<?php
namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WMS\SStockController;
use App\SBarcode\SBarcode;
use App\Http\Requests\WMS\SMovRequest;
use App\SCore\SStockUtils;
use App\SCore\SMovsManagment;

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
        $lMovRows = SMovementRow::Search($request->date, $this->iFilter)->orderBy('item_id', 'ASC')->paginate(20);

        foreach ($lMovRows as $row) {
            $row->movement->branch;
            $row->movement->whs;
            $row->movement->mvtType;
            $row->movement->trnType;
            $row->movement->adjType;
            $row->movement->mfgType;
            $row->movement->expType;
            $row->lotRows;
            $row->item->unit;
        }

        //  \Debugbar::info($date);
        // dd($lMovRows);
        return view('wms.movs.index')
                    ->with('rows', $lMovRows);
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

          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_IN_TRA'):
            $mvtComp = SMvtAdjType::where('id_mvt_adj_type', 1)->lists('name', 'id_mvt_adj_type');
            break;

          case \Config::get('scwms.MVT_TP_IN_CON'):
          case \Config::get('scwms.MVT_TP_IN_PRO'):
          case \Config::get('scwms.MVT_TP_OUT_CON'):
          case \Config::get('scwms.MVT_TP_OUT_PRO'):
            $mvtComp = SMvtMfgType::where('is_deleted', false)->lists('name', 'id_mvt_mfg_type');
            break;

          case \Config::get('scwms.MVT_TP_IN_EXP'):
          case \Config::get('scwms.MVT_TP_OUT_EXP'):
            $mvtComp = SMvtExpType::where('is_deleted', false)->lists('name', 'id_mvt_exp_type');
            break;

          case \Config::get('scwms.PALLET_RECONFIG_IN'):
          case \Config::get('scwms.PALLET_RECONFIG_OUT'):
            $mvtComp = SMvtExpType::where('id_mvt_exp_type', 1)->lists('name', 'id_mvt_exp_type');
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
                          ->with('pallets', $pallets);
    }

    /**
     * [getTable set the value of data from client to session('data')]
     *
     * @param  Request $request [$request->value]
     */
    public function getTable(Request $request)
    {
      \Debugbar::info($request->value);
      session(['data' => $request->value]);
    }

    /**
     * [store saves the whs movs]
     * @param  Request $request [description]
     * @return [redirect to whs.home]
     */
    public function store(SMovRequest $request)
    {
        \Debugbar::info('Store');

        $whsId = 0;
        $whsSrc = 0;
        $whsDes = 0;
        if (session('data')['iMvtType'] == \Config::get('scwms.MVT_TP_OUT_TRA')) {
            $whsSrc = session('data')['iWhsSrc'];
            $whsDes = session('data')['iWhsDes'];
            $whsId = session('data')['iWhsSrc'];

            $aErrors = SStockUtils::validateStock(session('data'), $whsSrc);
            if(sizeof($aErrors) > 0)
            {
                return redirect()->back()->withErrors($aErrors)->withInput();
                // return view('wms.index');
            }
        }
        else if ($request->input('mvt_whs_class_id') == \Config::get('scwms.MVT_CLS_OUT'))
        {
            $whsId = session('data')['iWhsSrc'];

            $aErrors = SStockUtils::validateStock(session('data'), $whsId);
            if(sizeof($aErrors) > 0)
            {
                return redirect()->back()->withErrors($aErrors)->withInput();
                // return view('wms.index');
            }
        }
        else
        {
            // $whsId = $request->input('whs_des');
            $whsId = session('data')['iWhsDes'];
        }

        $movement = new SMovement($request->all());

        $movement->mvt_whs_type_id = session('data')['iMvtType'];
        $movement->mvt_trn_type_id = 1;
        $movement->mvt_adj_type_id = 1;
        $movement->mvt_mfg_type_id = 1;
        $movement->mvt_exp_type_id = 1;

        switch (session('data')['iMvtType']) {
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

        $movement->whs_id = $whsId;
        $movement->branch_id = $movement->whs->branch_id;
        $movement->auth_status_id = 1; // ??? pendientes constantes de status
        $movement->src_mvt_id = 1;
        $movement->auth_status_by_id = 1;
        $movement->closed_shipment_by_id = 1;
        $movement->created_by_id = \Auth::user()->id;
        $movement->updated_by_id = \Auth::user()->id;

        $movementRows = array();
        foreach (session('data')['rows'] as $row) {
           $oMvtRow = new SMovementRow();
           $oMvtRow->quantity = $row['dQuantity'];
           $oMvtRow->amount_unit = $row['dPrice'];
           $oMvtRow->item_id = $row['iItemId'];
           $oMvtRow->unit_id = $row['iUnitId'];
           $oMvtRow->pallet_id = $row['iPalletId'];
           $oMvtRow->location_id = $row['iLocationId'];
           $oMvtRow->doc_order_row_id =1;
           $oMvtRow->doc_invoice_row_id = 1;
           $oMvtRow->doc_debit_note_row_id = 1;
           $oMvtRow->doc_credit_note_row_id = 1;

           $movLotRows = array();
           foreach ($row['lotRows'] as $lotRow) {
               $oMovLotRow = new SMovementRowLot();
               $oMovLotRow->quantity = $lotRow['dQuantity'];
               $oMovLotRow->amount_unit = $lotRow['dPrice'];
               $oMovLotRow->lot_id = $lotRow['iLotId'];

               array_push($movLotRows, $oMovLotRow);
           }

           $oMvtRow->setAuxLots($movLotRows);
           array_push($movementRows, $oMvtRow);
        }

        $movements = SMovsManagment::processMovement($movement,
                                                    $movementRows,
                                                    $request->input('mvt_whs_class_id'),
                                                    session('data')['iMvtType'],
                                                    $whsSrc,
                                                    $whsDes);

        $this->saveMovement($movements, $request);


        // return redirect()->route('wms.home');
        return view('wms.index');
    }

    /**
     * [Saves the movement in DB and creates and saves stock rows]
     * @param  [SMovement] $movement
     * @param  [Array of SMovementRow] $movementRows
     * @param  [SMovRequest] $request
     */
    private function saveMovement($movements, $request)
    {
        \DB::connection('company')->transaction(function() use ($movements, $request) {
          try
          {
            foreach ($movements as $mov)
            {
                $movement = clone $mov;
                $movement->save();

                foreach ($mov->aAuxRows as $movRow)
                {
                  $row = clone $movRow;
                  $row->mvt_id = $movement->id_mvt;
                  $row->save();

                  foreach ($movRow->getAuxLots() as $lotRow)
                  {
                     $lRow = clone $lotRow;
                     $lRow->mvt_row_id = $row->id_mvt_row;
                     $lRow->save();
                  }
                  // $row->lotRows()->saveMany($movRow->getAuxLots());
                }

                $movement = SMovement::find($movement->id_mvt);
                foreach ($movement->rows as $row)
                {
                  $row->lotRows;
                }

                $stkController = new SStockController();
                $stkController->store($request, $movement);
            }
          }
          catch (\Exception $e) {
              \Debugbar::warning('Not saved!');
              \Debugbar::error($e);
          }
       });
    }

    /**
     * [children gets the data of code received from view]
     *
     * @param  Request $request [parent, whs, idCls]
     * @return [array]    [array of SMovementRow]
     */
    public function children(Request $request)
    {
      $rows = array();

      try
      {
          $dStock = 0.0;
          $obj = SBarcode::decodeBarcode($request->parent);
          // If the barcode is Item
          if ($obj == NULL)
          {
            $items = SItem::where('is_deleted', false)->where('code', $request->parent)->get();

            foreach ($items as $item) {
                $row = $this->createMovement($item->id_item, $item->unit_id, 0);

                $aParameters = array();
                $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $item->id_item;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $item->unit_id;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 0;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 0;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $request->whs;
                $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

                $row->aux_stock = session('stock')->getStock($aParameters);

                array_push($rows, $row);
            }
          }

          // If the barcode is Pallet
          elseif ($obj instanceof SPallet)
          {
              $row = $this->createMovement($obj->item_id, $obj->unit_id, $obj->quantity);

              $aParameters = array();
              $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $obj->item_id;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $obj->unit_id;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 0;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $obj->id_pallet;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $request->whs;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

              $row->aux_stock = session('stock')->getStock($aParameters);

              $result = session('stock')->getLotsOfPallet($obj->id_pallet, $request->whs);
              $row->aux_lots = $this->createLotRows($result);

              $qty = 0;
              $amnt = 0;
              foreach ($row->aux_lots as $lot) {
                $qty += $lot->quantity;
                $amnt += $lot->amount;
              }

              $row->quantity = $qty;
              $row->amount = $amnt;
              $row->amount_unit = $amnt/$qty;
              $row->pallet_id = $obj->id_pallet;

              array_push($rows, $row);
          }

          //If the barcode is lot
          elseif ($obj instanceof SWmsLot)
          {
              $row = $this->createMovement($obj->item_id, $obj->unit_id, $obj->quantity);
              $row->aux_lot_id = $obj->id_lot;

              $aParameters = array();
              $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $obj->item_id;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $obj->unit_id;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = $obj->id_lot;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 0;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 0;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $request->whs;
              $aParameters[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 0;

              $row->aux_stock = session('stock')->getStock($aParameters);

              array_push($rows, $row);
          }
      }
      catch (Exception $e)
      {
        \Debugbar::error($e);
      }
      finally
      {
        return $rows;
      }
    }

    /**
     * [createMovement description]
     * @param  string $iItemId   [description]
     * @param  string $iUnitId   [description]
     * @param  string $dQuantity [description]
     * @return [type]            [description]
     */
    public function createMovement($iItemId = '0', $iUnitId = '0', $dQuantity = '0')
    {
        $movRow = new SMovementRow();
        $movRow->item_id = $iItemId;
        $movRow->unit_id = $iUnitId;
        $movRow->item;
        $movRow->unit;
        $movRow->quantity = $dQuantity;
        $movRow->pallet_id = 1;
        $movRow->location_id = 1;

        return $movRow;
    }

    /**
     * [createLotRows description]
     * @param  array  $result [description]
     * @return [type]         [description]
     */
    public function createLotRows($result = [])
    {
       $lotRows = array();
       foreach ($result as $row) {
          $mrl = new SMovementRowLot();
          $mrl->lot_id = $row->lot_id;
          $mrl->quantity = $row->stock;
          $mrl->amount_unit = $row->cost_unit;
          $mrl->amount = $mrl->quantity * $mrl->amount_unit;

          array_push($lotRows, $mrl);
       }

       return $lotRows;
    }
}
