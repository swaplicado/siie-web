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

    public function getStock(Request $request)
    {
       $lStock = array();
       $oInvStk = new SInventoryCore();

       $lStock = $oInvStk->getWarehouseStock($request->iWhs);

       return json_encode($lStock);
    }

  }
