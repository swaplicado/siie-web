<?php namespace App\SImportations;

use App\ERP\SItem;
use App\ERP\SItemGender;
use App\ERP\SUnit;

/**
 * this class import the data of items from siie
 */
class SImportItems
{
   protected $webusername;
   protected $webpassword;
   protected $webdbname;
   protected $webcon;
 
   /**
    * receive the name of host to connect
    * can be a IP or name of host
    *
    * @param string $sHost
    */
     function __construct($sHost)
     {
         $this->webusername = env("SIIE_DB_USER", "");
         $this->webpassword = env("SIIE_DB_PASS", "");
         $this->webdbname = env("SIIE_DB_NAME", "");
         
         $this->webcon = mysqli_connect(
             $sHost, $this->webusername,
             $this->webpassword, $this->webdbname
         );
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
  public function importItems()
  {
      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.ITEMS'));

      $sql = "SELECT id_item, item_key, item, item_short,
                      len, surf, vol, mass,
                      b_lot, b_bulk, b_del,
                      fid_unit, fid_igen, ts_new, ts_edit, ts_del FROM itmu_item
                      WHERE
                      ts_new > '".$oImportation->last_importation."' OR
                      ts_edit > '".$oImportation->last_importation."' OR
                      ts_del > '".$oImportation->last_importation."'
                      ";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieItems = array();
      $lWebItems = SItem::get();
      $lWebGenders = SItemGender::get();
      $lWebUnits = SUnit::get();
      $lItems = array();
      $lItemsToWeb = array();

      foreach ($lWebItems as $key => $value) {
          $lItems[$value->external_id] = $value;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_item"], $lItems)) {
                if ($row["ts_edit"] > $oImportation->last_importation ||
                      $row["ts_del"] > $oImportation->last_importation) {
                    $lItems[$row["id_item"]]->code = $row["item_key"];
                    $lItems[$row["id_item"]]->name = $row["item"];
                    $lItems[$row["id_item"]]->short_name = $row["item_short"];
                    $lItems[$row["id_item"]]->length = $row["len"];
                    $lItems[$row["id_item"]]->surface = $row["surf"];
                    $lItems[$row["id_item"]]->volume = $row["vol"];
                    $lItems[$row["id_item"]]->mass = $row["mass"];
                    $lItems[$row["id_item"]]->external_id = $row["id_item"];
                    $lItems[$row["id_item"]]->is_lot = $row["b_lot"];
                    $lItems[$row["id_item"]]->is_bulk = $row["b_bulk"];
                    $lItems[$row["id_item"]]->is_deleted = $row["b_del"];
                    $lItems[$row["id_item"]]->unit_id = SImportItems::getUnitId($lWebUnits, $row["fid_unit"]);
                    $lItems[$row["id_item"]]->item_gender_id = SImportItems::getGenderId($lWebGenders, $row["fid_igen"]);
                    $lItems[$row["id_item"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lItemsToWeb, $lItems[$row["id_item"]]);
                }
             }
             else {
                array_push($lItemsToWeb, SImportItems::siieToSiieWeb($row, $lWebGenders, $lWebUnits));
             }
         }
      }

      foreach ($lItemsToWeb as $key => $oItem) {
         $oItem->save();
      }

      SImportUtils::saveImportation($oImportation);

      return sizeof($lItemsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieItem
   * @param  array  $lGenders array of item genders to map siie to siie-web genders
   * @param  array  $lWebUnits array of item units to map siie to siie-web units
   *
   * @return SItem
   */
  private static function siieToSiieWeb($oSiieItem = '', $lGenders = [], $lWebUnits = [])
  {
     $oItem = new SItem();
     $oItem->code = $oSiieItem["item_key"];
     $oItem->name = $oSiieItem["item"];
     $oItem->short_name = $oSiieItem["item_short"];
     $oItem->length = $oSiieItem["len"];
     $oItem->surface = $oSiieItem["surf"];
     $oItem->volume = $oSiieItem["vol"];
     $oItem->mass = $oSiieItem["mass"];
     $oItem->external_id = $oSiieItem["id_item"];
     $oItem->is_lot = $oSiieItem["b_lot"];
     $oItem->is_bulk = $oSiieItem["b_bulk"];
     $oItem->is_deleted = $oSiieItem["b_del"];
     $oItem->unit_id = SImportItems::getUnitId($lWebUnits, $oSiieItem["fid_unit"]);
     $oItem->item_gender_id = SImportItems::getGenderId($lGenders, $oSiieItem["fid_igen"]);
     $oItem->created_by_id = 1;
     $oItem->updated_by_id = 1;
     $oItem->created_at = $oSiieItem["ts_new"];
     $oItem->updated_at = $oSiieItem["ts_edit"];

     return $oItem;
  }

  /**
   * get the id of gender based on the external id, if the is isn't found
   * return 1
   *
   * @param  array   $lGenders array of existing genders in the system
   * @param  integer $iExternalId id of gender in siie
   *
   * @return integer id of gender in siie-web
   */
  private static function getGenderId($lGenders = [], $iExternalId = 0)
  {
     foreach ($lGenders as $key => $oGender) {
       if ($oGender->external_id == $iExternalId)
       {
         return $oGender->id_item_gender;
       }
     }

     return 1;
  }

  /**
   * get the id of gender based on the external id, if the is isn't found
   * return 1
   *
   * @param  array $lUnits array of existing units in the system
   * @param  integer $iExternalId id of gender in siie
   *
   * @return integer id of gender in siie-web
   */
  private static function getUnitId($lUnits, $iExternalId)
  {
     foreach ($lUnits as $key => $oUnit) {
       if ($oUnit->external_id == $iExternalId) {
         return $oUnit->id_unit;
       }
     }
  }
}
