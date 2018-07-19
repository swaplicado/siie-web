<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SMenu;
use App\Database\Config;
use App\SUtils\SUtil;
use App\SCore\SExplosionCore;
use Carbon\Carbon;

use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\SUtils\SProcess;

use App\MMS\SProductionPlan;
use App\WMS\SWarehouse;

class SExplosionMaterialsController extends Controller {

    private $oCurrentUserPermission;
    private $iFilter;
    private $sClassNav;

    public function __construct()
    {
         $this->oCurrentUserPermission = SProcess::constructor($this,
         \Config::get('scperm.PERMISSION.MMS_EXPLOSION_MATERIALS'),
         \Config::get('scsys.MODULES.MMS'));

         $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    public function index(Request $request)
    {
      $lPlanes = SProductionPlan::where('is_deleted', false)
                                 ->select('id_production_plan', \DB::raw("CONCAT(folio, '-', production_plan) as plan"))
                                  ->lists('plan', 'id_production_plan');

      $lWarehouses = SWarehouse::where('is_deleted', false)
                                 ->select('id_whs', \DB::raw("CONCAT(code, '-', name) as whs"))
                                 ->where('branch_id', session('branch')->id_branch)
                                 ->lists('whs', 'id_whs');

      $sTitle = trans('mms.EXPLOSION_MATERIALS');

       return view('mms.explosion.explode')
           ->with('lWarehouses', $lWarehouses)
           ->with('lPlanes', $lPlanes)
           ->with('sTitle', $sTitle);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
       $sTitle = trans('mms.EXPLOSION_MATERIALS');

       $sDate = $request->dt_date;
       $iProductionPlan = $request->production_plan;
       $aWarehouses = json_decode($request->warehouses_array);
       $bExplodeSubs = $request->explode_sub;

       $oProductionPlan = SProductionPlan::find($iProductionPlan);
       $lWarehouses = array();
       $sWarehouses = '';

       foreach ($aWarehouses as $iWhs) {
          $oWhs = SWarehouse::find($iWhs);
          $sWarehouses .= $oWhs->code.'-'.$oWhs->name.';';
          array_push($lWarehouses, $oWhs);
       }

       $oDate = Carbon::parse($sDate);

       $oExplosionCore = new SExplosionCore();
       $lExplosion = $oExplosionCore->explode($oProductionPlan, $lWarehouses, $oDate, $bExplodeSubs);
       $lStock = $oExplosionCore->getStockFromWarehouses($lWarehouses, $oDate)
                                                       ->groupBy('ws.lot_id')
                                                       ->groupBy('ws.pallet_id')
                                                       ->groupBy('ws.whs_id')
                                                       ->get();

       $lOrders = $oProductionPlan->orders;

       return view('mms.explosion.explosionmaterials')
              ->with('lExplosion', $lExplosion)
              ->with('oProductionPlan', $oProductionPlan)
              ->with('lOrders', $lOrders)
              ->with('sDate', $sDate)
              ->with('lWarehouses', $lWarehouses)
              ->with('sWarehouses', $sWarehouses)
              ->with('lStock', $lStock)
              ->with('sTitle', $sTitle);
    }

}
