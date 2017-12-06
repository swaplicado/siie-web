<?php namespace App\SImportations;

use App\ERP\SBranch;
use App\ERP\SPartner;

/**
 *
 */
class SImportBranches
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

  public function importBranches()
  {
      $sql = "SELECT id_bpb, code, bpb, b_add_prt, b_del, fid_bp, ts_new, ts_edit, ts_del FROM bpsu_bpb";
      $result = $this->webcon->query($sql);
      $lSiieBranches = array();
      $lWebBranches = SBranch::get();
      $lWebPartners = SPartner::get();
      $lBranches = array();
      $lBranchesToWeb = array();

      foreach ($lWebBranches as $key => $value)
      {
          $lBranches[$value->external_id] = $value;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_bpb"], $lBranches))
             {
                if ($row["ts_edit"] > $lBranches[$row["id_bpb"]]->updated_at ||
                      $row["ts_del"] > $lBranches[$row["id_bpb"]]->updated_at)
                {
                    $lBranches[$row["id_bpb"]]->code = $row["code"];
                    $lBranches[$row["id_bpb"]]->name = $row["bpb"];
                    $lBranches[$row["id_bpb"]]->external_id = $row["id_bpb"];
                    $lBranches[$row["id_bpb"]]->is_headquarters = $row["b_add_prt"];
                    $lBranches[$row["id_bpb"]]->is_deleted = $row["b_del"];
                    $lBranches[$row["id_bpb"]]->partner_id = SImportBranches::getPartnerId($lWebPartners, $row["fid_bp"]);
                    $lBranches[$row["id_bpb"]]->updated_at = $row["ts_edit"];

                    array_push($lBranchesToWeb, $lBranches[$row["id_bpb"]]);
                }
             }
             else
             {
                array_push($lBranchesToWeb, SImportBranches::siieToSiieWeb($row, $lWebPartners));
             }
         }
      }
      else
      {
         echo "0 results";
      }

      $this->webcon->close();

      foreach ($lBranchesToWeb as $key => $oBranch) {
         $oBranch->save();
      }

  }

  private static function siieToSiieWeb($oSiieBranch = '', $lPartners)
  {
     $oBranch = new SBranch();
     $oBranch->code = $oSiieBranch["code"];
     $oBranch->name = $oSiieBranch["bpb"];
     $oBranch->external_id = $oSiieBranch["id_bpb"];
     $oBranch->is_headquarters = $oSiieBranch["b_add_prt"];
     $oBranch->is_deleted = $oSiieBranch["b_del"];
     $oBranch->partner_id = SImportBranches::getPartnerId($lPartners, $oSiieBranch["fid_bp"]);
     $oBranch->created_by_id = 1;
     $oBranch->updated_by_id = 1;
     $oBranch->created_at = $oSiieBranch["ts_new"];
     $oBranch->updated_at = $oSiieBranch["ts_edit"];

     return $oBranch;
  }

  private static function getPartnerId($lPartners, $iExternalId)
  {
     foreach ($lPartners as $key => $oPartner) {
       if ($oPartner->external_id == $iExternalId)
       {
         return $oPartner->id_partner;
       }
     }
  }
}
