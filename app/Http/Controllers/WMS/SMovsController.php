<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WMS\SStockController;
use App\Http\Controllers\QMS\SSegregationsController;
use Laracasts\Flash\Flash;
use App\Http\Requests\WMS\SMovRequest;
use App\SBarcode\SBarcode;
use App\SUtils\SStockUtils;
use App\SCore\SMovsManagment;
use App\SCore\SReceptions;
use App\SCore\SLotsValidations;
use App\SCore\StransfersCore;
use App\SCore\SMovsCore;
use App\SCore\SInventoryCore;

use App\SUtils\SMovsUtils;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;
use App\SUtils\SValidation;
use App\SUtils\SGuiUtils;
use App\ERP\SErpConfiguration;
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
use App\WMS\SMvtInternalType;
use App\WMS\SWhsType;
use App\WMS\SWmsLot;
use App\WMS\SItemContainer;
use App\WMS\SMovement;
use App\WMS\SMovementRow;
use App\WMS\SMovementRowLot;
use App\WMS\Data\SData;

use App\MMS\SProductionOrder;

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
        $iFilterWhs = $request->warehouse == null ? session('whs')->id_whs : $request->warehouse;

        // $lMovRows = SMovementRow::Search($this->iFilter, $sFilterDate);
        $lMovRows = SMovsCore::getMovementsIndex($sFilterDate, $iFilterWhs);

        $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, false);
        $lWarehouses['0'] = 'TODOS';

        return view('wms.movs.index')
                    ->with('iFolio', $iFolio)
                    ->with('lWarehouses', $lWarehouses)
                    ->with('iFilterWhs', $iFilterWhs)
                    ->with('iFilter', $this->iFilter)
                    ->with('sFilterDate', $sFilterDate)
                    ->with('rows', $lMovRows);
    }

    /**
     * Movements Index detail
     *
     * @param  Request $request [description]
     *
     * @return view('wms.movs.indexdetail')
     */
    public function movementsIndex(Request $request)
    {
       $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
       $iFilterWhs = $request->warehouse == null ? session('whs')->id_whs : $request->warehouse;

       $movs = SMovsCore::getMovsDetailIndex($sFilterDate, $iFilterWhs);

       $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, true);
       $lWarehouses['0'] = 'TODOS';

       return view('wms.movs.indexdetail')
                   ->with('lWarehouses', $lWarehouses)
                   ->with('iFilterWhs', $iFilterWhs)
                   ->with('iFilter', $this->iFilter)
                   ->with('sFilterDate', $sFilterDate)
                   ->with('lRows', $movs)
                   ->with('title', trans('wms.WHS_MOVS_DETAIL'));
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
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $iFilterWhs = $request->warehouse == null ? session('whs')->id_whs : $request->warehouse;

        $lMovs = SMovsCore::getInventoryDocs($sFilterDate, $iFilterWhs, $this->iFilter);

        $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, false);
        $lWarehouses['0'] = 'TODOS';

        return view('wms.movs.inventorydocs')
                    ->with('iFilter', $this->iFilter)
                    ->with('sFilterDate', $sFilterDate)
                    ->with('lWarehouses', $lWarehouses)
                    ->with('iFilterWhs', $iFilterWhs)
                    ->with('actualUserPermission', $this->oCurrentUserPermission)
                    ->with('lMovs', $lMovs);
    }

    public function receiveMovsIndex(Request $request)
    {
        $lList = SReceptions::getPendingReceptions(session('branch')->id_branch);

        return view('wms.movs.transfers.receptions')->with('lList', $lList)
                                                  ->with('sTitle', trans('wms.MOV_WHS_RECEIVE_EXTERNAL_TRS_OUT'));
    }

    public function getTransferred(Request $request)
    {
        $lList = SReceptions::getTransferredTransfers(session('branch')->id_branch);

        return view('wms.movs.transfers.transfers')->with('lList', $lList)
                                                  ->with('iClass', \Config::get('scwms.MVT_CLS_OUT'))
                                                  ->with('actualUserPermission', $this->oCurrentUserPermission)
                                                  ->with('sTitle', 'Traspasos externos enviados');
    }

    public function getReceived(Request $request)
    {
        $lList = SReceptions::getReceivedTransfers(session('branch')->id_branch);

        return view('wms.movs.transfers.transfers')->with('lList', $lList)
                                  ->with('iClass', \Config::get('scwms.MVT_CLS_IN'))
                                  ->with('actualUserPermission', $this->oCurrentUserPermission)
                                  ->with('sTitle', 'Traspasos externos recibidos');
    }

    public function receiveTransfer($iMov = 0)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $oCore = new StransfersCore();

        $iOperation = \Config::get('scwms.OPERATION_TYPE.CREATION');
        $bIsReceiveTransfer = true;

        $oMovementSrc = $oCore->getReceivedFromMovement($iMov);

        $oMovRef = SMovement::find($oMovementSrc->src_mvt_id);

        foreach ($oMovementSrc->rows as $row) {
           $row->location;
           $row->quantity_received = $row->dReceived;
           foreach ($row->lotRows as $lotRow) {
              $lotRow->lot;
              $lotRow->quantity_received = $lotRow->dReceived;
           }
        }

        $oMovement = new SMovement();
        $oMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_OUT');
        $oMovement->mvt_whs_type_id = \Config::get('scwms.MVT_TP_OUT_TRA');
        $oMovement->mvt_trn_type_id = \Config::get('scwms.MVT_INTERNAL_TRANSFER');
        $oMovement->src_mvt_id = $oMovementSrc->id_mvt;
        $oMovement->branch_id = session('branch')->id_branch;

        $iWhsSrc = session('transit_whs')->id_whs;
        $iWhsDes = 0;

        $warehouses = SWarehouse::where('is_deleted', false)
                                ->where('branch_id', session('branch')->id_branch)
                                ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as warehouse"))
                                ->orderBy('code', 'ASC')
                                ->lists('warehouse', 'id_whs');

        return view('wms.movs.transfers.receivetransfer')
                                        ->with('oMovementSrc', $oMovementSrc)
                                        ->with('oMovement', $oMovement)
                                        ->with('iWhsSrc', $iWhsSrc)
                                        ->with('oMovRef', $oMovRef)
                                        ->with('iWhsDes', $iWhsDes)
                                        ->with('warehouses', $warehouses)
                                        ->with('iOperation', $iOperation)
                                        ->with('sTitle', trans('wms.MOV_WHS_RECEIVE_EXTERNAL_TRS_OUT'))
                                        ;
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
    public function create(Request $request, $mvtType, $sTitle = '', $iDocId = 0)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id)) {
          return redirect()->route('notauthorized');
        }

        $iOperation = \Config::get('scwms.OPERATION_TYPE.CREATION');
        $bIsExternalTransfer = false;
        $branches = array();

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
                                ->whereIn('id_whs', session('utils')->getUserWarehousesArray())
                                ->orderBy('code', 'ASC')
                                ->lists('warehouse', 'id_whs');

        if ($sTitle == trans('wms.MOV_WHS_SEND_EXTERNAL_TRS_OUT') ||
              $sTitle == trans('wms.MOV_WHS_RECEIVE_EXTERNAL_TRS_OUT')) {
                $bIsExternalTransfer = true;
                $branches = SBranch::where('is_deleted', false)
                                        ->where('partner_id', session('partner')->id_partner)
                                        ->select('id_branch', \DB::raw("CONCAT(code, '-', name) as branch"))
                                        ->orderBy('code', 'ASC')
                                        ->lists('branch', 'id_branch');
        }

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
        $pallets = array();

        $lSrcPO = array();
        $lDesPO = array();
        $iSrcPO = 0;
        $iDesPO = 0;
        $iAssType = 0;
        $lItemsForOrders = array();

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
            $iMvtSubType = \Config::get('scwms.MVT_ADJ_TP_PRO');
            break;

          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_IN_TRA'):
            $iMvtSubType = \Config::get('scwms.MVT_INTERNAL_TRANSFER');
            $mvtComp = SMvtInternalType::where('id_mvt_internal_type', $iMvtSubType)->lists('name', 'id_mvt_internal_type');
            $oTransitWarehouse = session('transit_whs')->id_whs;
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
            if ($oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN')) {
               $iMvtSubType = \Config::get('scwms.MVT_INTERNAL_DIV_PALLET');
            }
            else {
               $iMvtSubType = \Config::get('scwms.MVT_INTERNAL_ADD_TO_PALLET');
            }
            $mvtComp = SMvtInternalType::where('id_mvt_internal_type', $iMvtSubType)->lists('name', 'id_mvt_internal_type');
            $pallets = array();
            break;

          case \Config::get('scwms.MVT_OUT_DLVRY_RM'):
            $iMvtSubType = \Config::get('scwms.MVT_MFG_TP_MAT');
            $mvtComp = SMvtMfgType::where('is_deleted', false)
                                  ->where('id_mvt_mfg_type', \Config::get('scwms.MVT_MFG_TP_MAT'))
                                  ->lists('name', 'id_mvt_mfg_type');

            $lSrcPO = SProductionOrder::where('is_deleted', false)
                                      ->selectRaw('(CONCAT(LPAD(folio, '.
                                            session('long_folios').', "0"),
                                                "-", identifier)) as prod_ord,
                                                id_order')
                                      ->lists('prod_ord', 'id_order');
            $lDesPO = [];

            $iSrcPO = 0;
            $iDesPO = 0;

            $iAssType = \Config::get('scmms.ASSIGN_TYPE.MP');

            break;

          case \Config::get('scwms.MVT_OUT_DLVRY_PP'):
            $iMvtSubType = \Config::get('scwms.MVT_MFG_TP_PRO');
            $mvtComp = SMvtMfgType::where('is_deleted', false)
                                  ->where('id_mvt_mfg_type', \Config::get('scwms.MVT_MFG_TP_PRO'))
                                  ->lists('name', 'id_mvt_mfg_type');

            $lSrcPO = SProductionOrder::where('is_deleted', false)
                                      ->selectRaw('(CONCAT(LPAD(folio, '.
                                            session('long_folios').', "0"),
                                                "-", identifier)) as prod_ord,
                                                id_order')
                                      ->whereRaw('id_order IN (SELECT father_order_id
                                                              FROM
                                                              mms_production_orders
                                                              WHERE father_order_id > 1
                                                              AND is_deleted = false
                                                              )')
                                      ->lists('prod_ord', 'id_order');
            $lDesPO = [];

            $iSrcPO = 0;
            $iDesPO = 0;

            $iAssType = \Config::get('scmms.ASSIGN_TYPE.PP');

            $lItemsForOrders = SItem::whereRaw('id_item IN
                                  (SELECT item_id FROM mms_production_orders
                                    WHERE NOT is_deleted)')
                                  ->get();
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
                          ->with('bIsExternalTransfer', $bIsExternalTransfer)
                          ->with('iMvtSubType', $iMvtSubType)
                          ->with('oDocument', $oDocument)
                          ->with('lDocData', $lDocData)
                          ->with('movTypes', $movTypes)
                          ->with('mvtComp', $mvtComp)
                          ->with('warehouses', $warehouses)
                          ->with('branches', $branches)
                          ->with('whs_src', $iWhsSrc)
                          ->with('whs_des', $iWhsDes)
                          ->with('lSrcPO', $lSrcPO)
                          ->with('lDesPO', $lDesPO)
                          ->with('iSrcPO', $iSrcPO)
                          ->with('iDesPO', $iDesPO)
                          ->with('iAssType', $iAssType)
                          ->with('lItemsForOrders', $lItemsForOrders)
                          ->with('oTransitWarehouse', $oTransitWarehouse)
                          ->with('lPallets', $pallets)
                          ->with('dPerSupp', $oDbPerSupply->val_decimal)
                          ->with('bCanCreateLotMat', $oCanCreateLotMat->val_boolean)
                          ->with('bCanCreateLotProd', $oCanCreateLotProd->val_boolean)
                          ->with('sTitle', $sTitle);
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
        if ($oMovementJs->iMvtType == \Config::get('scwms.MVT_TP_OUT_TRA')
            || SGuiUtils::isProductionMovement($oMovementJs->iMvtType))
        {
            $iWhsSrc = $oMovementJs->iWhsSrc;
            $iWhsDes = $oMovementJs->iWhsDes;
            $iWhsId = $iWhsSrc;
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

          case \Config::get('scwms.MVT_TP_OUT_TRA'):
          case \Config::get('scwms.MVT_TP_IN_TRA'):
            $movement->mvt_adj_type_id = 1;
            break;

          case \Config::get('scwms.MVT_TP_IN_CON'):
          case \Config::get('scwms.MVT_TP_IN_PRO'):
          case \Config::get('scwms.MVT_TP_OUT_CON'):
          case \Config::get('scwms.MVT_TP_OUT_PRO'):
            $movement->mvt_mfg_type_id = $oMovementJs->iMvtSubType;
            break;

          case \Config::get('scwms.MVT_TP_IN_EXP'):
          case \Config::get('scwms.MVT_TP_OUT_EXP'):
            $movement->mvt_exp_type_id = $oMovementJs->iMvtSubType;
            break;

          case \Config::get('scwms.MVT_OUT_DLVRY_RM'):
          case \Config::get('scwms.MVT_OUT_DLVRY_PP'):
            $movement->mvt_mfg_type_id = $oMovementJs->iMvtSubType;
            break;

          default:
            # code...
            break;
        }

        $oProcess = new SMovsManagment();

        $movement->whs_id = $iWhsId;
        $movement->branch_id = $movement->warehouse->branch_id;
        $movement->iAuxBranchDes = $oMovementJs->iBranchDes;
        $movement->year_id = session('work_year');
        $movement->auth_status_id = 1; // ??? pendientes constantes de status
        $movement->src_mvt_id = $movement->src_mvt_id > 0 ? $movement->src_mvt_id : 1;
        $movement = $oProcess->assignForeignDoc($movement, $movement->mvt_whs_type_id, $oMovementJs->iDocumentId);
        $movement->prod_ord_id = 1;
        $movement->auth_status_by_id = 1;
        $movement->closed_shipment_by_id = 1;
        $movement->created_by_id = \Auth::user()->id;
        $movement->updated_by_id = \Auth::user()->id;

        $movementRows = $this->createRows($movement, $oMovementJs, $oProcess);

        $iPallet = $oMovementJs->iAuxPallet;
        $iPalletLocation = $oMovementJs->iAuxPalletLocation;

        $movement->aAuxPOs[SMovement::SRC_PO] = $oMovementJs->iPOSrc;
        $movement->aAuxPOs[SMovement::DES_PO] = $oMovementJs->iPODes;
        $movement->aAuxPOs[SMovement::ASS_TYPE] = $oMovementJs->iAuxAssigType;

        $aResult = $oProcess->processTheMovement(\Config::get('scwms.OPERATION_TYPE.CREATION'),
                                                    $movement, $movementRows,
                                                    $request->input('mvt_whs_class_id'),
                                                    $oMovementJs->iMvtType,
                                                    $iWhsSrc,
                                                    $iWhsDes,
                                                    $iPallet,
                                                    $iPalletLocation,
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

      $oProcess = new SMovsManagment();

      $lErr = $oProcess->canMovBeModified($oMovement);
      if (sizeof($lErr) > 0)
      {
        return redirect()->back()->withErrors($lErr);
      }

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
        $oRow->item;
        $oRow->unit;
        $oRow->location;
        $oRow->pallet;

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

      $lStock = null;
      $oPallet = null;

      $iWhsSrc = 0;
      $iWhsDes = 0;
      if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
        $iWhsDes = $oMovement->whs_id;
      }
      else {
        $iWhsSrc = $oMovement->whs_id;
        $lStock = SMovsUtils::getStockFromWarehouse($iWhsSrc, $oMovement->id_mvt);
      }

      $movTypes = SMvtType::where('is_deleted', false)->where('id_mvt_type', $oMovement->mvt_whs_type_id)->lists('name', 'id_mvt_type');

      $iMvtSubType = 0;
      $iAssType = 0;
      $lSrcPO = [];
      $lDesPO = [];
      $iSrcPO = 0;
      $iDesPO = 0;
      $mvtComp = NULL;

      switch ($oMovement->mvt_whs_type_id) {
        case \Config::get('scwms.MVT_TP_IN_SAL'):
        case \Config::get('scwms.MVT_TP_IN_PUR'):
        case \Config::get('scwms.MVT_TP_OUT_SAL'):
        case \Config::get('scwms.MVT_TP_OUT_PUR'):
          $mvtComp = SMvtTrnType::where('is_deleted', false)->lists('name', 'id_mvt_trn_type');
          $iMvtSubType = $oMovement->mvt_trn_type_id;
          break;

        case \Config::get('scwms.MVT_TP_IN_ADJ'):
        case \Config::get('scwms.MVT_TP_OUT_ADJ'):
          $mvtComp = SMvtAdjType::where('is_deleted', false)->lists('name', 'id_mvt_adj_type');
          $iMvtSubType = $oMovement->mvt_adj_type_id;
          break;

        case \Config::get('scwms.MVT_TP_OUT_TRA'):
        case \Config::get('scwms.MVT_TP_IN_TRA'):
          $iMvtSubType = \Config::get('scwms.MVT_INTERNAL_TRANSFER');
          $mvtComp = SMvtInternalType::where('id_mvt_internal_type', $iMvtSubType)
                                        ->lists('name', 'id_mvt_internal_type');

          $oIdTranWhs = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.WHS_ITEM_TRANSIT'));
          $oTransitWarehouse = SWarehouse::find($oIdTranWhs->val_int);
          $oMvtIn = SMovement::where('src_mvt_id', $id)
                                ->take(1)->get();
          $iWhsDes = $oMvtIn[0]->whs_id;

          if ($oMvtIn->mvt_mfg_type_id > 1) {
            switch ($oMvtIn->mvt_mfg_type_id) {
              case \Config::get('scwms.MVT_MFG_TP_MAT'):
                    $iAssType = \Config::get('scmms.ASSIGN_TYPE.MP');
                    $lSrcPO = SProductionOrder::where('id_order', $oMvtIn->prod_ord_id)
                                              ->selectRaw('(CONCAT(LPAD(folio, '.
                                                    session('long_folios').', "0"),
                                                        "-", identifier)) as prod_ord,
                                                        id_order')
                                              ->lists('prod_ord', 'id_order');
                    $iSrcPO = $oMvtIn->prod_ord_id;
                    $lDesPO = [];
                    break;

              case \Config::get('scwms.MVT_MFG_TP_PRO'):
                    $iAssType = \Config::get('scmms.ASSIGN_TYPE.PP');
                    $lSrcPO = SProductionOrder::where('id_order', $oMovement->prod_ord_id)
                                              ->selectRaw('(CONCAT(LPAD(folio, '.
                                                    session('long_folios').', "0"),
                                                        "-", identifier)) as prod_ord,
                                                        id_order')
                                              ->lists('prod_ord', 'id_order');

                    $lDesPO = SProductionOrder::where('id_order', $oMvtIn->prod_ord_id)
                                              ->selectRaw('(CONCAT(LPAD(folio, '.
                                                    session('long_folios').', "0"),
                                                        "-", identifier)) as prod_ord,
                                                        id_order')
                                              ->lists('prod_ord', 'id_order');

                    $iSrcPO = $oMovement->prod_ord_id;
                    $iDesPO = $oMvtIn->prod_ord_id;
                    break;

              default:
                // code...
                break;
            }
          }
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
          if ($oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN')) {
             $iMvtSubType = \Config::get('scwms.MVT_INTERNAL_DIV_PALLET');
          }
          else {
             $iMvtSubType = \Config::get('scwms.MVT_INTERNAL_ADD_TO_PALLET');
          }
          $mvtComp = SMvtInternalType::where('id_mvt_internal_type', $iMvtSubType)->lists('name', 'id_mvt_internal_type');
          break;

        default:
          # code...
          break;
      }

      $warehouses = SWarehouse::where('id_whs', $iWhsSrc)
                      ->orWhere('id_whs', $iWhsDes)
                      ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as warehouse"))
                      ->orderBy('name', 'ASC')
                      ->lists('warehouse', 'id_whs');

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
                        ->with('bIsExternalTransfer', false)
                        ->with('dPerSupp', $oDbPerSupply->val_decimal)
                        ->with('lSrcPO', [])
                        ->with('lDesPO', [])
                        ->with('iSrcPO', 0)
                        ->with('iDesPO', 0)
                        ->with('iAssType', $iAssType)
                        ->with('bCanCreateLotMat', $oCanCreateLotMat->val_boolean)
                        ->with('sTitle', 'Modificación')
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
       $oMovement->dt_date = $request->input('dt_date');

       $oMovement->updated_by_id = \Auth::user()->id;

       $oProcess = new SMovsManagment();

       $movementRows = $this->createRows($oMovement, $oMovementJs, $oProcess);

       $iPallet = $oMovementJs->iAuxPallet;
       $iPalletLocation = $oMovementJs->iAuxPalletLocation;

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
                                                   $iPallet,
                                                   $iPalletLocation,
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        session('utils')->validateDestroy($this->oCurrentUserPermission->privilege_id);

        $oMov = SMovement::find($id);
        $oProcess = new SMovsManagment();

        $aErrors = $oProcess->canMovBeErasedOrActivated($oMov, \Config::get('scwms.MOV_ACTION.ERASE'));

        if (is_array($aErrors) && sizeof($aErrors) > 0) {
           redirect()->back()->withErrors($aErrors);
        }

        $aErr = $oProcess->eraseMov($oMov, $request);

        if (is_array($aErr) && sizeof($aErr) > 0) {
           redirect()->back()->withErrors($aErr);
        }

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->back();
    }



    public function activate(Request $request, $id)
    {
        $oMov = SMovement::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oMov);

        $oProcess = new SMovsManagment();

        $aErrors = $oProcess->canMovBeErasedOrActivated($oMov, \Config::get('scwms.MOV_ACTION.ACTIVATE'));

        if (is_array($aErrors) && sizeof($aErrors) > 0) {
           redirect()->back()->withErrors($aErrors);
        }

        $lReferencedMovs = SMovement::where('src_mvt_id', $oMov->id_mvt)
                                    ->where('is_deleted', true)
                                    ->where('is_system', true)
                                    ->get();

        foreach ($lReferencedMovs as $mov) {
          $mov->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
          $mov->updated_by_id = \Auth::user()->id;
          $errors = $oMov->save();

          if (is_array($errors) && sizeof($errors) > 0) {
             redirect()->back()->withErrors($errors);
          }
        }

        $oMov->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oMov->updated_by_id = \Auth::user()->id;

        $errors = $oMov->save();
        if (is_array($errors) && sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }

        try
        {
          $errors = \DB::connection('company')->transaction(function() use ($oMov, $request) {
            $stkController = new SStockController();
            $stkController->store($request, $oMov);
          });

          if (is_array($errors) && sizeof($errors) > 0) {
             return $errors;
          }
        }
        catch (\Exception $e)
        {
           return [$e];
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->back();
    }

    public function print($id = 0)
    {
       $oMovement = SMovement::find($id);
       $oMovement->rows;

       $view = view('wms.movs.print', ['oMovement' => $oMovement])->render();
        // set ukuran kertas dan orientasi
        $pdf = \PDF::loadHTML($view)->setPaper('letter', 'potrait')->setWarnings(false);
        // cetak
        return $pdf->stream();
    }

    /**
     * create the rows of movement to be saved or updated
     *
     * @param  SMovement $oMovement
     * @param  SMovement (Client) $oMovementJs
     * @param  SMovsManagment $oProcess
     *
     * @return array[SMovementRow] with lots
     */
    private function createRows($oMovement, $oMovementJs, $oProcess)
    {
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
        if (! $row->bIsDeleted) {
           $oMvtRow = new SMovementRow();

           $oMvtRow->quantity = $row->dQuantity;
           $oMvtRow->amount_unit = $row->dPrice;
           $oMvtRow->amount = $oMvtRow->quantity * $oMvtRow->amount_unit;
           $oMvtRow->item_id = $row->iItemId;
           $oMvtRow->unit_id = $row->iUnitId;
           $oMvtRow->pallet_id = $row->iPalletId;
           $oMvtRow->location_id = $row->iLocationId;
           $oMvtRow->is_deleted = $row->bIsDeleted;
           $oMvtRow->iAuxLocationDesId = $row->iLocationDesId;

           $oMvtRow = $oProcess->assignForeignRow($oMvtRow, $oMovement->mvt_whs_type_id, $row->iAuxDocRowId);

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
                 $oMovLotRow->amount = $oMovLotRow->amount_unit * $oMovLotRow->quantity;
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
      }

      $oMovement->total_amount = $dTotalAmount;

      return $movementRows;
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

        if ($request->mvt_type == \Config::get('scwms.PALLET_RECONFIG_IN')) {
            $request->whs_source = $request->whs_des;
        }

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

            if ($request->mvt_type == \Config::get('scwms.MVT_TP_OUT_TRA')) {
                $oData->lDesLocations = SMovsUtils::getResWarehouseLocations($request->whs_des)->get();
            }
        }
        else {
            $oData->lDesLocations = SMovsUtils::getResWarehouseLocations($request->whs_des)->get();
            $iWhs = $request->whs_des;
        }

        $oProcess = new SMovsManagment();

        if ($request->mvt_cls == \Config::get('scwms.MVT_CLS_OUT')
              || $request->mvt_type == \Config::get('scwms.MVT_TP_OUT_TRA')
                || $request->mvt_type == \Config::get('scwms.MVT_OUT_DLVRY_RM')
                  || $request->mvt_type == \Config::get('scwms.MVT_OUT_DLVRY_PP')) {
                $oWarehouse = SWarehouse::find($request->whs_source);
                $oData->iFolioSrc = $oProcess->getNewFolio($oWarehouse->branch_id,
                                                              $oWarehouse->id_whs,
                                                        \Config::get('scwms.MVT_CLS_OUT'),
                                                        $request->mvt_type);
        }
        if ($request->mvt_cls == \Config::get('scwms.MVT_CLS_IN')
             || $request->mvt_type == \Config::get('scwms.MVT_TP_OUT_TRA')
              || $request->mvt_type == \Config::get('scwms.MVT_OUT_DLVRY_RM')
                || $request->mvt_type == \Config::get('scwms.MVT_OUT_DLVRY_PP')) {
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

            $select = 'sum(ws.input) as inputs,
                               sum(ws.output) as outputs,
                               (sum(ws.input) - sum(ws.output)) as stock,
                               ei.code as item_code,
                               ei.name as item,
                               eu.code as unit,
                               wl.id_lot,
                               wl.lot,
                               wl.dt_expiry,
                               wp.id_pallet,
                               wwl.id_whs_location,
                               ww.id_whs';

            $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $select;

            $lStock = session('stock')->getStockResult($aParameters);
            $oData->lPalletStock = $lStock->groupBy('id_whs')
                                            ->groupBy('id_whs_location')
                                            ->groupBy('id_pallet')
                                            ->groupBy('id_lot')
                                            ->groupBy('id_item')
                                            ->groupBy('id_unit')
                                            ->having('stock', '>', 0)
                                            ->get();
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

               $oValidation = new SLotsValidations($lLots, $lLotsToCreateJs, $oRow->iItemId, $oRow->iUnitId);

               $oValidation->validateLots();
               $oData->lErrors = $oValidation->getErrors();
               $oData->lNewLots = $oValidation->getLotsToCreate();
               $oData->lLotRows = $oValidation->getLots();


               if ($request->iMvtType == \Config::get('scwms.MVT_TP_OUT_SAL') && sizeof($oData->lErrors == 0)) {
                 $oValidation->validatelotsByExpiration($request->iMovement, $request->iPartner, $request->iAddress);
                 $oData->oLastLot = $oValidation->getLastLot();
                 $oData->lErrors = $oValidation->getErrors();
               }
           }

        }

        return json_encode($oData);
    }

    public function getProductionData(Request $request)
    {
       $iSrcPO = $request->po_src;
       $iDesPO = $request->po_des;
       $iAssignType = $request->assig_type;

       $oData = new \App\MMS\data\SData;

       $oSrcPO = SProductionOrder::find($iSrcPO);
       $oSrcPO->item;
       $oSrcPO->formula;

       $oData->oSrcPO = $oSrcPO;

       switch ($iAssignType) {
         case \Config::get('scmms.ASSIGN_TYPE.MP'):

           break;
         case \Config::get('scmms.ASSIGN_TYPE.PP'):
            $lDesPO = SProductionOrder::where('father_order_id', $iSrcPO)
                              ->where('is_deleted', false)
                              ->get();

            foreach ($lDesPO as $oPO) {
               $oPO->formula;
               $oPO->item;
               $oPO->unit;
            }

            $oData->lDesPO = $lDesPO;
           break;

         default:
           // code...
           break;
       }

       return json_encode($oData);
    }
}
