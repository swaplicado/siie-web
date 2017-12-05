<?php namespace App\SImportations;

use App\ERP\SItem;
use App\ERP\SItemGender;
use App\ERP\SUnit;

/**
 *
 */
class SImportItems
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

  public function importItems()
  {
      $sql = "SELECT id_item, item_key, item,
                      len, surf, vol, mass,
                      b_lot, b_bulk, b_del,
                      fid_unit, fid_igen, ts_new, ts_edit, ts_del FROM itmu_item";

      $result = $this->webcon->query($sql);
      $lSiieItems = array();
      $lWebItems = SItem::get();
      $lWebGenders = SItemGender::get();
      $lWebUnits = SUnit::get();
      $lItems = array();
      $lItemsToWeb = array();

      foreach ($lWebItems as $key => $value)
      {
          $lItems[$value->external_id] = $value;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_item"], $lItems))
             {
                if ($row["ts_edit"] > $lItems[$row["id_item"]]->updated_at ||
                      $row["ts_del"] > $lItems[$row["id_item"]]->updated_at)
                {
                    $lItems[$row["id_item"]]->code = $row["item_key"];
                    $lItems[$row["id_item"]]->name = $row["item"];
                    $lItems[$row["id_item"]]->length = $row["len"];
                    $lItems[$row["id_item"]]->surface = $row["surf"];
                    $lItems[$row["id_item"]]->volume = $row["vol"];
                    $lItems[$row["id_item"]]->mass = $row["mass"];
                    $lItems[$row["id_item"]]->external_id = $row["id_item"];
                    $lItems[$row["id_item"]]->is_deleted = $row["b_del"];
                    $lItems[$row["id_item"]]->unit_id = SImportItems::getUnitId($lWebUnits, $row["fid_unit"]);
                    $lItems[$row["id_item"]]->item_gender_id = SImportItems::getGenderId($lWebGenders, $row["fid_igen"]);
                    $lItems[$row["id_item"]]->updated_at = $row["ts_edit"];

                    array_push($lItemsToWeb, $lItems[$row["id_item"]]);
                }
             }
             else
             {
                array_push($lItemsToWeb, SImportItems::siieToSiieWeb($row, $lWebGenders, $lWebUnits));
             }
         }
      }
      else
      {
         echo "0 results";
      }

      foreach ($lItemsToWeb as $key => $oItem) {
         $oItem->save();
      }

      $this->webcon->close();
  }

  private static function siieToSiieWeb($oSiieItem = '', $lGenders, $lWebUnits)
  {
     $oItem = new SItem();
     $oItem->code = $oSiieItem["item_key"];
     $oItem->name = $oSiieItem["item"];
     $oItem->length = $oSiieItem["len"];
     $oItem->surface = $oSiieItem["surf"];
     $oItem->volume = $oSiieItem["vol"];
     $oItem->mass = $oSiieItem["mass"];
     $oItem->external_id = $oSiieItem["id_item"];
     $oItem->is_deleted = $oSiieItem["b_del"];
     $oItem->unit_id = SImportItems::getUnitId($lWebUnits, $oSiieItem["fid_unit"]);
     $oItem->item_gender_id = SImportItems::getGenderId($lGenders, $oSiieItem["fid_igen"]);
     $oItem->created_by_id = 1;
     $oItem->updated_by_id = 1;
     $oItem->created_at = $oSiieItem["ts_new"];
     $oItem->updated_at = $oSiieItem["ts_edit"];

     return $oItem;
  }

  private static function getGenderId($lGenders, $iExternalId)
  {
     foreach ($lGenders as $key => $oGender) {
       if ($oGender->external_id == $iExternalId)
       {
         return $oGender->id_item_gender;
       }
     }
  }

  private static function getUnitId($lUnits, $iExternalId)
  {
     foreach ($lUnits as $key => $oUnit) {
       if ($oUnit->external_id == $iExternalId)
       {
         return $oUnit->id_unit;
       }
     }
  }
}
