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

  public function importTaxRows($iYear, $iDocExternalId, $iExternalRowId, $lWebDocuments, $lWebDocumentRows)
  {
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

      if (array_key_exists(''.$lWebDocuments[$iDocExternalId].$lYearsId[$iYear].$iExternalRowId, $lWebDocumentRows)) {
        $lTaxes = SDocumentRowTax::where('year_id', $lYearsId[$iYear])
                                  ->where('document_id', $lWebDocuments[$iDocExternalId])
                                  ->where('document_row_id', $lWebDocumentRows[''.$lWebDocuments[$iDocExternalId].$lYearsId[$iYear].$iExternalRowId]);
        $lTaxes->delete();
      }

      $sql = "SELECT *
              FROM trn_dps_ety_tax
              where id_year = $iYear and id_doc = $iDocExternalId and id_ety = $iExternalRowId";

      $result = $this->webcon->query($sql);
      $lTaxRowsToWeb = array();

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
            array_push($lTaxRowsToWeb, SImportDocumentTaxRows::siieToSiieWeb($row, $lWebDocuments, $lYearsId, $lWebDocumentRows));
         }
      }

      // $this->webcon->close();

      // foreach ($lTaxRowsToWeb as $key => $oRow) {
      //    $oRow->save();
      // }

      return $lTaxRowsToWeb;
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
      // $oRow->document_row_id = $lWebDocumentRows[''.$lWebDocuments[$oSiieRow["id_doc"]].$oSiieRow["id_year"].$oSiieRow["id_ety"]];
      $oRow->document_id = $lWebDocuments[$oSiieRow["id_doc"]];
      $oRow->year_id = $lYearsId[$oSiieRow["id_year"]];

      return $oRow;
  }
}
