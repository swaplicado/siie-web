<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\WMS\SWhsRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\WMS\SWarehouse;
use App\WMS\SMvtType;
use App\WMS\SWhsType;
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
        $movement = SMovement::find(2);
        $movementRow = new SMovementRow();
        $movementRowLot = new SMovementRowLot();

        $movTypes = SMvtType::where('is_deleted', false)->lists('name', 'id_mvt_type');
        $warehouses = SWarehouse::where('is_deleted', false)->lists('name', 'id_whs');

        $movement->rows;

        // \Debugbar::info($movement);

        return view('wms.movs.whsmovs')
                          ->with('movTypes', $movTypes)
                          ->with('warehouses', $warehouses)
                          ->with('movement', $movement)
                          ->with('movementRow', $movementRow)
                          ->with('movementRowLot', $movementRowLot);
    }

    public function show(Request $request)
    {
        $item = SItem::find($request->parent);
        $item->unit->code;
        // \Debugbar::info($item);

        $arra = array();
        $arra[0] = $item;

        return $arra;
    }
}
