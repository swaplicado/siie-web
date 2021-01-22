<?php namespace App\SImportations;

use App\MMS\Formulas\SFormula;
use App\ERP\SItem;
use App\ERP\SUnit;

/**
 * this class import the data of item families from siie
 */
class SImportFormulas {
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
  public function importFormulas()
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
                  WHERE lev = 0
                  AND fid_item_n IS NULL
                  AND
                    (ts_new > '".$oImportation->last_importation."' OR
                    ts_edit > '".$oImportation->last_importation."' OR
                    ts_del > '".$oImportation->last_importation."')
                  ORDER BY id_bom ASC";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lWebFormulas = SFormula::orderBy('external_id', 'ASC')->get();
      $lFormulas = array();
      $lFormulasToWeb = array();
      $lItems = SItem::lists('id_item', 'external_id');
      $lUnits = SUnit::lists('id_unit', 'external_id');

      foreach ($lWebFormulas as $key => $value) {
          $lFormulas[$value->external_id] = $value;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
           if (! array_key_exists($row["id_bom"], $lFormulas)) {
              array_push($lFormulasToWeb, SImportFormulas::siieToSiieWeb($row, $lItems, $lUnits));
            }
         }
      }

       foreach ($lFormulasToWeb as $formula) {
         $formula->recipe = SFormula::max('recipe') + 1;
         $formula->save();
       }

      //  SImportUtils::saveImportation($oImportation);

       return sizeof($lFormulasToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieFormula
   * @return SFormula
   */
  private static function siieToSiieWeb($oSiieFormula = '', $lWebItems = [], $lWebUnits = [])
  {
     $oFormula = new SFormula();

     $oFormula->version = 1;
     $oFormula->identifier = $oSiieFormula["bom"];
     $oFormula->dt_date = $oSiieFormula["ts_start"];
     $oFormula->notes = '';
     $oFormula->quantity = $oSiieFormula["qty"] <= '0' ? 1 : $oSiieFormula["qty"];
     $oFormula->is_deleted = $oSiieFormula["b_del"];
     $oFormula->external_id = $oSiieFormula["id_bom"];
     $oFormula->item_id = $lWebItems[$oSiieFormula["fid_item"]];
     $oFormula->unit_id = $lWebUnits[$oSiieFormula["fid_unit"]];
     $oFormula->created_by_id = 1;
     $oFormula->updated_by_id = 1;
     $oFormula->created_at = $oSiieFormula["ts_new"];
     $oFormula->updated_at = $oSiieFormula["ts_edit"];

     return $oFormula;
  }
}
