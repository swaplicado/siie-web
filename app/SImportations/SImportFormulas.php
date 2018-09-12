<?php namespace App\SImportations;

use App\MMS\Formulas\SFormula;

/**
 * this class import the data of item families from siie
 */
class SImportFormulas {
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
  public function importFormulas()
  {
      $sql = "SELECT id_ifam, ifam, b_del, ts_new, ts_edit, ts_del FROM itmu_ifam WHERE
                    ts_new > '".$oImportation->last_importation."' OR
                    ts_edit > '".$oImportation->last_importation."' OR
                    ts_del > '".$oImportation->last_importation."'
                    ";
      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieFormulas = array();
      $lWebFormulas = SItemFamily::get();
      $lFormulas = array();
      $lFormulasToWeb = array();

      foreach ($lWebFormulas as $key => $value) {
          $lFormulas[$value->external_id] = $value;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_ifam"], $lFormulas)) {
                if ($row["ts_edit"] > $lFormulas[$row["id_ifam"]]->updated_at ||
                      $row["ts_del"] > $lFormulas[$row["id_ifam"]]->updated_at) {
                    $lFormulas[$row["id_ifam"]]->name = $row["ifam"];
                    $lFormulas[$row["id_ifam"]]->external_id = $row["id_ifam"];
                    $lFormulas[$row["id_ifam"]]->is_deleted = $row["b_del"];
                    $lFormulas[$row["id_ifam"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lFormulasToWeb, $lFormulas[$row["id_ifam"]]);
                }
             }
             else {
                array_push($lFormulasToWeb, SImportFormulas::siieToSiieWeb($row));
             }
         }
      }

       foreach ($lFormulasToWeb as $key => $family) {
         $family->save();
       }

       return sizeof($lFormulasToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieFamily
   * @return SItemFamily
   */
  private static function siieToSiieWeb($oSiieFamily = '')
  {
     $oFamily = new SItemFamily();
     $oFamily->name = $oSiieFamily["ifam"];
     $oFamily->external_id = $oSiieFamily["id_ifam"];
     $oFamily->is_deleted = $oSiieFamily["b_del"];
     $oFamily->created_by_id = 1;
     $oFamily->updated_by_id = 1;
     $oFamily->created_at = $oSiieFamily["ts_new"];
     $oFamily->updated_at = $oSiieFamily["ts_edit"];

     return $oFamily;
  }
}
