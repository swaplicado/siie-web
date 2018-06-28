<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Laracasts\Flash\Flash;

use App\SUtils\SProcess;
use App\SUtils\SUtil;
use App\SUtils\SGuiUtils;
use App\SUtils\SMenu;
use App\ERP\SErpConfiguration;

use App\ERP\SDocument;
use App\SCore\SInventoryCore;
use App\WMS\SWarehouse;
use App\WMS\SMovement;
use App\WMS\SMvtType;

class SInventoriesController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.INVENTORY_OPERATION'), \Config::get('scsys.MODULES.WMS'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function emptyWarehouseIndex(Request $request)
    {
        $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, true);

        $title = trans('wms.EMPTY_WAREHOUSE');

        return view('wms.inventories.emptywhs')
                    ->with('lWarehouses', $lWarehouses)
                    ->with('title', $title);
    }

    /**
     * index
     *
     * @param  Request $request
     */
    public function initialIndex(Request $request)
    {
       $title = trans('wms.GENERATE_INITIAL_INVENTORY');

       return view('wms.inventories.initialinventories')
                  ->with('title', $title);
    }

    /**
     * @param  Request $request
     *
     * @return redirect()->route('wms.home');
     */
    public function generateInitialInventory(Request $request)
    {
        $oInvStk = new SInventoryCore();
        $aResult = $oInvStk->generateInitialInventory($request->year, $request);

        if (is_array($aResult)) {
          if(sizeof($aResult) > 0) {
              return redirect()
                        ->back()
                        ->withErrors($aResult)
                        ->withInput();
          }
        }

        Flash::success(trans('messages.GENERATED_INVENTORY'))->important();

        return redirect()->route('wms.home');
    }

    /**
     * @param  Request $request
     *
     * @return JSON(lStock)
     */
    public function getStock(Request $request)
    {
       $lStock = array();
       $oInvStk = new SInventoryCore();

       $lStock = $oInvStk->getStock($request->iWhs, 0, $request->dt_date);

       return json_encode($lStock);
    }

    public function physicalInventory(Request $request)
    {
        $title = trans('wms.PHYSICAL_INVENTORY');

        $oMovement = new SMovement();
        $oMovement->mvt_whs_class_id = \Config::get('scwms.MVT_CLS_IN');
        $oMovement->mvt_whs_type_id = \Config::get('scwms.PHYSICAL_INVENTORY');
        $oMovement->branch_id = session('branch')->id_branch;

        $movTypes[\Config::get('scwms.PHYSICAL_INVENTORY')] = trans('wms.PHYSICAL_INVENTORY_UPP');

        $mvtComp[1] = 'N/A';
        $iMvtSubType = \Config::get('scwms.N_A');

        $iOperation = \Config::get('scwms.OPERATION_TYPE.CREATION');

        $warehouses = SWarehouse::where('is_deleted', false)
                                ->where('branch_id', session('branch')->id_branch)
                                ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as warehouse"))
                                ->whereIn('id_whs', session('utils')->getUserWarehousesArray())
                                ->orderBy('code', 'ASC')
                                ->lists('warehouse', 'id_whs');

        $oDbPerSupply = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PERCENT_SUPPLY'));

        $oDocument = 0;
        $lDocData = null;
        $lStock = null;

        return view('wms.movs.whsmovs')
                  ->with('oMovement', $oMovement)
                  ->with('iOperation', $iOperation)
                  ->with('warehouses', $warehouses)
                  ->with('whs_des', session('whs')->id_whs)
                  ->with('movTypes', $movTypes)
                  ->with('mvtComp', $mvtComp)
                  ->with('iMvtSubType', $iMvtSubType)
                  ->with('bIsExternalTransfer', false)
                  ->with('oDocument', $oDocument)
                  ->with('lDocData', $lDocData)
                  ->with('lStock', $lStock)
                  ->with('dPerSupp', $oDbPerSupply->val_decimal)
                  ->with('sTitle', $title);
    }

    public function createPhysicalInventory(Request $request)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id)) {
          return redirect()->route('notauthorized');
        }


    }

  }
