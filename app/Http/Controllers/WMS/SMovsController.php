<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WMS\SStockController;
use App\Http\Controllers\QMS\SSegregationsController;
use App\SBarcode\SBarcode;
use App\Http\Requests\WMS\SMovRequest;
use App\SCore\SStockUtils;
use App\SCore\SMovsManagment;
use App\SCore\SMovsUtils;
use App\SCore\SLotsValidations;
use App\ERP\SErpConfiguration;

use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;
use App\SUtils\SValidation;
use App\SUtils\SGuiUtils;
use App\ERP\SBranch;
use App\ERP\SItem;
use App\ERP\SDocument;
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
use App\WMS\SItemContainer;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\WMS\Data\SData;

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
     * @param  Request $request
     * @param  integer $iFolio receive a folio if a movement was saved
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $iFolio = 0)
    {
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
        $lMovRows = SMovementRow::Search($request->date, $this->iFilter, $sFilterDate)->orderBy('item_id', 'ASC')->orderBy('item_id', 'ASC')->paginate(20);

        foreach ($lMovRows as $row) {
            $row->movement->branch;
            $row->movement->warehouse;
            $row->movement->mvtType;
            $row->movement->trnType;
            $row->movement->adjType;
            $row->movement->mfgType;
            $row->movement->expType;
            $row->lotRows;
            $row->item->unit;
        }

        return view('wms.movs.index')
                    ->with('iFolio', $iFolio)
                    ->with('iFilter', $this->iFilter)
                    ->with('sFilterDate', $sFilterDate)
                    ->with('rows', $lMovRows);
    }

    /**
     * index for the view of inventory documents
     *
     * @param  Request $request
     *
     * @return view  'wms.movs.inventorydocs'
     */
    public function inventoryDocs(Request $request)
    {
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
        $lMovs = SMovement::Search($this->iFilter, $sFilterDate)->orderBy('dt_date', 'ASC')->orderBy('folio', 'ASC')->get();

        foreach ($lMovs as $mov) {
            $mov->order;
            $mov->invoice;
            $mov->branch;
            $mov->warehouse;
            $mov->mvtType;
            $mov->trnType;
            $mov->adjType;
            $mov->mfgType;
            $mov->expType;
        }

        return view('wms.movs.inventorydocs')
                    ->with('iFilter', $this->iFilter)
                    ->with('sFilterDate', $sFilterDate)
                    ->with('actualUserPermission', $this->oCurrentUserPermission)
                    ->with('lMovs', $lMovs);
    }

    /**
     * prepare all data to create a new warehouse movement
     *
     * @param  Request $request [description]
     * @param  integer  $mvtType type of movement
     * @param  integer $iDocId if is a supply the method receive the id to show
     *                         this document
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $mvtType, $iDocId = 0)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id)) {
          return redirect()->route('notauthorized');
        }

        $iOperation = \Config::get('scwms.OPERATION_TYPE.CREATION');

        $oMovement = new SMovement();
        $lDocData = array();
        $lStock = null;

        // get the document if the id != 0
        if ($iDocId != 0)
        {
            $oDocument = SDocument::find($iDocId);
            $oDocument->rows;

            $FilterDel = \Config::get('scsys.FILTER.ACTIVES');
            $sFilterDate = null;
            $iViewType = \Config::get('scwms.DOC_VIEW.DETAIL');
            $bWithPending = true;

            $lDocData = session('stock')::getSuppliedRes($oDocument->doc_category_id,
                                            $oDocument->doc_class_id,
                                            $oDocument->doc_type_id, $FilterDel,
                                            $sFilterDate, $iViewType, $iDocId,
                                            $bWithPending)->get();
        }
        else
        {
            $oDocument = 0;
        }

        $oMovType = SMvtType::find($mvtType);
        $iMvtSubType = 1;
        $oMovement->mvt_whs_class_id = $oMovType->mvt_class_id;
        $oMovement->mvt_whs_type_id = $oMovType->id_mvt_type;

        $movTypes = SMvtType::where('is_deleted', false)
                              ->where('id_mvt_type', $oMovement->mvt_whs_type_id)
                              ->lists('name', 'id_mvt_type');

        $warehouses = SWarehouse::where('is_deleted', false)
                                ->where('branch_id', session('branch')->id_branch)
                                ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as warehouse"))
                                ->orderBy('name', 'ASC')
                                ->lists('warehouse', 'id_whs');

        $iWhsSrc = 0;
        $iWhsDes = 0;
        if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
          $iWhsDes = session('whs')->id_whs;
        }
        else {
          $iWhsSrc = session('whs')->id_whs;
        }

        $oTransitWarehouse = null;
        $mvtComp = NULL;

        switch ($oMovement->mvt_whs_type_id) {
          case \Config::get('scwms.MVT_TP_IN_SAL'):
          case \Config::get('scwms.MVT_TP_IN_PUR'):
          case \Config::get('scwms.MVT_TP_OUT_SAL'):
          case \Config::get('scwms.MVT_TP_OUT_PUR'):
            $mvtComp = SMvtTrnType::where('is_deleted', false)->lists('name', 'id_mvt_trn_type');
            $iMvtSubType = \Config::get('scwms.MVT_SPT_TP_STK_RET');
            break;

          case \Config::get('scwms.MVT_TP_IN_ADJ'):
          case \Config::get('scwms.MVT_TP_OUT_ADJ'):
            $mvtComp = SMvtAdjType::where('is_deleted', false)->lists('name', 'id_mvt_adj_type');
            break;

          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_IN_TRA'):
            $mvtComp = SMvtAdjType::where('id_mvt_adj_type', 1)->lists('name', 'id_mvt_adj_type');
            $oIdTranWhs = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.WHS_ITEM_TRANSIT'));
            $oTransitWarehouse = SWarehouse::find($oIdTranWhs->val_int);
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

        $oDbPerSupply = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PERCENT_SUPPLY'));
        $oCanCreateLotMat = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.CAN_CREATE_LOT_PAL_MAT'));
        $oCanCreateLotProd = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.CAN_CREATE_LOT_PAL_PROD'));

        return view('wms.movs.whsmovs')
                          ->with('oMovement', $oMovement)
                          ->with('lStock', $lStock)
                          ->with('iOperation', $iOperation)
                          ->with('iMvtSubType', $iMvtSubType)
                          ->with('oDocument', $oDocument)
                          ->with('lDocData', $lDocData)
                          ->with('movTypes', $movTypes)
                          ->with('mvtComp', $mvtComp)
                          ->with('warehouses', $warehouses)
                          ->with('whs_src', $iWhsSrc)
                          ->with('whs_des', $iWhsDes)
                          ->with('oTransitWarehouse', $oTransitWarehouse)
                          ->with('dPerSupp', $oDbPerSupply->val_decimal)
                          ->with('bCanCreateLotMat', $oCanCreateLotMat->val_boolean)
                          ->with('bCanCreateLotProd', $oCanCreateLotProd->val_boolean);
    }

    /**
     * store saves the whs movs
     *
     * @param  Request $request
     *
     * @return redirect to whs.home
     */
    public function store(Request $request)
    {
        $oData = new SItem();
        $oMovementJs = json_decode($request->input('movement_object'));
        // $oPalletData = session()->has('pallet') ? session('pallet') : 0;

        $iWhsId = 0;
        $iWhsSrc = 0;
        $iWhsDes = 0;

        // the transfer implies two warehouses
        if ($oMovementJs->iMvtType == \Config::get('scwms.MVT_TP_OUT_TRA'))
        {
            $iWhsSrc = $oMovementJs->iWhsSrc;
            $iWhsDes = $oMovementJs->iWhsDes;
        }
        // if the movement is output implies that the warehouse is of source
        else if ($request->input('mvt_whs_class_id') == \Config::get('scwms.MVT_CLS_OUT'))
        {
            $iWhsSrc = $oMovementJs->iWhsSrc;
            $iWhsId = $iWhsSrc;
        }
        else
        {
            $iWhsDes = $oMovementJs->iWhsDes;
            $iWhsId = $iWhsDes;
        }

        $movement = new SMovement($request->all());

        $movement->mvt_whs_type_id = $oMovementJs->iMvtType;
        $movement->mvt_trn_type_id = 1;
        $movement->mvt_adj_type_id = 1;
        $movement->mvt_mfg_type_id = 1;
        $movement->mvt_exp_type_id = 1;

        switch ($movement->mvt_whs_type_id) {
          case \Config::get('scwms.MVT_TP_IN_SAL'):
          case \Config::get('scwms.MVT_TP_IN_PUR'):
          case \Config::get('scwms.MVT_TP_OUT_SAL'):
          case \Config::get('scwms.MVT_TP_OUT_PUR'):
            $movement->mvt_trn_type_id = $oMovementJs->iMvtSubType;
            break;

          case \Config::get('scwms.MVT_TP_IN_ADJ'):
          case \Config::get('scwms.MVT_TP_OUT_ADJ'):
            $movement->mvt_adj_type_id = $oMovementJs->iMvtSubType;
            break;

          case \Config::get('scwms.MVT_TP_IN_TRA'):
          case \Config::get('scwms.MVT_TP_IN_CON'):
          case \Config::get('scwms.MVT_TP_IN_PRO'):
          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_OUT_CON'):
          case \Config::get('scwms.MVT_TP_OUT_PRO'):
            $movement->mvt_mfg_type_id = $oMovementJs->iMvtSubType;
            break;

          case \Config::get('scwms.MVT_TP_IN_EXP'):
          case \Config::get('scwms.MVT_TP_OUT_EXP'):
            $movement->mvt_exp_type_id = $oMovementJs->iMvtSubType;
            break;

          default:
            # code...
            break;
        }

        $oProcess = new SMovsManagment();

        $movement->whs_id = $iWhsId;
        $movement->branch_id = $movement->warehouse->branch_id;
        $movement->year_id = session('work_year');
        $movement->auth_status_id = 1; // ??? pendientes constantes de status
        $movement->src_mvt_id = 1;
        $movement = $oProcess->assignForeignDoc($movement, $movement->mvt_whs_type_id, $oMovementJs->iDocumentId);
        $movement->auth_status_by_id = 1;
        $movement->closed_shipment_by_id = 1;
        $movement->created_by_id = \Auth::user()->id;
        $movement->updated_by_id = \Auth::user()->id;

        //transform the hash map to normal array of php
        $lRowsJs = array();
        foreach ($oMovementJs->lAuxRows as $value) {
           $key = $value['0'];
           $lRowsJs[$key] = $value['1'];
        }

        // save new lots required for the movement
        $lCreatedLots = $oProcess->saveLots($oMovementJs->lAuxlotsToCreate);

        $dTotalAmount = 0;
        $movementRows = array();
        foreach ($lRowsJs as $row) {
           $oMvtRow = new SMovementRow();

           $oMvtRow->quantity = $row->dQuantity;
           $oMvtRow->amount_unit = $row->dPrice;
           $oMvtRow->amount = $oMvtRow->quantity * $oMvtRow->amount_unit;
           $oMvtRow->item_id = $row->iItemId;
           $oMvtRow->unit_id = $row->iUnitId;
           $oMvtRow->pallet_id = $row->iPalletId;
           $oMvtRow->location_id = $row->iLocationId;

           $oMvtRow = $oProcess->assignForeignRow($oMvtRow, $movement->mvt_whs_type_id, $row->iAuxDocRowId);

           $movLotRows = array();
           if ($row->bIsLot && isset($row->lAuxlotRows)) {

             $lLotRowsJs = array();
             foreach ($row->lAuxlotRows as $value) {
                $key = $value['0'];

                if ($value['1']->iLotId == 0) {
                    if (array_key_exists($value['1']->iKeyLot, $lCreatedLots)) {
                       $value['1']->iLotId = $lCreatedLots[$value['1']->iKeyLot]->id_lot;
                    }
                }

                $lLotRowsJs[$key] = $value['1'];
             }

             foreach ($lLotRowsJs as $rowsJs) {
                 $oMovLotRow = new SMovementRowLot();
                 $oMovLotRow->quantity = $rowsJs->dQuantity;
                 $oMovLotRow->amount_unit = $rowsJs->dPrice;
                 $oMovLotRow->lot_id = $rowsJs->iLotId;
                 $oMovLotRow->is_deleted = false;

                 array_push($movLotRows, $oMovLotRow);
                 $dTotalAmount += ($oMovLotRow->quantity * $oMovLotRow->amount_unit);
             }
           }
           else {
             $dTotalAmount += ($oMvtRow->quantity * $oMvtRow->amount_unit);
           }

           $oMvtRow->setAuxLots($movLotRows);
           array_push($movementRows, $oMvtRow);
        }

        $movement->total_amount = $dTotalAmount;
        $oPalletData = null;

        $aResult = $oProcess->processTheMovement(\Config::get('scwms.OPERATION_TYPE.CREATION'),
                                                    $movement, $movementRows,
                                                    $request->input('mvt_whs_class_id'),
                                                    $oMovementJs->iMvtType,
                                                    $iWhsSrc,
                                                    $iWhsDes,
                                                    $oPalletData,
                                                    $request);

        if (is_array($aResult)) {
          if(sizeof($aResult) > 0) {
              return redirect()
                        ->back()
                        ->withErrors($aResult)
                        ->withInput();
          }
        }

        return redirect()->route('wms.movs.index', $aResult);
    }

    /**
     * edit the inventory movement
     * load all neccesary data for the edition and shows the form
     *
     * @param  integer $id id of movement
     *
     * @return view('wms.movs.whsmovs')
     */
    public function edit($id = 0)
    {
      $iOperation = \Config::get('scwms.OPERATION_TYPE.EDITION');
      $oMovement = SMovement::find($id);

      session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oMovement);

      /*
      This method tries to get the lock, if not is obtained returns an array of errors
      */
      $error = session('utils')->validateLock($oMovement);
      if (sizeof($error) > 0)
      {
        return redirect()->back()->withErrors($error);
      }

      $oMovement->rows;

      foreach ($oMovement->rows as $oRow) {
        $oRow->lotRows;
        foreach ($oRow->lotRows as $oLotRow) {
          $oLotRow->lot;
        }
      }

      $lDocData = array();
      $lStock = null;

      $iDocId = 0;
      if ($oMovement->doc_order_id != 1) {
        $iDocId = $oMovement->doc_order_id;
      }
      elseif($oMovement->doc_invoice_id != 1) {
        $iDocId = $oMovement->doc_invoice_id;
      }
      elseif($oMovement->doc_credit_note_id != 1) {
        $iDocId = $oMovement->doc_credit_note_id;
      }
      elseif($oMovement->doc_debit_note_id != 1) {
        $iDocId = $oMovement->doc_debit_note_id;
      }

      // get the document if the id != 0
      if ($iDocId != 0)
      {
          $oDocument = SDocument::find($iDocId);
          $oDocument->rows;

          $FilterDel = \Config::get('scsys.FILTER.ACTIVES');
          $sFilterDate = null;
          $iViewType = \Config::get('scwms.DOC_VIEW.DETAIL');
          $bWithPending = true;

          $lDocData = session('stock')::getSuppliedRes($oDocument->doc_category_id,
                                          $oDocument->doc_class_id,
                                          $oDocument->doc_type_id, $FilterDel,
                                          $sFilterDate, $iViewType, $iDocId,
                                          $bWithPending)->get();
      }
      else
      {
          $oDocument = 0;
      }

      $iMvtSubType = $oMovement->mvt_whs_type_id;
      $lStock = null;

      $iWhsSrc = 0;
      $iWhsDes = 0;
      if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
        $iWhsDes = $oMovement->whs_id;
      }
      else {
        $iWhsSrc = $oMovement->whs_id;
        $lStock = SMovsUtils::getStockFromWarehouse($iWhsSrc, $oMovement->id_mvt);
      }

      $warehouses = SWarehouse::where('id_whs', $oMovement->whs_id)
                              ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as warehouse"))
                              ->orderBy('name', 'ASC')
                              ->lists('warehouse', 'id_whs');

      $movTypes = SMvtType::where('is_deleted', false)->where('id_mvt_type', $oMovement->mvt_whs_type_id)->lists('name', 'id_mvt_type');

      $mvtComp = NULL;

      switch ($oMovement->mvt_whs_type_id) {
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
          $oIdTranWhs = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.WHS_ITEM_TRANSIT'));
          $oTransitWarehouse = SWarehouse::find($oIdTranWhs->val_int);
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

      $oDbPerSupply = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PERCENT_SUPPLY'));
      $oCanCreateLotMat = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.CAN_CREATE_LOT_PAL_MAT'));
      $oCanCreateLotProd = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.CAN_CREATE_LOT_PAL_PROD'));


      return view('wms.movs.whsmovs')
                        ->with('oMovement', $oMovement)
                        ->with('oDocument', $oDocument)
                        ->with('iOperation', $iOperation)
                        ->with('iMvtSubType', $iMvtSubType)
                        ->with('lDocData', $lDocData)
                        ->with('movTypes', $movTypes)
                        ->with('mvtComp', $mvtComp)
                        ->with('warehouses', $warehouses)
                        ->with('whs_src', $iWhsSrc)
                        ->with('whs_des', $iWhsDes)
                        ->with('lStock', $lStock)
                        ->with('dPerSupp', $oDbPerSupply->val_decimal)
                        ->with('bCanCreateLotMat', $oCanCreateLotMat->val_boolean)
                        ->with('bCanCreateLotProd', $oCanCreateLotProd->val_boolean);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $oMovement = SMovement::find($id);
       $oMovementJs = json_decode($request->input('movement_object'));

       $oMovement->updated_by_id = \Auth::user()->id;

       //transform the hash map to normal array of php
       $lRowsJs = array();
       foreach ($oMovementJs->lAuxRows as $value) {
          $key = $value['0'];
          $lRowsJs[$key] = $value['1'];
       }

       $oProcess = new SMovsManagment();

       // save new lots required for the movement
       $lCreatedLots = $oProcess->saveLots($oMovementJs->lAuxlotsToCreate);

       $dTotalAmount = 0;
       $movementRows = array();
       foreach ($lRowsJs as $row) {
          if ($row->iIdMovRow > 0) {
            $oMvtRow = SMovementRow::find($row->iIdMovRow);

            if (!$oMvtRow->is_deleted && $row->bIsDeleted) {
              $oMvtRow->is_deleted = true;

              $movLotRows = array();
              if ($row->bIsLot && sizeof($row->lAuxlotRows) > 0) {

                $lLotRowsJs = array();
                foreach ($row->lAuxlotRows as $value) {
                   $key = $value['0'];

                   if ($value['1']->iLotId == 0) {
                       if (array_key_exists($value['1']->iKeyLot, $lCreatedLots)) {
                          $value['1']->iLotId = $lCreatedLots[$value['1']->iKeyLot]->id_lot;
                       }
                   }

                   $lLotRowsJs[$key] = $value['1'];
                }

                foreach ($lLotRowsJs as $rowsJs) {
                    if ($rowsJs->iIdLotRow > 0) {
                        $oMovLotRow = SMovementRowLot::find($rowsJs->iIdLotRow);
                        $oMovLotRow->is_deleted = true;
                    }
                    else {
                        $oMovLotRow = new SMovementRowLot();
                        $oMovLotRow->quantity = $rowsJs->dQuantity;
                        $oMovLotRow->amount_unit = $rowsJs->dPrice;
                        $oMovLotRow->lot_id = $rowsJs->iLotId;
                    }

                    array_push($movLotRows, $oMovLotRow);
                }

                $oMvtRow->setAuxLots($movLotRows);
              }

              array_push($movementRows, $oMvtRow);
            }
            else {
              $dTotalAmount += $oMvtRow->amount;
            }
          }
          else {
            $oMvtRow = new SMovementRow();

            $oMvtRow->quantity = $row->dQuantity;
            $oMvtRow->amount_unit = $row->dPrice;
            $oMvtRow->amount = $oMvtRow->quantity * $oMvtRow->amount_unit;
            $oMvtRow->item_id = $row->iItemId;
            $oMvtRow->unit_id = $row->iUnitId;
            $oMvtRow->pallet_id = $row->iPalletId;
            $oMvtRow->location_id = $row->iLocationId;

            $oMvtRow = $oProcess->assignForeignRow($oMvtRow, $oMovement->mvt_whs_type_id, $row->iAuxDocRowId);

            $movLotRows = array();
            if ($row->bIsLot && sizeof($row->lAuxlotRows) > 0) {

              $lLotRowsJs = array();
              foreach ($row->lAuxlotRows as $value) {
                 $key = $value['0'];

                 if ($value['1']->iLotId == 0) {
                     if (array_key_exists($value['1']->iKeyLot, $lCreatedLots)) {
                        $value['1']->iLotId = $lCreatedLots[$value['1']->iKeyLot]->id_lot;
                     }
                 }

                 $lLotRowsJs[$key] = $value['1'];
              }

              foreach ($lLotRowsJs as $rowsJs) {
                  $oMovLotRow = new SMovementRowLot();
                  $oMovLotRow->quantity = $rowsJs->dQuantity;
                  $oMovLotRow->amount_unit = $rowsJs->dPrice;
                  $oMovLotRow->lot_id = $rowsJs->iLotId;

                  array_push($movLotRows, $oMovLotRow);
                  $dTotalAmount += ($oMovLotRow->quantity * $oMovLotRow->amount_unit);
              }
            }
            else {
              $dTotalAmount += ($oMvtRow->quantity * $oMvtRow->amount_unit);
            }

            $oMvtRow->setAuxLots($movLotRows);
            array_push($movementRows, $oMvtRow);
          }
       }

       $oMovement->total_amount = $dTotalAmount;
       $oPalletData = null;

       if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT')) {
          $iWhsSrc = $oMovement->whs_id;
          $iWhsDes = 0;
       }
       else {
          $iWhsSrc = 0;
          $iWhsDes = $oMovement->whs_id;
       }

       $aResult = $oProcess->processTheMovement(\Config::get('scwms.OPERATION_TYPE.EDITION'),
                                                   $oMovement, $movementRows,
                                                   $oMovement->mvt_whs_class_id,
                                                   $oMovement->mvt_whs_type_id,
                                                   $iWhsSrc,
                                                   $iWhsDes,
                                                   $oPalletData,
                                                   $request);

       if (is_array($aResult)) {
         if(sizeof($aErrors) > 0) {
             return redirect()
                       ->back()
                       ->withErrors($aErrors)
                       ->withInput();
         }
       }

       return redirect()->route('wms.movs.index', $aResult);
    }

    /**
     * this method obtains the data required to process the movement
     * on client side, obtains:
     *  the permitted items
     *  the permitted lots
     *  the permitted pallets
     *  the locations of implied warehouses
     *  the stock of warehouse
     *
     * @param  Request $request
     *                  the request contains:
     *                        source warehouse
     *                        destiny warehouse
     *                        movement class
     *                        movement type
     *
     * @return JSON JSON object of the class SData
     */
    public function getMovementData(Request $request)
    {
        $oData = new SData();
        $oData->lItems = SMovsUtils::getElementsToWarehouse(
                                                      $request->whs_source,
                                                      $request->whs_des,
                                                      \Config::get('scwms.ELEMENTS_TYPE.ITEMS')
                                                    );
        $oData->lPallets = SMovsUtils::getElementsToWarehouse(
                                                      $request->whs_source,
                                                      $request->whs_des,
                                                      \Config::get('scwms.ELEMENTS_TYPE.PALLETS')
                                                    );
        $oData->lLots = SMovsUtils::getElementsToWarehouse(
                                                      $request->whs_source,
                                                      $request->whs_des,
                                                      \Config::get('scwms.ELEMENTS_TYPE.LOTS')
                                                    );

        $iWhs = 0;
        if ($request->mvt_cls == \Config::get('scwms.MVT_CLS_OUT')) {
            $oData->lSrcLocations = SMovsUtils::getResWarehouseLocations($request->whs_source)->get();
            $iWhs = $request->whs_source;
        }
        else {
            $oData->lDesLocations = SMovsUtils::getResWarehouseLocations($request->whs_des)->get();
            $iWhs = $request->whs_des;
        }

        $oProcess = new SMovsManagment();

        if ($request->mvt_cls == \Config::get('scwms.MVT_CLS_OUT') ||
              $request->mvt_type == \Config::get('scwms.MVT_TP_OUT_TRA')) {
                $oWarehouse = SWarehouse::find($request->whs_source);
                $oData->iFolioSrc = $oProcess->getNewFolio($oWarehouse->branch_id,
                                                              $oWarehouse->id_whs,
                                                        \Config::get('scwms.MVT_CLS_OUT'),
                                                        $request->mvt_type);
        }
        if ($request->mvt_cls == \Config::get('scwms.MVT_CLS_IN') ||
              $request->mvt_type == \Config::get('scwms.MVT_TP_OUT_TRA')) {
                $oWarehouse = SWarehouse::find($request->whs_des);
                $oData->iFolioDes = $oProcess->getNewFolio($oWarehouse->branch_id,
                                                              $oWarehouse->id_whs,
                                                              \Config::get('scwms.MVT_CLS_IN'),
                                                              $request->mvt_type);
        }

        $oData->lStock = SMovsUtils::getStockFromWarehouse($iWhs, $request->mvt_id);

        return json_encode($oData);
    }

    /**
     * Method that search the code written by the user
     *
     * @param  Request $request contains the code written by the user in the
     *                           variable "code"
     *
     * @return JSON of object SData
     */
    public function searchElement(Request $request)
    {
        $oObject = SBarcode::decodeBarcode($request->code);
        $oData = new SData();

        if ($oObject instanceof SItem) {
            $oData->iElementType = \Config::get('scwms.ELEMENTS_TYPE.ITEMS');
            $oObject->unit;
        }
        elseif ($oObject instanceof SWmsLot) {
            $oData->iElementType = \Config::get('scwms.ELEMENTS_TYPE.LOTS');
            $oObject->item;
            $oObject->unit;
        }
        elseif ($oObject instanceof SPallet) {
            $oData->iElementType = \Config::get('scwms.ELEMENTS_TYPE.PALLETS');
            $oObject->item;
            $oObject->unit;

            $aParameters = array();
            $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $oObject->item_id;
            $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $oObject->unit_id;
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $oObject->id_pallet;
            $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');

            $oData->dStock = session('stock')->getStock($aParameters)[\Config::get('scwms.STOCK.GROSS')];
        }
        elseif ($oObject instanceof SLocation) {
            $oData->iElementType = \Config::get('scwms.ELEMENTS_TYPE.LOCATIONS');
        }

        $oData->oElement = $oObject;

        return json_encode($oData);
    }

    /**
     * Validate if the lots introduced by the client
     * are new or already exists on the datatabase
     *
     * @param  Request $request
     *
     * @return JSON of object SData
     */
    public function validateRow(Request $request)
    {
        $lLots = array();
        $lLotsToCreate = array();
        $oData = new SData();

        if ($request->value != '') {
           $oRow = json_decode($request->value);
           $oItem = SItem::find($oRow->iItemId);

           if ($oItem->is_lot) {
               $lLotsJs = $oRow->lAuxlotRows;
               $lLotsToCreateJs = $oRow->lAuxlotsToCreate;

               foreach ($lLotsJs as $value) {
                  $key = $value['0'];
                  $lLots[$key] = $value['1'];
               }

               $oValidation = new SLotsValidations($lLots, $lLotsToCreateJs, $oItem);

               $oValidation->validateLots();
               $oData->lErrors = $oValidation->getErrors();
               $oData->lNewLots = $oValidation->getLotsToCreate();
               $oData->lLotRows = $oValidation->getLots();
           }

        }

        return json_encode($oData);
    }
}
