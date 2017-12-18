<?php namespace App\SImportations;

use App\ERP\SDocument;
use App\ERP\SPartner;
use App\ERP\SCurrency;

/**
 *
 */
class SImportDocuments
{
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = '';
  protected $webcon         = '';

  function __construct($sDbName)
  {
      $this->webdbname = $sDbName;
      $this->webcon = mysqli_connect($this->webhost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  public function importDocuments($iYearId)
  {
      // $document = SDocument::find(1);
      // $document->rows;
      // foreach ($document->rows as $key => $row) {
      //   $row->taxRows;
      // }
      // dd($document);
      // $document = '';

      $lYears = [
        '2016' => '1',
        '2017' => '2',
        '2018' => '3',
      ];

      $lCurrencies = [
        '1' => '2',
        '2' => '3',
        '3' => '4',
      ];

      $sql = "SELECT
            id_year,
            id_doc,
            dt,
            dt_doc,
            num,
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
            fid_cur,
            fid_bp_r,
            ts_new,
            ts_edit,
            ts_del
        FROM
            trn_dps
        WHERE
            id_year = ".$iYearId.";";

      $result = $this->webcon->query($sql);
      $lSiieDocuments = array();
      $lWebDocuments = SDocument::get();
      $lPartners = SPartner::get();
      $lDocuments = array();
      $lDocumentsToWeb = array();
      $lWebPartners = array();

      foreach ($lWebDocuments as $key => $value)
      {
          $lDocuments[$value->external_id] = $value;
      }

      foreach ($lPartners as $key => $partner) {
          $lWebPartners[$partner->external_id] = $partner->id_partner;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_doc"], $lDocuments))
             {
                if ($row["ts_edit"] > $lDocuments[$row["id_doc"]]->updated_at ||
                      $row["ts_del"] > $lDocuments[$row["id_doc"]]->updated_at)
                {
                    $lDocuments[$row["id_doc"]]->dt_date = $row["dt"];
                    $lDocuments[$row["id_doc"]]->dt_doc = $row["dt_doc"];
                    $lDocuments[$row["id_doc"]]->num = $row["num"];
                    $lDocuments[$row["id_doc"]]->subtotal = $row["stot_r"];
                    $lDocuments[$row["id_doc"]]->tax_charged = $row["tax_charged_r"];
                    $lDocuments[$row["id_doc"]]->tax_retained = $row["tax_retained_r"];
                    $lDocuments[$row["id_doc"]]->total = $row["tot_r"];
                    $lDocuments[$row["id_doc"]]->exchange_rate = $row["exc_rate"];
                    $lDocuments[$row["id_doc"]]->exchange_rate_sys = $row["exc_rate_sys"];
                    $lDocuments[$row["id_doc"]]->subtotal_cur = $row["stot_cur_r"];
                    $lDocuments[$row["id_doc"]]->tax_charged_cur = $row["tax_charged_cur_r"];
                    $lDocuments[$row["id_doc"]]->tax_retained_cur = $row["tax_retained_cur_r"];
                    $lDocuments[$row["id_doc"]]->total_cur = $row["tot_cur_r"];
                    $lDocuments[$row["id_doc"]]->is_closed = $row["b_close"];
                    $lDocuments[$row["id_doc"]]->is_deleted = $row["b_del"];
                    $lDocuments[$row["id_doc"]]->external_id = $row["id_doc"];
                    $lDocuments[$row["id_doc"]]->year_id = $lYears[$row["id_year"]];
                    $lDocuments[$row["id_doc"]]->doc_category_id = $row["fid_ct_dps"];
                    $lDocuments[$row["id_doc"]]->doc_class_id = $row["fid_cl_dps"];
                    $lDocuments[$row["id_doc"]]->doc_type_id = $row["fid_tp_dps"];
                    $lDocuments[$row["id_doc"]]->doc_status_id = 1;
                    $lDocuments[$row["id_doc"]]->currency_id = $lCurrencies[$row["fid_cur"]];
                    $lDocuments[$row["id_doc"]]->partner_id = $lWebPartners[$row["fid_bp_r"]];
                    $lDocuments[$row["id_doc"]]->created_by_id = 1;
                    $lDocuments[$row["id_doc"]]->updated_by_id = 1;
                    $lDocuments[$row["id_doc"]]->created_at = $row["ts_new"];
                    $lDocuments[$row["id_doc"]]->updated_at = $row["ts_edit"];

                    array_push($lDocumentsToWeb, $lDocuments[$row["id_doc"]]);
                }
             }
             else
             {
                array_push($lDocumentsToWeb, SImportDocuments::siieToSiieWeb($row, $lYears, $lWebPartners, $lCurrencies));
             }
         }
      }
      else
      {
         echo "0 results";
      }
       $this->webcon->close();

       foreach ($lDocumentsToWeb as $key => $document) {
         $document->save();
       }

       return true;
  }

  private static function siieToSiieWeb($oSiieDocument = '', $lYears, $lWebPartners, $lCurrencies)
  {
     $oDocument = new SDocument();
     $oDocument->dt_date = $oSiieDocument["dt"];
     $oDocument->dt_doc = $oSiieDocument["dt_doc"];
     $oDocument->num = $oSiieDocument["num"];
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
     $oDocument->external_id = $oSiieDocument["id_doc"];
     $oDocument->year_id = $lYears[$oSiieDocument["id_year"]];
     $oDocument->doc_category_id = $oSiieDocument["fid_ct_dps"];
     $oDocument->doc_class_id = $oSiieDocument["fid_cl_dps"];
     $oDocument->doc_type_id = $oSiieDocument["fid_tp_dps"];
     $oDocument->doc_status_id = 1;
     $oDocument->currency_id = $lCurrencies[$oSiieDocument["fid_cur"]];
     $oDocument->partner_id = $lWebPartners[$oSiieDocument["fid_bp_r"]];
     $oDocument->created_by_id = 1;
     $oDocument->updated_by_id = 1;
     $oDocument->created_at = $oSiieDocument["ts_new"];
     $oDocument->updated_at = $oSiieDocument["ts_edit"];

     return $oDocument;
  }
}
