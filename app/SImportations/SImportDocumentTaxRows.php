<?php namespace App\SImportations;

use App\ERP\SDocumentRowTax;

/**
 * this class import the data of document taxes from siie
 */
class SImportDocumentTaxRows
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
   * read the data  from siie, transform it, and saves it in the database
   *
   * @param  integer $iYearId  id of year in siie ('2017')
   * @param  integer $iDocExternalId
   * @param  integer $iExternalRowId
   * @param  array   $lWebDocuments key: year_iddoc  array of documents to map siie to siie-web documents
   * @param  array   $lWebDocumentRows key: documentid_ety  array of document rows to map siie to siie-web document rows
   * @param int $idRow puede ser 0
   *
   * @return array array of tax rows
   */
  public function importTaxRows($iYear, $iDocExternalId, $iExternalRowId,
                                $lWebDocuments, $lWebDocumentRows, $idRow)
  {
      $lYearsId = [
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

      if ($idRow > 0) {
        SDocumentRowTax::where('document_row_id', $idRow)->delete();
      }

      $sql = "SELECT *
              FROM trn_dps_ety_tax
              where id_year = ".$iYear." and id_doc = ".$iDocExternalId." and id_ety = ".$iExternalRowId."";

      $result = $this->webcon->query($sql);
      $lTaxRowsToWeb = array();

      if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
          array_push($lTaxRowsToWeb, SImportDocumentTaxRows::siieToSiieWeb($row, $lWebDocuments, $lYearsId));
        }
      }

      return $lTaxRowsToWeb;
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  array $oSiieRow
   * @param  array  $lWebDocuments  array of documents to map siie to siie-web documents
   * @param  array  $lYearsId  array of years to map siie to siie-web years
   * @param  array  $lWebDocumentRows  array of document rows to map siie to siie-web document rows
   *
   * @return SDocumentRowTax
   */
  private static function siieToSiieWeb($oSiieRow, $lWebDocuments, $lYearsId)
  {
      $oRow = new SDocumentRowTax();

      $oRow->percentage = $oSiieRow["per"];
      $oRow->value_unit = $oSiieRow["val_u"];
      $oRow->value = $oSiieRow["val"];
      $oRow->tax = $oSiieRow["tax"];
      $oRow->tax_currency = $oSiieRow["tax_cur"];
      $oRow->external_id = $oSiieRow["id_tax"];
      $oRow->document_id = $lWebDocuments[$oSiieRow["id_year"].'_'.$oSiieRow["id_doc"]];
      $oRow->year_id = $lYearsId[$oSiieRow["id_year"]];

      return $oRow;
  }
}
