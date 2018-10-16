<?php namespace App\SCore;

use Illuminate\Http\Request;
use App\Http\Controllers\WMS\SStockController;
use Carbon\Carbon;

use App\SCore\SStockManagment;
use App\ERP\SYear;
use App\MMS\SProductionPlan;
use App\MMS\SProductionOrder;
use App\MMS\Formulas\SFormula;
use App\MMS\data\SAuxuliarData;

/**
 *
 */
class SExplosionCore {

  /**
   * determines the necessary ingredients to make the production orders contained
   * on production plan
   *
   * @param  SProductionPlan-or-SProductionOrder  $oProduction
   * @param  integer $iExplosionType  [Type of explosion] \Config::get('scmms.EXPLOSION_BY.ORDER')
*                                                          \Config::get('scmms.EXPLOSION_BY.PLAN')
*                                                          \Config::get('scmms.EXPLOSION_BY.FILE')
   * @param  array[SWarehouse]   $lWarehouses [list of warehouses for explosion]
   * @param  string  $sDate [date of evaluation]
   * @param  boolean $bExplodeSubs [flag, the subformulas will be exploded too]
   *
   * @return array[Query]
   */
  public function explode($oProduction = null, $iExplosionType = 0, $lWarehouses = [], $oDate = '', $bExplodeSubs = false)
  {
     $lIngredients = array();
     $lProdOrders = array();
     $lFormulasToExplode = array();

     switch ($iExplosionType) {
       case \Config::get('scmms.EXPLOSION_BY.ORDER'):
         if ($oProduction instanceof SProductionOrder) {
           $lProdOrders[0] = $oProduction;
         }
         else {
           return ['Error, el objeto recibido no es una orden de producción'];
         }
         break;
       case \Config::get('scmms.EXPLOSION_BY.PLAN'):
         if ($oProduction instanceof SProductionPlan) {
           $lProdOrders = $oProduction->orders;
         }
         else {
           return ['Error, el objeto recibido no es un plan de producción'];
         }
         break;
       case \Config::get('scmms.EXPLOSION_BY.FILE'):
         if (is_array($oProduction) && $oProduction[0] instanceof SAuxuliarData) {
           $lFormulasToExplode = $oProduction;
         }
         else {
           return ['Error, el objeto recibido no es un arreglo de Fórmulas'];
         }
         break;

       default:
         return ['Error, opción desconocida'];
         break;
     }

     if ($iExplosionType != \Config::get('scmms.EXPLOSION_BY.FILE')) {
       foreach ($lProdOrders as $oPO) {
         if ($oPO->is_deleted) {
           continue;
         }

         array_push($lFormulasToExplode, new SAuxuliarData($oPO->formula, $oPO->charges));
       }
     }

     foreach ($lFormulasToExplode as $oData) {
       $lFormulaRows = $this->getRowsFromFormula($oData->value->id_formula);

       $lIngredients = $this->formulaRowsToIngredients($lFormulaRows, $lIngredients, $oData->dQuantity, $bExplodeSubs);
     }

     $lStock = $this->getStockFromWarehouses($lWarehouses, $oDate)->get();

     $aItems = array();
     foreach ($lIngredients as $key => $oRow) {
        array_push($aItems, $oRow->item_id);
     }

     $lBackOrder = $this->getBackOrder($aItems);

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

        $bHasBackOrder = false;
        foreach ($lBackOrder as $oBack) {
            if ($iItem == $oBack->item_id && $iUnit == $oBack->unit_id) {
                $oRow->dBackOrder = $oBack->pending;
                $oRow->sPartner = $oBack->partner_name;
                $bHasBackOrder = true;
            }
        }

        if (! $bHasBackOrder) {
          $oRow->dBackOrder = 0;
          $oRow->sPartner = '?';
        }
     }

