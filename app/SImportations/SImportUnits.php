<?php namespace App\SImportations;

use App\ERP\SUnit;

/**
 * this class import the data of units from siie
 */
class SImportUnits {
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = 'erp';
  protected $webcon         = '';

  /**
   * receive the name of host to connect
   * can be a IP or name of host
   *
   * @param string $sHost
   */
  function __construct($sHost)
  {
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
  public function importUnits()
  {
      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.UNITS'));

      $sql = "SELECT id_unit, symbol, unit, unit_base_equiv, b_del, ts_new, ts_edit, ts_del FROM itmu_unit WHERE
              ts_new > '".$oImportation->last_importation."' OR
              ts_edit > '".$oImportation->last_importation."' OR
              ts_del > '".$oImportation->last_importation."'
              ";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieUnits = array();
      $lWebUnits = SUnit::get();
      $lUnits = array();
      $lUnitsToWeb = array();

      foreach ($lWebUnits as $key => $value) {
          $lUnits[$value->external_id] = $value;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_unit"], $lUnits)) {
                if ($row["ts_edit"] > $lUnits[$row["id_unit"]]->updated_at ||
                      $row["ts_del"] > $lUnits[$row["id_unit"]]->updated_at) {

                    $lUnits[$row["id_unit"]]->code = $row["symbol"];
                    $lUnits[$row["id_unit"]]->name = $row["unit"];
                    $lUnits[$row["id_unit"]]->external_id = $row["id_unit"];
                    $lUnits[$row["id_unit"]]->base_unit_equivalence_opt = $row["unit_base_equiv"];
                    $lUnits[$row["id_unit"]]->is_deleted = $row["b_del"];
                    $lUnits[$row["id_unit"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lUnitsToWeb, $lUnits[$row["id_unit"]]);
                }
             }
             else {
                array_push($lUnitsToWeb, SImportUnits::siieToSiieWeb($row));
             }
         }
      }

       foreach ($lUnitsToWeb as $key => $unit) {
         $unit->save();
       }

       SImportUtils::saveImportation($oImportation);

       return sizeof($lUnitsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  string $oSiieUnit
   *
   * @return SUnit
   */
  private static function siieToSiieWeb($oSiieUnit = '')
  {
     $oUnit = new SUnit();

     $oUnit->code = $oSiieUnit["symbol"];
     $oUnit->name = $oSiieUnit["unit"];
     $oUnit->external_id = $oSiieUnit["id_unit"];
     $oUnit->base_unit_equivalence_opt = $oSiieUnit["unit_base_equiv"];
     $oUnit->is_deleted = $oSiieUnit["b_del"];
     $oUnit->created_by_id = 1;
     $oUnit->updated_by_id = 1;
     $oUnit->created_at = $oSiieUnit["ts_new"];
     $oUnit->updated_at = $oSiieUnit["ts_edit"];

     return $oUnit;
  }
}
