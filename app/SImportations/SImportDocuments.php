<?php namespace App\SImportations;

use App\ERP\SDocument;
use App\ERP\SPartner;
use App\ERP\SBranch;
use App\ERP\SAddress;

/**
 * this class import the data of documents from siie
 */
class SImportDocuments
{
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = '';
  protected $webcon         = '';

  /**
   * __construct
   *
   * @param string $sHost the name of host to connect
   *                  can be a IP or name of host
   * @param string $sDbName name of data base to read
   */
  function __construct($sHost, $sDbName)
  {
      $this->webdbname = $sDbName;
      $this->webcon = mysqli_connect($sHost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  /**
   *  read the data  from siie, transform it, and saves it in the database
   *
   * @param  integer $iYearId  id of year in siie ('2017')
   * @param  integer $sOperator [description]
   *
   * @return integer quantity of records imported
   */
  public function importDocuments($iYearId)
  {
      $lYears = [
        '2016' => '1',
        '2017' => '2',
        '2018' => '3',
        '2019' => '4',
        '2020' => '5',
        '2021' => '6',
        '2022' => '7',
        '2023' => '8',
        '2024' => '9',
        '2025' => '10',
        '2026' => '11',
        '2027' => '12',
        '2028' => '13',
        '2029' => '14',
        '2030' => '15',
      ];

      $lCurrencies = [
        '1' => '2',
        '2' => '3',
        '3' => '4',
      ];

      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.DOCUMENTS'));

      $sql = "SELECT
            id_year,
            id_doc,
            dt,
            dt_doc,
            num,
            num_ser,
            stot_r,
            tax_charged_r,
            tax_retained_r,
            tot_r,
            exc_rate,
            exc_rate_sys,
            stot_cur_r,
            tax_charged_cur_r,
            tax_retained_cur_r,
            tot_cur_r,
            b_close,
            b_del,
            fid_ct_dps,
            fid_cl_dps,
            fid_tp_dps,
            fid_st_dps,
            fid_src_year_n,
            fid_src_doc_n,
            fid_cob,
            fid_cur,
            fid_bp_r,
            fid_bpb,
            fid_add,
            ts_new,
            ts_edit,
            ts_del
        FROM
            trn_dps
        WHERE
             id_year = ".$iYearId." AND
            (ts_new > '".$oImportation->last_importation."' OR
            ts_edit > '".$oImportation->last_importation."' OR
            ts_del > '".$oImportation->last_importation."') 
            ORDER BY id_doc ASC";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieDocuments = array();
      $lWebDocuments = SDocument::get();
      $lPartners = SPartner::get();
      $lBranches = SBranch::get();
      $lAddress = SAddress::get();
      $lDocuments = array();
      $lDocumentsToWeb = array();
      $lWebPartners = array();
      $lWebBranches = array();
      $lWebAddresses = array();

      foreach ($lWebDocuments as $key => $value) {
          $lDocuments[$value->external_id] = $value;
          $lDocsYear[$value->external_id] = $value->id_document;
      }

      foreach ($lPartners as $key => $partner) {
          $lWebPartners[$partner->external_id] = $partner->id_partner;
      }

      foreach ($lBranches as $key => $branch) {
          $lWebBranches[$branch->external_id] = $branch->id_branch;
      }

      foreach ($lAddress as $key => $address) {
          $lWebAddresses[$address->external_id.'-'.$address->external_ad_id] = $address->id_branch_address;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_year"].'_'.$row["id_doc"], $lDocuments)) {
                if ($row["ts_edit"] > $oImportation->last_importation ||
                      $row["ts_del"] > $oImportation->last_importation) {
                    $sKey = $row["id_year"].'_'.$row["id_doc"];

                    $lDocuments[$sKey]->dt_date = $row["dt"];
                    $lDocuments[$sKey]->dt_doc = $row["dt_doc"];
                    $lDocuments[$sKey]->num = $row["num"];
                    $lDocuments[$sKey]->service_num = $row["num_ser"];
                    $lDocuments[$sKey]->subtotal = $row["stot_r"];
                    $lDocuments[$sKey]->tax_charged = $row["tax_charged_r"];
                    $lDocuments[$sKey]->tax_retained = $row["tax_retained_r"];
                    $lDocuments[$sKey]->total = $row["tot_r"];
                    $lDocuments[$sKey]->exchange_rate = $row["exc_rate"];
                    $lDocuments[$sKey]->exchange_rate_sys = $row["exc_rate_sys"];
                    $lDocuments[$sKey]->subtotal_cur = $row["stot_cur_r"];
                    $lDocuments[$sKey]->tax_charged_cur = $row["tax_charged_cur_r"];
                    $lDocuments[$sKey]->tax_retained_cur = $row["tax_retained_cur_r"];
                    $lDocuments[$sKey]->total_cur = $row["tot_cur_r"];
                    $lDocuments[$sKey]->is_closed = $row["b_close"];
                    $lDocuments[$sKey]->is_deleted = $row["b_del"];
                    $lDocuments[$sKey]->external_id = $row["id_year"].'_'.$row["id_doc"];
                    $lDocuments[$sKey]->year_id = $lYears[$row["id_year"]];
                    $lDocuments[$sKey]->billing_branch_id = $lWebBranches[$row["fid_cob"]];
                    $lDocuments[$sKey]->doc_category_id = $row["fid_ct_dps"];
                    $lDocuments[$sKey]->doc_class_id = $row["fid_cl_dps"];
                    $lDocuments[$sKey]->doc_type_id = $row["fid_tp_dps"];
                    try {
                      $src_id = $lDocsYear[$lYears[$row["fid_src_year_n"]].'_'.$row["fid_src_doc_n"]];
                    }
                    catch (\ErrorException $e) {
                      $src_id = 1;
                    }
                    $lDocuments[$sKey]->doc_src_id = is_numeric($src_id) ? $src_id : 1;
                    $lDocuments[$sKey]->doc_status_id = 1;
                    $lDocuments[$sKey]->doc_sys_status_id = $row["fid_st_dps"];
                    $lDocuments[$sKey]->currency_id = $lCurrencies[$row["fid_cur"]];
                    $lDocuments[$sKey]->partner_id = $lWebPartners[$row["fid_bp_r"]];
                    $lDocuments[$sKey]->branch_id = $lWebBranches[$row["fid_bpb"]];
                    $lDocuments[$sKey]->address_id = $lWebAddresses[$row["fid_bpb"].'-'.$row["fid_add"]];
                    $lDocuments[$sKey]->created_by_id = 1;
                    $lDocuments[$sKey]->updated_by_id = 1;
                    $lDocuments[$sKey]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lDocumentsToWeb, $lDocuments[$sKey]);
                }
             }
             else {
                array_push($lDocumentsToWeb, SImportDocuments::siieToSiieWeb($row,
                                                                          $lYears,
                                                                          $lWebPartners,
                                                                          $lWebBranches,
                                                                          $lWebAddresses,
                                                                          $lCurrencies,
                                                                          $lDocsYear));
             }
         }
      }

