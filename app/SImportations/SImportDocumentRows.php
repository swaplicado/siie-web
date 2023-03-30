<?php namespace App\SImportations;

use App\ERP\SDocumentRow;
use App\ERP\SDocument;
use App\ERP\SItem;
use App\ERP\SUnit;

/**
 * this class import the data of document rows from siie
 */
class SImportDocumentRows {
  protected $webusername;
  protected $webpassword;
  protected $webdbname;
  protected $webcon;

  protected $webhost;

  /**
   * __construct
   *
   * @param string $sHost the name of host to connect
   *                  can be a IP or name of host
   * @param string $sDbName name of data base to read
   */
  function __construct($sHost = '', $sDbName = '')
  {
      $this->webusername = env("SIIE_DB_USER", "");
      $this->webpassword = env("SIIE_DB_PASS", "");
      $this->webdbname = $sDbName;
      $this->webhost = $sHost;
      $this->webcon = mysqli_connect($sHost, $this->webusername, $this->webpassword, $this->webdbname);
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
                ORDER BY id_doc ASC, id_ety ASC";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lWebDocuments = array();
      $lWebItems = array();
      $lWebUnits = array();
      $lRows = array();
      $lRowsToWeb = array();
      $taxRows = new SImportDocumentTaxRows($this->webhost, $this->webdbname);

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
      
      if ($result->num_rows > 0) {
        $lWebDocuments = SDocument::orderBy('id_document', 'ASC')
                                  ->where(function ($query) use ($lYearsId, $iYearId) {
                                        $query->where('year_id', '=', $lYearsId[$iYearId])
                                              ->orWhere('year_id', '=', $lYearsId[$iYearId-1]);
                                    })
                                  ->distinct()
                                  ->get()
                                  ->pluck('id_document', 'external_id');

        $lWebItems = SItem::get()->pluck('id_item', 'external_id');
        $lWebUnits = SUnit::get()->pluck('id_unit', 'external_id');

        $lRows = SDocumentRow::selectRaw('*, CONCAT(document_id, "_", external_id) AS row_key')
                              ->where(function ($query) use ($lYearsId, $iYearId) {
                                    $query->where('year_id', '=', $lYearsId[$iYearId])
                                          ->orWhere('year_id', '=', $lYearsId[$iYearId-1]);
                                })
                              ->get()
                              ->keyBy('row_key');

         // output data of each row
         while($row = $result->fetch_assoc()) {
            if ($lWebDocuments->has($row["id_year"].'_'.$row["id_doc"])) {
              $idDocument = $lWebDocuments[$row["id_year"].'_'.$row["id_doc"]];
              $sKey = $idDocument."_".$row["id_ety"];

             if ($lRows->has($sKey)) {
                if ($row["ts_edit"] > $oImportation->last_importation ||
                      $row["ts_del"] > $oImportation->last_importation) {
                    $oRow = SDocumentRow::where('document_id', $idDocument)
                                    ->where('external_id', $row["id_ety"])
                                    ->orderBy('id_document_row', 'ASC')
                                    ->first();

                    $oRow->concept_key = $row["concept_key"];
                    $oRow->concept = $row["concept"];
                    $oRow->reference = $row["ref"];
                    $oRow->quantity = $row["qty"];
                    $oRow->price_unit = $row["price_u"];
                    $oRow->price_unit_sys = $row["price_u_sys"];
                    $oRow->subtotal = $row["stot_r"];
                    $oRow->tax_charged = $row["tax_charged_r"];
                    $oRow->tax_retained = $row["tax_retained_r"];
                    $oRow->total = $row["tot_r"];
                    $oRow->price_unit_cur = $row["price_u_cur"];
                    $oRow->price_unit_sys_cur = $row["price_u_sys_cur"];
                    $oRow->subtotal_cur = $row["stot_cur_r"];
                    $oRow->tax_charged_cur = $row["tax_charged_cur_r"];
                    $oRow->tax_retained_cur = $row["tax_retained_cur_r"];
                    $oRow->total_cur = $row["tot_cur_r"];
                    $oRow->length = $row["len"];
                    $oRow->surface = $row["surf"];
                    $oRow->volume = $row["vol"];
                    $oRow->mass = $row["mass"];
                    $oRow->is_inventory = $row["b_inv"];
                    $oRow->is_deleted = $row["b_del"];
                    $oRow->external_id = $row["id_ety"];
                    $oRow->item_id = $lWebItems[$row["fid_item"]];
                    $oRow->unit_id = $lWebUnits[$row["fid_unit"]];
                    $oRow->year_id = $lYearsId[$row["id_year"]];
                    $oRow->created_by_id = 1;
                    $oRow->updated_by_id = 1;
                    $oRow->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    $oRow->taxRowsAux = $taxRows->importTaxRows($row["id_year"], $row["id_doc"], $row["id_ety"], $lWebDocuments, $lRows, $oRow->id_document_row);

                    array_push($lRowsToWeb, $oRow);
                }
             }
             else {
                $oRow = SImportDocumentRows::siieToSiieWeb($row, $lWebDocuments, $lYearsId, $lWebItems, $lWebUnits);
                $oRow->taxRowsAux = $taxRows->importTaxRows($row["id_year"], $row["id_doc"], $row["id_ety"], $lWebDocuments, $lRows, 0);
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

      SImportUtils::saveImportation($oImportation, $iYearId);

      return sizeof($lRowsToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  array $oSiieRow Objet of siie DB row
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
