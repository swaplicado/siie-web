<?php namespace App\SImportations;

use App\ERP\SItemGroup;
use App\ERP\SItemFamily;

/**
 * this class import the data of item groups from siie
 */
class SImportGroups {
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
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  /**
   * read the data  from siie, transform it, and saves it in the database
   *
   * @return integer number of records imported
   */
  public function importGroups()
  {
      $sql = "SELECT id_igrp, igrp, b_del, fid_ifam, ts_new, ts_edit, ts_del FROM itmu_igrp";
      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieGroups = array();
      $lWebGroups = SItemGroup::get();
      $lWebFamilies = SItemFamily::get();
      $lGroups = array();
      $lGroupsToWeb = array();

      foreach ($lWebGroups as $key => $value) {
          $lGroups[$value->external_id] = $value;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_igrp"], $lGroups)) {
                if ($row["ts_edit"] > $lGroups[$row["id_igrp"]]->updated_at ||
                      $row["ts_del"] > $lGroups[$row["id_igrp"]]->updated_at) {
                    $lGroups[$row["id_igrp"]]->name = $row["id_igrp"];
                    $lGroups[$row["id_igrp"]]->external_id = $row["id_igrp"];
                    $lGroups[$row["id_igrp"]]->is_deleted = $row["b_del"];
                    $lGroups[$row["id_igrp"]]->item_family_id = SImportGroups::getFamilyId($lWebFamilies, $row["fid_ifam"]);
                    $lGroups[$row["id_igrp"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lGroupsToWeb, $lGroups[$row["id_igrp"]]);
                }
             }
             else {
                array_push($lGroupsToWeb, SImportGroups::siieToSiieWeb($row, $lWebFamilies));
             }
         }
      }

      foreach ($lGroupsToWeb as $key => $oGroup) {
         $oGroup->save();
      }

      return sizeof($lGroupsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieGroup
   * @param  array  $lFamilies array of item families to map siie to siie-web families
   *
   * @return SItemGroup
   */
  private static function siieToSiieWeb($oSiieGroup = '', $lFamilies = [])
  {
     $oGroup = new SItemGroup();
     $oGroup->name = $oSiieGroup["igrp"];
     $oGroup->external_id = $oSiieGroup["id_igrp"];
     $oGroup->is_deleted = $oSiieGroup["b_del"];
     $oGroup->item_family_id = SImportGroups::getFamilyId($lFamilies, $oSiieGroup["fid_ifam"]);
     $oGroup->created_by_id = 1;
     $oGroup->updated_by_id = 1;
     $oGroup->created_at = $oSiieGroup["ts_new"];
     $oGroup->updated_at = $oSiieGroup["ts_edit"];

     return $oGroup;
  }

  /**
   * get the family id based on the external id
   *
   * @param  array   $lFamilies
   * @param  integer $iExternalId
   *
   * @return integer corresponding item family id
   */
  private static function getFamilyId($lFamilies = [], $iExternalId = 0)
  {
     foreach ($lFamilies as $key => $oFamily) {
       if ($oFamily->external_id == $iExternalId) {
         return $oFamily->id_item_family;
       }
     }
  }
}
