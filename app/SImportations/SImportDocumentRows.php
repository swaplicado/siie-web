<?php namespace App\SImportations;

use App\ERP\SDocumentRow;
use App\ERP\SDocument;
use App\ERP\SItem;
use App\ERP\SUnit;

/**
 * this class import the data of document rows from siie
 */
class SImportDocumentRows {
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
  function __construct($sHost = '', $sDbName = '')
  {
      $this->webhost = $sHost;
      $this->webdbname = $sDbName;
      $this->webcon = mysqli_connect($this->webhost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  /**
   *  read the data  from siie, transform it, and saves it in the database
   *  this process is divided on first and second half of year
   *
   * @param  integer $iYearId   [description]
   * @param  integer $sOperator [description]
   *
   * @return integer quantity of records imported
   */
  public function importRows($iYearId = 0)
  {
      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.ROWS'));

      $sql = "SELECT * FROM trn_dps_ety WHERE
          id_year = ".$iYearId." AND
          (ts_new > '".$oImportation->last_importation."' OR
          ts_edit > '".$oImportation->last_importation."' OR
          ts_del > '".$oImportation->last_importation."')
          AND ts_new <= '2021-01-01 23:59:00'
          ";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieRows = array();
      $lWebRows = SDocumentRow::get();
      $lDocuments = SDocument::get();
      $lItems = SItem::get();
      $lUnits = SUnit::get();
      $lWebDocuments = array();
      $lWebItems = array();
      $lWebUnits = array();
      $lRows = array();
      $lRowsToWeb = array();
      $taxRows = new SImportDocumentTaxRows($this->webhost, $this->webdbname);

      $lYears = [
        '1' => '2016',
        '2' => '2017',
        '3' => '2018',
        '4' => '2019',
        '5' => '2020',
        '6' => '2021',
        '7' => '2022',
        '8' => '2023',
        '9' => '2024',
        '10' => '2025',
        '11' => '2026',
        '12' => '2027',
        '13' => '2028',
        '14' => '2029',
        '15' => '2030',
      ];

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

      foreach ($lWebRows as $key => $value) {
          $lRows[''.$value->document_id.$lYears[$value->year_id].$value->external_id] = $value;
      }

      foreach ($lDocuments as $key => $document) {
          $lWebDocuments[$document->external_id] = $document->id_document;
      }

      foreach ($lItems as $key => $item) {
          $lWebItems[$item->external_id] = $item->id_item;
      }

      foreach ($lUnits as $key => $unit) {
          $lWebUnits[$unit->external_id] = $unit->id_unit;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
            if (array_key_exists($row["id_year"].'_'.$row["id_doc"], $lWebDocuments)) {
                $sKey = ''.$lWebDocuments[$row["id_year"].'_'.$row["id_doc"]].$row["id_year"].$row["id_ety"];

             if (array_key_exists($sKey, $lRows)) {
                if ($row["ts_edit"] > $oImportation->last_importation ||
                      $row["ts_del"] > $oImportation->last_importation) {

                    $lRows[$sKey]->concept_key = $row["concept_key"];
                    $lRows[$sKey]->concept = $row["concept"];
                    $lRows[$sKey]->reference = $row["ref"];
                    $lRows[$sKey]->quantity = $row["qty"];
                    $lRows[$sKey]->price_unit = $row["price_u"];
                    $lRows[$sKey]->price_unit_sys = $row["price_u_sys"];
                    $lRows[$sKey]->subtotal = $row["stot_r"];
                    $lRows[$sKey]->tax_charged = $row["tax_charged_r"];
                    $lRows[$sKey]->tax_retained = $row["tax_retained_r"];
                    $lRows[$sKey]->total = $row["tot_r"];
                    $lRows[$sKey]->price_unit_cur = $row["price_u_cur"];
                    $lRows[$sKey]->price_unit_sys_cur = $row["price_u_sys_cur"];
                    $lRows[$sKey]->subtotal_cur = $row["stot_cur_r"];
                    $lRows[$sKey]->tax_charged_cur = $row["tax_charged_cur_r"];
                    $lRows[$sKey]->tax_retained_cur = $row["tax_retained_cur_r"];
                    $lRows[$sKey]->total_cur = $row["tot_cur_r"];
                    $lRows[$sKey]->length = $row["len"];
                    $lRows[$sKey]->surface = $row["surf"];
                    $lRows[$sKey]->volume = $row["vol"];
                    $lRows[$sKey]->mass = $row["mass"];
                    $lRows[$sKey]->is_inventory = $row["b_inv"];
                    $lRows[$sKey]->is_deleted = $row["b_del"];
                    $lRows[$sKey]->external_id = $row["id_ety"];
                    $lRows[$sKey]->item_id = $lWebItems[$row["fid_item"]];
                    $lRows[$sKey]->unit_id = $lWebUnits[$row["fid_unit"]];
                    $lRows[$sKey]->year_id = $lYearsId[$row["id_year"]];
                    $lRows[$sKey]->document_id = $lWebDocuments[$row["id_year"].'_'.$row["id_doc"]];
                    $lRows[$sKey]->created_by_id = 1;
                    $lRows[$sKey]->updated_by_id = 1;
                    $lRows[$sKey]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    $lRows[$sKey]->taxRowsAux = $taxRows->importTaxRows($row["id_year"], $row["id_doc"], $row["id_ety"], $lWebDocuments, $lRows);

                    array_push($lRowsToWeb, $lRows[$sKey]);
                }
             }
             else {
                $oRow = SImportDocumentRows::siieToSiieWeb($row, $lWebDocuments, $lYearsId, $lWebItems, $lWebUnits);
                $oRow->taxRowsAux = $taxRows->importTaxRows($row["id_year"], $row["id_doc"], $row["id_ety"], $lWebDocuments, $lRows);
                array_push($lRowsToWeb, $oRow);
             }
           }
         }
      }

      foreach ($lRowsToWeb as $key => $oRow) {
         $oRowCopy = clone $oRow;
         $oRow->save();
         $oRow->taxRows()->saveMany($oRowCopy->taxRowsAux);
      }

      SImportUtils::saveImportation($oImportation);

      return sizeof($lRowsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieRow Objet of siie DB row
   * @param  array $lWebDocuments  array of documents to map siie to siie-web documents
   * @param  array $lYearsId  array of years to map siie to siie-web years
   * @param  array $lWebItems  array of items to map siie to siie-web items
   * @param  array $lWebUnits  array of units to map siie to siie-web units
   *
   * @return SDocumentRow
   */
  private static function siieToSiieWeb($oSiieRow = null, $lWebDocuments = [],
                                          $lYearsId = [], $lWebItems = [], $lWebUnits = [])
  {
      $oRow = new SDocumentRow();

      $oRow->concept_key = $oSiieRow["concept_key"];
      $oRow->concept = $oSiieRow["concept"];
      $oRow->reference = $oSiieRow["ref"];
      $oRow->quantity = $oSiieRow["qty"];
      $oRow->price_unit = $oSiieRow["price_u"];
      $oRow->price_unit_sys = $oSiieRow["price_u_sys"];
      $oRow->subtotal = $oSiieRow["stot_r"];
      $oRow->tax_charged = $oSiieRow["tax_charged_r"];
      $oRow->tax_retained = $oSiieRow["tax_retained_r"];
      $oRow->total = $oSiieRow["tot_r"];
      $oRow->price_unit_cur = $oSiieRow["price_u_cur"];
      $oRow->price_unit_sys_cur = $oSiieRow["price_u_sys_cur"];
      $oRow->subtotal_cur = $oSiieRow["stot_cur_r"];
      $oRow->tax_charged_cur = $oSiieRow["tax_charged_cur_r"];
      $oRow->tax_retained_cur = $oSiieRow["tax_retained_cur_r"];
      $oRow->total_cur = $oSiieRow["tot_cur_r"];
      $oRow->length = $oSiieRow["len"];
      $oRow->surface = $oSiieRow["surf"];
      $oRow->volume = $oSiieRow["vol"];
      $oRow->mass = $oSiieRow["mass"];
      $oRow->is_inventory = $oSiieRow["b_inv"];
      $oRow->is_deleted = $oSiieRow["b_del"];
      $oRow->external_id = $oSiieRow["id_ety"];
      $oRow->item_id = $lWebItems[$oSiieRow["fid_item"]];
      $oRow->unit_id = $lWebUnits[$oSiieRow["fid_unit"]];
      $oRow->year_id = $lYearsId[$oSiieRow["id_year"]];
      $oRow->document_id = $lWebDocuments[$oSiieRow["id_year"].'_'.$oSiieRow["id_doc"]];
      $oRow->created_by_id = 1;
      $oRow->updated_by_id = 1;
      $oRow->created_at = $oSiieRow["ts_new"];
      $oRow->updated_at = $oSiieRow["ts_edit"];

      return $oRow;
  }
}