       foreach ($lDocumentsToWeb as $key => $document) {
         $document->save();
       }

       SImportUtils::saveImportation($oImportation, $iYearId);

       return sizeof($lDocumentsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieDocument
   * @param  array  $lYears  array of years to map siie to siie-web years
   * @param  array  $lWebPartners  array of partners to map siie to siie-web partners
   * @param  array  $lCurrencies  array of currencies to map siie to siie-web currencies
   * @param  array  $lDocsYear  array of documents to map siie to siie-web documents
   *
   * @return SDocument
   */
  private static function siieToSiieWeb($oSiieDocument = null, $lYears = [], $lWebPartners = [],
                                  $lWebBranches = [], $lWebAddresses = [], $lCurrencies = [], $lDocsYear = []) {
       $oDocument = new SDocument();
       $oDocument->dt_date = $oSiieDocument["dt"];
       $oDocument->dt_doc = $oSiieDocument["dt_doc"];
       $oDocument->num = $oSiieDocument["num"];
       $oDocument->service_num = $oSiieDocument["num_ser"];
       $oDocument->subtotal = $oSiieDocument["stot_r"];
       $oDocument->tax_charged = $oSiieDocument["tax_charged_r"];
       $oDocument->tax_retained = $oSiieDocument["tax_retained_r"];
       $oDocument->total = $oSiieDocument["tot_r"];
       $oDocument->exchange_rate = $oSiieDocument["exc_rate"];
       $oDocument->exchange_rate_sys = $oSiieDocument["exc_rate_sys"];
       $oDocument->subtotal_cur = $oSiieDocument["stot_cur_r"];
       $oDocument->tax_charged_cur = $oSiieDocument["tax_charged_cur_r"];
       $oDocument->tax_retained_cur = $oSiieDocument["tax_retained_cur_r"];
       $oDocument->total_cur = $oSiieDocument["tot_cur_r"];
       $oDocument->is_closed = $oSiieDocument["b_close"];
       $oDocument->is_deleted = $oSiieDocument["b_del"];
       $oDocument->external_id = $oSiieDocument["id_year"].'_'.$oSiieDocument["id_doc"];
       $oDocument->year_id = $lYears[$oSiieDocument["id_year"]];
       $oDocument->billing_branch_id = $lWebBranches[$oSiieDocument["fid_cob"]];
       $oDocument->doc_category_id = $oSiieDocument["fid_ct_dps"];
       $oDocument->doc_class_id = $oSiieDocument["fid_cl_dps"];
       $oDocument->doc_type_id = $oSiieDocument["fid_tp_dps"];
       try {
         $src_id = $lDocsYear[$lYears[$oSiieDocument["fid_src_year_n"]].'_'.$oSiieDocument["fid_src_doc_n"]];
       }
       catch (\ErrorException $e) {
         $src_id = 1;
       }

       $oDocument->doc_src_id = $src_id;
       $oDocument->doc_status_id = 1;
       $oDocument->doc_sys_status_id = $oSiieDocument["fid_st_dps"];
       $oDocument->currency_id = $lCurrencies[$oSiieDocument["fid_cur"]];
       $oDocument->partner_id = $lWebPartners[$oSiieDocument["fid_bp_r"]];
       $oDocument->branch_id = $lWebBranches[$oSiieDocument["fid_bpb"]];
       $oDocument->address_id = $lWebAddresses[$oSiieDocument["fid_bpb"].'-'.$oSiieDocument["fid_add"]];
       $oDocument->created_by_id = 1;
       $oDocument->updated_by_id = 1;
       $oDocument->created_at = $oSiieDocument["ts_new"];
       $oDocument->updated_at = $oSiieDocument["ts_edit"];

       return $oDocument;
  }
}
