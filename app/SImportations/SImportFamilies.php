<?php namespace App\SImportations;

use App\ERP\SItemFamily;

/**
 *
 */
class SImportFamilies
{
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = 'erp';
  protected $webcon         = '';

  function __construct()
  {
      $this->webcon = mysqli_connect($this->webhost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  public function importFamilies()
  {
      $sql = "SELECT id_ifam, ifam, b_del, ts_new, ts_edit, ts_del FROM itmu_ifam";
      $result = $this->webcon->query($sql);
      $lSiieFamilies = array();
      $lWebFamilies = SItemFamily::get();
      $lFamilies = array();
      $lFamiliesToWeb = array();

      foreach ($lWebFamilies as $key => $value)
      {
          $lFamilies[$value->external_id] = $value;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_ifam"], $lFamilies))
             {
                if ($row["ts_edit"] > $lFamilies[$row["id_ifam"]]->updated_at ||
                      $row["ts_del"] > $lFamilies[$row["id_ifam"]]->updated_at)
                {
                    $lFamilies[$row["id_ifam"]]->name = $row["ifam"];
                    $lFamilies[$row["id_ifam"]]->external_id = $row["id_ifam"];
                    $lFamilies[$row["id_ifam"]]->is_deleted = $row["b_del"];
                    $lFamilies[$row["id_ifam"]]->updated_at = $row["ts_edit"];

                    array_push($lFamiliesToWeb, $lFamilies[$row["id_ifam"]]);
                }
             }
             else
             {
                array_push($lFamiliesToWeb, SImportFamilies::siieToSiieWeb($row));
             }
         }
      }
      else
      {
         echo "0 results";
      }
       $this->webcon->close();

       foreach ($lFamiliesToWeb as $key => $family) {
         $family->save();
       }

       return true;
  }

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
