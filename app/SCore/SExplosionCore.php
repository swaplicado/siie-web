<?php namespace App\SCore;

use Illuminate\Http\Request;
use App\Http\Controllers\WMS\SStockController;
use Carbon\Carbon;

use App\SCore\SStockManagment;
use App\ERP\SYear;

/**
 *
 */
class SExplosionCore {

  /**
   * [explode description]
   *
   * @param  SProductionPlan  $oProductionPlan
   * @param  array[SWarehouse]   $lWarehouses [list of warehouses for explosion]
   * @param  string  $sDate [date of cevaluation]
   * @param  boolean $bExplodeSubs [flag, the subformulas will be exploded too]
   *
   * @return array[Query]
   */
  public function explode($oProductionPlan = null, $lWarehouses = [], $oDate = '', $bExplodeSubs = false)
  {
     $lIngredients = array();
     $lProdOrders = $oProductionPlan->orders;

     $sSelect =  "
                 mfr.quantity,
                 mfr.item_id,
                 mfr.unit_id,
                 mfr.item_recipe_id,
                 ei.code AS item_code,
                 ei.name AS item,
                 eu.code AS unit_code,
                 mfr.id_formula_row
                 ";

     foreach ($lProdOrders as $oPO) {
        $oFormula = $oPO->formula;

        $lFormulaRows = \DB::connection(session('db_configuration')->getConnCompany())
                          ->table('mms_formula_rows as mfr')
                          ->join('erpu_items as ei', 'mfr.item_id', '=', 'ei.id_item')
                          ->join('erpu_units as eu', 'mfr.unit_id', '=', 'eu.id_unit')
                          ->where('formula_id', $oFormula->id_formula)
                          ->where('mfr.is_deleted', false)
                          ->select(\DB::raw($sSelect))
                          ->get();

        foreach ($lFormulaRows as $oRow) {
           $sKey = $oRow->item_id.'-'.$oRow->unit_id.'-'.$oRow->item_recipe_id;

           if (in_array($sKey, $lIngredients)) {
             $lIngredients[$sKey]->dRequiredQuantity += ($oRow->quantity * $oPO->charges);
           }
           else {
             $oRow->dRequiredQuantity = ($oRow->quantity * $oPO->charges);
             $lIngredients[$sKey] = $oRow;
           }
        }
     }

     $lStock = $this->getStockFromWarehouses($lWarehouses, $oDate)->get();

     foreach ($lIngredients as $key => $oRow) {
        $aKey = explode("-", $key);
        $iItem = $aKey[0];
        $iUnit = $aKey[1];

        $bHasStock = false;
        foreach ($lStock as $oStock) {
           if ($iItem == $oStock->item_id && $iUnit == $oStock->unit_id) {
             $oRow->dStock = $oStock->stock;
             $oRow->dSegregated = $oStock->segregated;
             $bHasStock = true;
             break;
           }
        }

        if (! $bHasStock) {
          $oRow->dStock = 0;
          $oRow->dSegregated = 0;
        }
     }

     return $lIngredients;
  }


  public function getStockFromWarehouses($lWarehouses = [], $oDate = null)
  {
     $aWhss = array();
     foreach ($lWarehouses as $oWhs) {
       array_push($aWhss, $oWhs->id_whs);
     }

     $iYear = $oDate->year;
     $oYear = SYear::where('year', $iYear)
                     ->where('is_deleted', false)
                     ->first();

     $sSelect = 'ws.item_id,
                  ei.code AS item_code,
                  ei.name AS item,
                  ws.unit_id,
                  eu.code AS unit_code,
                  ws.lot_id,
                  wl.lot,
                  wl.dt_expiry,
                  ws.pallet_id,
                  ws.location_id,
                  wwl.code AS location_code,
                  wwl.name AS location,
                  ws.whs_id,
                  ww.code AS whs_code,
                  ww.name AS whs,
                  sum(ws.input) as inputs,
                  sum(ws.output) as outputs,
                  (sum(ws.input) - sum(ws.output)) as stock
                  ';

     $aStkParams = array();
     $aStkParams[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
     $aStkParams[\Config::get('scwms.STOCK_PARAMS.WHS')] = $aWhss;
     $aStkParams[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = session('branch')->id_branch;
     $aStkParams[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = $oYear->id_year;
     $aStkParams[\Config::get('scwms.STOCK_PARAMS.DATE')] = $oDate->format('Y-m-d');

     $aSegParams = array();
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 'ws.item_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 'ws.unit_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'ws.lot_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'ws.pallet_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ws.whs_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = 'ws.branch_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = 'ws.year_id';
     $aSegParams[\Config::get('scwms.STOCK_PARAMS.DATE')] = $oDate->format('Y-m-d');

     $lStock = SStockManagment::getStockResult($aStkParams, $aSegParams);

     $lStock = $lStock->groupBy('ws.item_id')
                      ->groupBy('ws.unit_id');

     return $lStock;
  }

  public function FunctionName($value='')
  {
     $lSupplies = SStockManagment::getBaseQuery(\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                \Config::get('scsiie.DOC_CLS.ORDER'),
                                                \Config::get('scsiie.DOC_TYPE.ORDER'));
  }

}