     return $lIngredients;
  }

  /**
   * transform the formula row objects to ingredients (format to be showed in explosion)
   *
   * @param  array   $lFormulaRows Result of method of this class (from query)
   * @param  array   $lIngredients list of ingredient objects
   * @param  double $dCharges     charges of production order
   * @param  boolean $bExplodeSubs the sub formules will be exploded or not
   *
   * @return array
   */
  private function formulaRowsToIngredients($lFormulaRows = [], $lIngredients = [],
                                                $dCharges = 0, $bExplodeSubs = false)
  {
      $lRecipes = array();
      $oFormula = null;

      if (sizeof($lFormulaRows) > 0) {
        $oFormula = SFormula::find($lFormulaRows[0]->formula_id);
      }
      else {
         return array();
      }

      foreach ($lFormulaRows as $oRow) {
         if ($oRow->item_recipe_id > 1
              && $bExplodeSubs
                && $oFormula->recipe != $oRow->item_recipe_id
                  && $oRow->item_class_id == \Config::get('scsiie.ITEM_CLS.PRODUCT')
                  ) {
             array_push($lRecipes, new SAuxuliarData($oRow->item_recipe_id, $oRow->quantity));
         }
         else {
             $sKey = $oRow->item_id.'-'.$oRow->unit_id.'-'.$oRow->item_recipe_id;
             if (array_key_exists($sKey, $lIngredients)) {
               $lIngredients[$sKey]->dRequiredQuantity = $lIngredients[$sKey]->dRequiredQuantity + ($oRow->quantity * $dCharges);
             }
             else {
               $oRow->dRequiredQuantity = ($oRow->quantity * $dCharges);
               $lIngredients[$sKey] = $oRow;
             }
         }
      }

      if (sizeof($lRecipes) > 0) {
        $lIngredients = $this->explodeRecipes($lRecipes, $lIngredients, $dCharges);
      }

      return $lIngredients;
  }

  /**
   * explode the subformulas based on recipe
   *
   * @param  array   $aRecipes     array of recipes contained in production order
   * @param  array   $lIngredients list of ingredients
   * @param  double $dCharges     charges on production order
   *
   * @return array  the same received array with the added ingredients of subformulas
   */
  private function explodeRecipes($aRecipes = [], $lIngredients = [], $dCharges = 1)
  {
      foreach ($aRecipes as $oData) {
         $oFormula = SFormula::whereRaw('version = (SELECT MAX(version)
                                                    FROM mms_formulas WHERE
                                                    recipe = '.$oData->value.')')
                              ->where('is_deleted', false)
                              ->where('recipe', $oData->value)
                              ->first();

         $lIngredients = $this->formulaRowsToIngredients($this->getRowsFromFormula($oFormula->id_formula), $lIngredients,
                                                          ($dCharges * $oData->dQuantity), true);
      }

      return $lIngredients;
  }

  /**
   * obtain the rows of formula with a query
   *
   * @param  integer $iFormula [description]
   * @return array[Query]  mfr.quantity,
   *                       mfr.item_id,
   *                       mfr.unit_id,
   *                       mfr.item_recipe_id,
   *                       ei.code AS item_code,
   *                       ei.name AS item,
   *                       eu.code AS unit_code,
   *                       mfr.id_formula_row
   */
  public function getRowsFromFormula($iFormula = 0)
  {
      $sSelect =  "
                  mfr.quantity,
                  mfr.item_id,
                  mfr.unit_id,
                  mfr.item_recipe_id,
                  ei.code AS item_code,
                  ei.name AS item,
                  eu.code AS unit_code,
                  mfr.id_formula_row,
                  mfr.formula_id,
                  eig.item_type_id,
                  eig.item_class_id
                  ";

      $lFormulaRows = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('mms_formula_rows as mfr')
                        ->join('erpu_items as ei', 'mfr.item_id', '=', 'ei.id_item')
                        ->join('erpu_units as eu', 'mfr.unit_id', '=', 'eu.id_unit')
                        ->join('erpu_item_genders as eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                        ->where('formula_id', $iFormula)
                        ->where('mfr.is_deleted', false)
                        ->select(\DB::raw($sSelect))
                        ->orderBy('ei.code', 'ASC')
                        ->get();

      return $lFormulaRows;
  }

  /**
   * get the stock of multiple warehouses
   *
   * @param  array  $lWarehouses array of warehouses objects (SWarehouse)
   * @param  Carbon $oDate  Date Carbon Object
   *
   * @return array[Query]
   */
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

  /**
   * get the back order of Items contained in array received
   *
   * @param  array  $aItems ids of items
   *
   * @return array[Query] (with get())
   */
  private function getBackOrder($aItems = [])
  {
     $lSupplies = SStockManagment::getBaseQuery(\Config::get('scsiie.DOC_CAT.PURCHASES'),
                                                \Config::get('scsiie.DOC_CLS.ORDER'),
                                                \Config::get('scsiie.DOC_TYPE.ORDER'));

     $lSupplies =  $lSupplies->select('ed.id_document',
                           'ed.dt_date',
                           'ed.dt_doc',
                           \DB::raw('CONCAT(ed.service_num, IF(ed.service_num = "", "", "-"), ed.num) as folio'),
                           \DB::raw('CONCAT(edsrc.service_num, IF(edsrc.service_num = "", "", "-"), edsrc.num) as num_src'),
                           'ed.is_closed',
                           'ed.doc_src_id',
                           'ed.doc_sys_status_id',
                           'ed.external_id',
                           'ed.partner_id',
                           'ed.is_deleted',
                           'ep.name AS partner_name',
                           'ep.fiscal_id',
                           'ep.code as cve_an',
                           'edr.item_id',
                           'edr.unit_id',
                           \DB::raw("(SELECT COUNT(*) supp_inv
                                                   FROM
                                                   wms_mvts WHERE doc_invoice_id IN
                                                   (SELECT id_document
                                                   FROM erpu_documents
                                                   WHERE doc_src_id = ed.id_document)
                                                   AND NOT is_deleted) AS supp_inv"),
                           \DB::raw('COALESCE(SUM(wisl.quantity), 0) AS qty_sur_ind'),
                           \DB::raw('(SUM(edr.quantity) - ( COALESCE(SUM(
                                       IF (wm.is_deleted IS NULL
                                       OR (wm.is_deleted IS NOT NULL
                                       AND wm.is_deleted = FALSE
                                       AND wmr.is_deleted = FALSE),
                                       wmr.quantity, 0)), 0) + COALESCE(SUM(wisl.quantity), 0)))  AS pending')
                   )
                   ->where('ed.is_closed', false)
                   ->where('ed.is_deleted', false)
                   ->where('edr.is_deleted', false)
                   ->whereIn('edr.item_id', $aItems)
                   ->groupBy('edr.item_id')
                   ->groupBy('edr.unit_id')
                   ->having('pending', '>', 0)
                   ->get()
                  ;

    return $lSupplies;
  }

  public function getFormulasFromArray($lCsv = [])
  {
      $lToExplode = array();

      foreach ($lCsv as $csvRow) {
        $oFormula = $this->getFormulaByItemCode($csvRow->sItemKey);
        if ($oFormula == null) {
          return ['ERROR. No hay fórmula para: '.$csvRow->sItemKey];
        }
        array_push($lToExplode, new SAuxuliarData($oFormula, $csvRow->dQuantity));
      }

      return $lToExplode;
  }

  private function getFormulaByItemCode($sItemCode = '')
  {
      $oFormula = SFormula::join('erpu_items as ei', 'item_id', '=', 'ei.id_item')
                ->where('ei.code', trim($sItemCode))
                ->orderBy('mms_formulas.updated_at', 'DESC')
                ->take(1)
                ->get();

      return sizeof($oFormula) > 0 ? $oFormula[0] : null;
  }

}
