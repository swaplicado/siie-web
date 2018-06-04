<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Laracasts\Flash\Flash;

use App\SUtils\SProcess;
use App\SUtils\SUtil;
use App\SUtils\SMenu;

use App\SCore\SInventoryCore;
use App\WMS\SWarehouse;

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

  }
