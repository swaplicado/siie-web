<?php namespace App\SImportations;

use App\ERP\SAddress;
use App\ERP\SBranch;
use App\ERP\SCountry;
use App\ERP\SState;

/**
 * this class import the data of address from siie
 */
class SImportAddresses {
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
      $this->webcon = mysqli_connect($sHost, $this->webusername,
                                    $this->webpassword, $this->webdbname);
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
  public function importAddresses()
  {
      $sql = "SELECT
                  bpb_add,
                  street,
                  street_num_ext,
                  street_num_int,
                  neighborhood,
                  reference,
                  locality,
                  county,
                  state,
                  zip_code,
                  b_def,
                  COALESCE(lc.cty_key, 251) AS cty_key,
                  COALESCE(ls.sta_code, 'NA') AS sta_code,
                  bba.id_bpb,
                  bba.fid_cty_n,
                  bba.fid_sta_n,
                  bba.b_del,
                  bba.ts_new,
                  bba.ts_edit,
                  bba.ts_del
              FROM
                  bpsu_bpb_add AS bba
                      LEFT OUTER JOIN
                  locu_cty AS lc ON (bba.fid_cty_n = lc.id_cty)
                      LEFT OUTER JOIN
                  locu_sta AS ls ON (bba.fid_sta_n = ls.id_sta)";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieAddresses = array();
      $lWebAddresses = SAddress::get();
      $lBranches = SBranch::get();
      $lWebBranches = array();
      $lCountries = SCountry::get();
      $lWebCountries = array();
      $lStates = SState::get();
      $lWebStates = array();
      $lAddresses = array();
      $lAddressesToWeb = array();

      foreach ($lWebAddresses as $key => $value) {
          $lAddresses[$value->external_id] = $value;
      }

      foreach ($lBranches as $key => $branch) {
          $lWebBranches[$branch->external_id] = $branch->id_branch;
      }

      foreach ($lCountries as $key => $country) {
          $lWebCountries[$country->code] = $country->id_country;
      }

      foreach ($lStates as $key => $state) {
          $lWebStates[$state->code] = $state->id_state;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
           $rowId = $row["id_bpb"];
             if (array_key_exists($rowId, $lAddresses)) {
                if ($row["ts_edit"] > $lAddresses[$row["id_bpb"]]->updated_at ||
                      $row["ts_del"] > $lAddresses[$row["id_bpb"]]->updated_at) {

                    $lAddresses[$rowId]->name = $row["bpb_add"];
                    $lAddresses[$rowId]->street = $row["street"];
                    $lAddresses[$rowId]->num_ext = $row["street_num_ext"];
                    $lAddresses[$rowId]->num_int = $row["street_num_int"];
                    $lAddresses[$rowId]->neighborhood = $row["neighborhood"];
                    $lAddresses[$rowId]->reference = $row["reference"];
                    $lAddresses[$rowId]->locality = $row["locality"];
                    $lAddresses[$rowId]->county = $row["county"];
                    $lAddresses[$rowId]->state_name = $row["state"];
                    $lAddresses[$rowId]->zip_code = $row["zip_code"];
                    $lAddresses[$rowId]->external_id = $rowId;
                    $lAddresses[$rowId]->is_main = $row["b_def"];
                    $lAddresses[$rowId]->branch_id = $lWebBranches[$rowId];
                    $lAddresses[$rowId]->country_id = $lWebCountries[$row["cty_key"]];
                    $lAddresses[$rowId]->state_id = $lWebStates[$row["sta_code"]];
                    $lAddresses[$rowId]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lAddressesToWeb, $lAddresses[$rowId]);
                }
             }
             else {
                array_push($lAddressesToWeb, SImportAddresses::siieToSiieWeb($row, $lWebBranches, $lWebCountries, $lWebStates));
             }
         }
      }
      else {
         echo "0 results";
      }

      foreach ($lAddressesToWeb as $key => $oAddress) {
         $oAddress->save();
      }

      return sizeof($lAddressesToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  string $oSiieAddress
   * @param  array $lWebBranches array of branches to map siie to siie-web branches
   * @param  array $lWebCountries array of countries to map siie to siie-web countries
   * @param  array $lWebStates array of states to map siie to siie-web states
   *
   * @return SAddress object from siie
   */
  private static function siieToSiieWeb($oSiieAddress = '', $lWebBranches = [],
                                        $lWebCountries = [], $lWebStates = [])
  {
     $oAddress = new SAddress();
     $oAddress->name = $oSiieAddress["bpb_add"];
     $oAddress->street = $oSiieAddress["street"];
     $oAddress->num_ext = $oSiieAddress["street_num_ext"];
     $oAddress->num_int = $oSiieAddress["street_num_int"];
     $oAddress->neighborhood = $oSiieAddress["neighborhood"];
     $oAddress->reference = $oSiieAddress["reference"];
     $oAddress->locality = $oSiieAddress["locality"];
     $oAddress->county = $oSiieAddress["county"];
     $oAddress->state_name = $oSiieAddress["state"];
     $oAddress->zip_code = $oSiieAddress["zip_code"];
     $oAddress->external_id = $oSiieAddress["id_bpb"];
     $oAddress->is_main = $oSiieAddress["b_def"];
     $oAddress->is_deleted = $oSiieAddress["b_del"];
     $oAddress->branch_id = $lWebBranches[$oSiieAddress["id_bpb"]];
     $oAddress->country_id = $lWebCountries[$oSiieAddress["cty_key"]];
     $oAddress->state_id = $lWebStates[$oSiieAddress["sta_code"]];
     $oAddress->created_by_id = 1;
     $oAddress->updated_by_id = 1;
     $oAddress->created_at = $oSiieAddress["ts_new"];
     $oAddress->updated_at = $oSiieAddress["ts_edit"];

     return $oAddress;
  }
}
