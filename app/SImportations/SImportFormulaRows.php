<?php namespace App\SImportations;

use App\MMS\Formulas\SFormula;
use App\MMS\Formulas\SFormulaRow;
use App\ERP\SItem;
use App\ERP\SUnit;

/**
 * this class import the data of item families from siie
 */
class SImportFormulaRows {
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = 'erp_sc';
  protected $webcon         = '';

  /**
   * receive the name of host to connect
   * can be a IP or name of host
   *
   * @param string $sHost
   */
  function __construct($sHost, $sDbName)
    {
      $this->webdbname = $sDbName;
      $this->webcon = mysqli_connect($sHost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno()) {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  /**
   * read the data  from siie, transform it, and saves it in the database
   *
   * @return integer number of records imported
   */
  public function importFormulaRows()
  {
      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.FORMULAS'));

      $sql = "SELECT id_bom,
                  ts_start,
                  bom,
                  qty,
                  lev,
                  b_del,
                  fid_item,
                  fid_unit,
                  fid_item_n,
                  ts_new,
                  ts_edit,
                  ts_del
                  FROM mfg_bom
                  WHERE
                  fid_item_n IS NOT NULL
                  AND lev > 0
                  AND
                    (ts_new > '".$oImportation->last_importation."' OR
                    ts_edit > '".$oImportation->last_importation."' OR
                    ts_del > '".$oImportation->last_importation."')
                  ORDER BY id_bom ASC";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lFormulaRowsToWeb = array();
      
      
      if (is_object($result) && $result->num_rows > 0) {
        $lWebFormulaRows = SFormulaRow::orderBy('external_id', 'ASC')->get();
        $lWebFormulas = SFormula::orderBy('external_id', 'ASC')->get();
        $lWebItems = SItem::get();
        $lWebUnits = SUnit::get();
  
        $lFormulasByItem = array();
        $lUnits = array();
        $lItems = array();
  
        foreach ($lWebItems as $value) {
            $lItems[$value->external_id] = $value;
        }
  
        foreach ($lWebUnits as $value) {
            $lUnits[$value->external_id] = $value;
        }
  
        foreach ($lWebFormulaRows as $value) {
            $lFormulaRows[$value->external_id] = $value;
        }
  
        foreach ($lWebFormulas as $key => $value) {
            $lFormulasByItem[$value->item_id] = $value;
        }

        // output data of each row
        while($row = $result->fetch_assoc()) {
          if (! array_key_exists($row["id_bom"], $lFormulaRows)) {
            array_push($lFormulaRowsToWeb, SImportFormulaRows::siieToSiieWeb($row, $lFormulasByItem, $lItems, $lUnits));
          }
        }
      }

      foreach ($lFormulaRowsToWeb as $formulaRow) {
        $formulaRow->save();
      }

      SImportUtils::saveImportation($oImportation);

      return sizeof($lFormulaRowsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieFormula
   * @return SFormula
   */
  private static function siieToSiieWeb($oSiieFormulaR = '', $lFormulasByItem = [], $lWebItems = [], $lWebUnits = [])
  {
     $oFormulaRow = new SFormulaRow();

     $oFormulaRow->quantity = $oSiieFormulaR["qty"];
     $oFormulaRow->mass = 0;
     $oFormulaRow->is_deleted = $oSiieFormulaR["b_del"];
     $oFormulaRow->external_id = $oSiieFormulaR["id_bom"];
     $oFormulaRow->formula_id = $lFormulasByItem[$lWebItems[$oSiieFormulaR["fid_item_n"]]->id_item]->id_formula;
     $oFormulaRow->item_id = $lWebItems[$oSiieFormulaR["fid_item"]]->id_item;
     $oFormulaRow->unit_id = $lWebUnits[$oSiieFormulaR["fid_unit"]]->id_unit;
     $oFormulaRow->item_recipe_id = SImportFormulaRows::getRecipeId($oSiieFormulaR["fid_item"], $oSiieFormulaR["fid_unit"], $lWebItems, $lFormulasByItem);
     $oFormulaRow->created_by_id = 1;
     $oFormulaRow->updated_by_id = 1;
     $oFormulaRow->created_at = $oSiieFormulaR["ts_new"];
     $oFormulaRow->updated_at = $oSiieFormulaR["ts_edit"];

     return $oFormulaRow;
  }

  private static function getRecipeId($iItem = 0, $iUnit = 0, $lWebItems = [], $lFormulasByItem = [])
  {
      if (array_key_exists($lWebItems[$iItem]->id_item, $lFormulasByItem)) {
        return $lFormulasByItem[$lWebItems[$iItem]->id_item]->recipe;
      }
      else {
        return 1;
      }
  }
}
