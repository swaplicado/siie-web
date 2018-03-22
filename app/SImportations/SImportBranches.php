<?php namespace App\SImportations;

use App\ERP\SBranch;
use App\ERP\SPartner;

/**
 * this class import the data of branches from siie
 */
class SImportBranches {
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
  public function importBranches()
  {
      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.BRANCHES'));

      $sql = "SELECT id_bpb, code, bpb, b_add_prt, b_del, fid_bp, ts_new, ts_edit, ts_del FROM bpsu_bpb WHERE
                    ts_new > '".$oImportation->last_importation."' OR
                    ts_edit > '".$oImportation->last_importation."' OR
                    ts_del > '".$oImportation->last_importation."'
                    ";

      $result = $this->webcon->query($sql);

      $lSiieBranches = array();
      $lWebBranches = SBranch::get();
      $lPartners = SPartner::get();
      $lWebPartners = array();
      $lBranches = array();
      $lBranchesToWeb = array();

      foreach ($lWebBranches as $key => $value) {
          $lBranches[$value->external_id] = $value;
      }

      foreach ($lPartners as $key => $partner) {
          $lWebPartners[$partner->external_id] = $partner->id_partner;
      }

      $this->webcon->close();

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_bpb"], $lBranches)) {
                if ($row["ts_edit"] > $lBranches[$row["id_bpb"]]->updated_at ||
                      $row["ts_del"] > $lBranches[$row["id_bpb"]]->updated_at) {
                    $lBranches[$row["id_bpb"]]->code = $row["code"];
                    $lBranches[$row["id_bpb"]]->name = $row["bpb"];
                    $lBranches[$row["id_bpb"]]->external_id = $row["id_bpb"];
                    $lBranches[$row["id_bpb"]]->is_headquarters = $row["b_add_prt"];
                    $lBranches[$row["id_bpb"]]->is_deleted = $row["b_del"];
                    $lBranches[$row["id_bpb"]]->partner_id = $lWebPartners[$row["fid_bp"]];
                    $lBranches[$row["id_bpb"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lBranchesToWeb, $lBranches[$row["id_bpb"]]);
                }
             }
             else {
                array_push($lBranchesToWeb, SImportBranches::siieToSiieWeb($row, $lWebPartners));
             }
         }
      }

      foreach ($lBranchesToWeb as $key => $oBranch) {
         $oBranch->save();
      }

      SImportUtils::saveImportation($oImportation);

      return sizeof($lBranchesToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieBranch
   * @param  array $lWebPartners array of partners to map siie to siie-web partners
   *
   * @return SBranch object from siie
   */
  private static function siieToSiieWeb($oSiieBranch = '', $lWebPartners = [])
  {
     $oBranch = new SBranch();
     $oBranch->code = $oSiieBranch["code"];
     $oBranch->name = $oSiieBranch["bpb"];
     $oBranch->external_id = $oSiieBranch["id_bpb"];
     $oBranch->is_headquarters = $oSiieBranch["b_add_prt"];
     $oBranch->is_deleted = $oSiieBranch["b_del"];
     $oBranch->partner_id = $lWebPartners[$oSiieBranch["fid_bp"]];
     $oBranch->created_by_id = 1;
     $oBranch->updated_by_id = 1;
     $oBranch->created_at = $oSiieBranch["ts_new"];
     $oBranch->updated_at = $oSiieBranch["ts_edit"];

     return $oBranch;
  }
}
