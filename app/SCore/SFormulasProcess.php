<?php namespace App\SCore;

use App\MMS\Formulas\SFormula;

/**
 *
 */
class SFormulasProcess {

  public static function validateRecursion($oFormula = null, $lFormulas = [], $lFormulaRows = [])
  {
     $lErrors = [];
     if (sizeof($lFormulaRows) > 0) {
        foreach ($lFormulaRows as $oFormRow) {
           if ($oFormRow->item_id == $oFormula->item_id && $oFormRow->unit_id == $oFormula->unit_id) {
              array_push($lErrors, trans('messages.FORMULA_RECURSION_ERROR').$oFormRow->item->name);
              return $lErrors;
           }
        }
     }
     else {
       $lFormToAnalize = clone $lFormulas;
       array_push($lFormToAnalize, $oFormula);
       foreach ($oFormula->rows as $oFormulaRow) {
          foreach ($lFormToAnalize as $oFormAn) {
            if ($oFormulaRow->item_id == $oFormAn->item_id && $oFormulaRow->unit_id == $oFormAn->unit_id) {
               array_push($lErrors, trans('messages.FORMULA_RECURSION_ERROR').$oFormula->identifier);
               return $lErrors;
            }
          }
          if ($oFormulaRow->item_recipe_id > 1) {
              $FormulaToAdd = SFormula::where('recipe', $oFormulaRow->item_recipe_id)
                      ->whereRaw('version = (select max(version) from mms_formulas where recipe = '.$oFormulaRow->item_recipe_id.')')
                      ->first();

              $oResult = SFormulasProcess::validateRecursion($FormulaToAdd, $lFormToAnalize);
              if (is_array($oResult) && sizeof($oResult) > 0) {
                 array_push($lErrors, $oResult);
                 return $lErrors;
              }
          }
       }
     }
  }
}
