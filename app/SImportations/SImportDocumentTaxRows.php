<?php namespace App\SImportations;

use App\ERP\SDocument;
use App\ERP\SDocumentRow;
use App\ERP\SDocumentRowTax;
use App\ERP\SItem;
use App\ERP\SUnit;

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
   * @param  array   $lWebDocuments  array of documents to map siie to siie-web documents
   * @param  array   $lWebDocumentRows  array of document rows to map siie to siie-web document rows
   *
   * @return array array of tax rows
   */
  public function importTaxRows($iYear = 0, $iDocExternalId = 0, $iExternalRowId = 0,
                                $lWebDocuments = [], $lWebDocumentRows = [])
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

      if (array_key_exists(''.$lWebDocuments[$iYear.'_'.$iDocExternalId].$lYearsId[$iYear].$iExternalRowId, $lWebDocumentRows)) {
        $lTaxes = SDocumentRowTax::where('year_id', $lYearsId[$iYear])
                                  ->where('document_id', $lWebDocuments[$iYear.'_'.$iDocExternalId])
                                  ->where('document_row_id', $lWebDocumentRows[''.$lWebDocuments[$iYear.'_'.$iDocExternalId].$lYearsId[$iYear].$iExternalRowId]);
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

      return $lTaxRowsToWeb;
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieRow
   * @param  array  $lWebDocuments  array of documents to map siie to siie-web documents
   * @param  array  $lYearsId  array of years to map siie to siie-web years
   * @param  array  $lWebDocumentRows  array of document rows to map siie to siie-web document rows
   *
   * @return SDocumentRowTax
   */
  private static function siieToSiieWeb($oSiieRow = null, $lWebDocuments = [], $lYearsId = [], $lWebDocumentRows = [])
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
