<?php namespace App\SImportations;

use App\ERP\SItemGroup;
use App\ERP\SItemFamily;

/**
 *
 */
class SImportGroups
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

  public function importGroups()
  {
      $sql = "SELECT id_igrp, igrp, b_del, fid_ifam, ts_new, ts_edit, ts_del FROM itmu_igrp";
      $result = $this->webcon->query($sql);
      $lSiieGroups = array();
      $lWebGroups = SItemGroup::get();
      $lWebFamilies = SItemFamily::get();
      $lGroups = array();
      $lGroupsToWeb = array();

      foreach ($lWebGroups as $key => $value)
      {
          $lGroups[$value->external_id] = $value;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_igrp"], $lGroups))
             {
                if ($row["ts_edit"] > $lGroups[$row["id_igrp"]]->updated_at ||
                      $row["ts_del"] > $lGroups[$row["id_igrp"]]->updated_at)
                {
                    $lGroups[$row["id_igrp"]]->name = $row["id_igrp"];
                    $lGroups[$row["id_igrp"]]->external_id = $row["id_igrp"];
                    $lGroups[$row["id_igrp"]]->is_deleted = $row["b_del"];
                    $lGroups[$row["id_igrp"]]->item_family_id = SImportGroups::getFamilyId($lWebFamilies, $row["fid_ifam"]);
                    $lGroups[$row["id_igrp"]]->updated_at = $row["ts_edit"];

                    array_push($lGroupsToWeb, $lGroups[$row["id_igrp"]]);
                }
             }
             else
             {
                array_push($lGroupsToWeb, SImportGroups::siieToSiieWeb($row, $lWebFamilies));
             }
         }
      }
      else
      {
         echo "0 results";
      }

      foreach ($lGroupsToWeb as $key => $oGroup) {
         $oGroup->save();
      }

      $this->webcon->close();

      return true;
  }

  private static function siieToSiieWeb($oSiieGroup = '', $lFamilies)
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

  private static function getFamilyId($lFamilies, $iExternalId)
  {
     foreach ($lFamilies as $key => $oFamily) {
       if ($oFamily->external_id == $iExternalId)
       {
         return $oFamily->id_item_family;
       }
     }
  }
}
