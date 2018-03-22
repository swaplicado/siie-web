<?php namespace App\SImportations;

use App\ERP\SItemFamily;

/**
 * this class import the data of item families from siie
 */
class SImportFamilies {
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
  public function importFamilies()
  {

      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.FAMILIES'));

      $sql = "SELECT id_ifam, ifam, b_del, ts_new, ts_edit, ts_del FROM itmu_ifam WHERE
                    ts_new > '".$oImportation->last_importation."' OR
                    ts_edit > '".$oImportation->last_importation."' OR
                    ts_del > '".$oImportation->last_importation."'
                    ";
      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieFamilies = array();
      $lWebFamilies = SItemFamily::get();
      $lFamilies = array();
      $lFamiliesToWeb = array();

      foreach ($lWebFamilies as $key => $value) {
          $lFamilies[$value->external_id] = $value;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_ifam"], $lFamilies)) {
                if ($row["ts_edit"] > $lFamilies[$row["id_ifam"]]->updated_at ||
                      $row["ts_del"] > $lFamilies[$row["id_ifam"]]->updated_at) {
                    $lFamilies[$row["id_ifam"]]->name = $row["ifam"];
                    $lFamilies[$row["id_ifam"]]->external_id = $row["id_ifam"];
                    $lFamilies[$row["id_ifam"]]->is_deleted = $row["b_del"];
                    $lFamilies[$row["id_ifam"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lFamiliesToWeb, $lFamilies[$row["id_ifam"]]);
                }
             }
             else {
                array_push($lFamiliesToWeb, SImportFamilies::siieToSiieWeb($row));
             }
         }
      }

       foreach ($lFamiliesToWeb as $key => $family) {
         $family->save();
       }

       SImportUtils::saveImportation($oImportation);

       return sizeof($lFamiliesToWeb);
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
