<?php namespace App\SImportations;

use App\ERP\SUnit;

/**
 *
 */
class SImportUnits
{
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = 'erp';
  protected $webcon         = '';

  function __construct($sHost)
  {
      $this->webcon = mysqli_connect($sHost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  public function importUnits()
  {
      $sql = "SELECT id_unit, symbol, unit, unit_base_equiv, b_del, ts_new, ts_edit, ts_del FROM itmu_unit";
      $result = $this->webcon->query($sql);
      $lSiieUnits = array();
      $lWebUnits = SUnit::get();
      $lUnits = array();
      $lUnitsToWeb = array();

      foreach ($lWebUnits as $key => $value)
      {
          $lUnits[$value->external_id] = $value;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_unit"], $lUnits))
             {
                if ($row["ts_edit"] > $lUnits[$row["id_unit"]]->updated_at ||
                      $row["ts_del"] > $lUnits[$row["id_unit"]]->updated_at)
                {
                    $lUnits[$row["id_unit"]]->code = $row["symbol"];
                    $lUnits[$row["id_unit"]]->name = $row["unit"];
                    $lUnits[$row["id_unit"]]->external_id = $row["id_unit"];
                    $lUnits[$row["id_unit"]]->base_unit_equivalence_opt = $row["unit_base_equiv"];
                    $lUnits[$row["id_unit"]]->is_deleted = $row["b_del"];
                    $lUnits[$row["id_unit"]]->updated_at = $row["ts_edit"];

                    array_push($lUnitsToWeb, $lUnits[$row["id_unit"]]);
                }
             }
             else
             {
                array_push($lUnitsToWeb, SImportUnits::siieToSiieWeb($row));
             }
         }
      }
      else
      {
         echo "0 results";
      }
       $this->webcon->close();

       foreach ($lUnitsToWeb as $key => $unit) {
         $unit->save();
       }

       return true;
  }

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
