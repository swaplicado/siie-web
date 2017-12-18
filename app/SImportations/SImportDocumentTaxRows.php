<?php namespace App\SImportations;

use App\ERP\SDocument;
use App\ERP\SDocumentRow;
use App\ERP\SDocumentRowTax;
use App\ERP\SItem;
use App\ERP\SUnit;

/**
 *
 */
class SImportDocumentTaxRows
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

  public function importRows($iYearId, $sOperator)
  {
      $sql = "SELECT *
              FROM trn_dps_ety_tax tx
              INNER JOIN trn_dps_ety et ON tx.id_year = et.id_year and tx.id_doc = et.id_doc and tx.id_ety = et.id_ety
              where tx.id_year = ".$iYearId." AND et.ts_new ".$sOperator." '".$iYearId."-06-01';";

      $result = $this->webcon->query($sql);
      $lSiieRows = array();
      $lWebTaxRows = SDocumentRowTax::get();
      $lDocuments = SDocument::get();
      $lDocumentRows = SDocumentRow::get();
      $lWebDocuments = array();
      $lWebDocumentRows = array();
      $lWebDocumentRowsIds = array();
      $lTaxRowsToWeb = array();

      $lYears = [
        '1' => '2016',
        '2' => '2017',
        '3' => '2018',
        '4' => '2019',
        '5' => '2020',
        '6' => '2021',
        '7' => '2022',
      ];

      $lYearsId = [
        '2016' => '1',
        '2017' => '2',
        '2018' => '3',
        '2019' => '4',
        '2020' => '5',
        '2021' => '6',
        '2022' => '7',
      ];


      foreach ($lDocuments as $key => $document) {
          $lWebDocuments[$document->external_id] = $document->id_document;
      }

      foreach ($lDocumentRows as $key => $value)
      {
          $lWebDocumentRows[''.$value->document_id.$lYears[$value->year_id].$value->external_id] = $value->id_document_row;
      }

      foreach ($lDocumentRows as $key => $value)
      {
          $lWebDocumentRowsIds[''.$value->document_id.$lYears[$value->year_id].$value->id_document_row] = $value->id_document_row;
      }

      foreach ($lWebTaxRows as $key => $taxRow) {
          // doc + year + row
          $externalRowId = $lWebDocumentRowsIds[''.$taxRow->document_id.$lYears[$taxRow->year_id].$taxRow->document_row_id];

          $lRows[''.$taxRow->external_id.$externalRowId.$taxRow->document_id.$lYears[$taxRow->year_id]] = $taxRow;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             $sKey = ''.$row["id_tax"].$row["id_ety"].$lWebDocuments[$row["id_doc"]].$row["id_year"];

             if (array_key_exists($sKey, $lRows))
             {
                if ($row["et.ts_edit"] > $lRows[$sKey]->updated_at ||
                      $row["et.ts_del"] > $lRows[$sKey]->updated_at)
                {
                    $lRows[$sKey]->concept_key = $row["concept_key"];
                    $lRows[$sKey]->concept = $row["concept"];

                    $lRows[$sKey]->percentage = $row["per"];
                    $lRows[$sKey]->value_unit = $row["val_u"];
                    $lRows[$sKey]->value = $row["val"];
                    $lRows[$sKey]->tax = $row["tax"];
                    $lRows[$sKey]->tax_currency = $row["tax_cur"];
                    // $lRows[$sKey]->external_id = $row["id_tax"];
                    // $lRows[$sKey]->document_row_id = $lWebDocumentRows[''.$lWebDocuments[$row["id_doc"]].$row["id_year"].$row["id_ety"]];
                    // $lRows[$sKey]->document_id = $lWebDocuments[$row["id_doc"]];
                    // $lRows[$sKey]->year_id = $lYearsId[$row["id_year"]];

                    array_push($lTaxRowsToWeb, $lRows[$sKey]);
                }
             }
             else
             {
                array_push($lTaxRowsToWeb, SImportDocumentTaxRows::siieToSiieWeb($row, $lWebDocuments, $lYearsId, $lWebDocumentRows));
             }
         }
      }
      else
      {
         echo "0 results";
      }

      $this->webcon->close();

      foreach ($lTaxRowsToWeb as $key => $oRow) {
         $oRow->save();
      }

      return true;
  }

  private static function siieToSiieWeb($oSiieRow = '', $lWebDocuments, $lYearsId, $lWebDocumentRows)
  {
      $oRow = new SDocumentRowTax();

      $oRow->percentage = $oSiieRow["per"];
      $oRow->value_unit = $oSiieRow["val_u"];
      $oRow->value = $oSiieRow["val"];
      $oRow->tax = $oSiieRow["tax"];
      $oRow->tax_currency = $oSiieRow["tax_cur"];
      $oRow->external_id = $oSiieRow["id_tax"];
      $oRow->document_row_id = $lWebDocumentRows[''.$lWebDocuments[$oSiieRow["id_doc"]].$oSiieRow["id_year"].$oSiieRow["id_ety"]];
      $oRow->document_id = $lWebDocuments[$oSiieRow["id_doc"]];
      $oRow->year_id = $lYearsId[$oSiieRow["id_year"]];

      return $oRow;
  }
}
