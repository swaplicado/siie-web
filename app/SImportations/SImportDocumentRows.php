<?php namespace App\SImportations;

use App\ERP\SDocumentRow;
use App\ERP\SDocument;
use App\ERP\SItem;
use App\ERP\SUnit;

/**
 *
 */
class SImportDocumentRows
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
      $sql = "SELECT * FROM trn_dps_ety WHERE id_year = ".$iYearId." AND ts_new  ".$sOperator." '".$iYearId."-06-31';";
      $result = $this->webcon->query($sql);
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
      $taxRows = new SImportDocumentTaxRows('erp_universal');

      $lYears = [
        '1' => '2016',
        '2' => '2017',
        '3' => '2018',
      ];

      $lYearsId = [
        '2016' => '1',
        '2017' => '2',
        '2018' => '3',
      ];

      foreach ($lWebRows as $key => $value)
      {
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

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             $sKey = ''.$lWebDocuments[$row["id_doc"]].$row["id_year"].$row["id_ety"];
             if (array_key_exists($sKey, $lRows))
             {
                if ($row["ts_edit"] > $lRows[$sKey]->updated_at ||
                      $row["ts_del"] > $lRows[$sKey]->updated_at)
                {
                    $lRows[$sKey]->code = $row["code"];

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
                    $lRows[$sKey]->document_id = $lWebDocuments[$row["id_doc"]];
                    $lRows[$sKey]->created_by_id = 1;
                    $lRows[$sKey]->updated_by_id = 1;
                    $lRows[$sKey]->created_at = $row["ts_new"];
                    $lRows[$sKey]->updated_at = $row["ts_edit"];

                    $lRows[$sKey]->taxRowsAux = $taxRows->importTaxRows($row["id_year"], $row["id_doc"], $row["id_ety"], $lWebDocuments, $lRows);

                    array_push($lRowsToWeb, $lRows[$sKey]);
                }
             }
             else
             {
                $oRow = SImportDocumentRows::siieToSiieWeb($row, $lWebDocuments, $lYearsId, $lWebItems, $lWebUnits);
                $oRow->taxRowsAux = $taxRows->importTaxRows($row["id_year"], $row["id_doc"], $row["id_ety"], $lWebDocuments, $lRows);
                array_push($lRowsToWeb, $oRow);
             }
         }
      }
      else
      {
         echo "0 results";
      }

      $this->webcon->close();

      foreach ($lRowsToWeb as $key => $oRow) {
         $oRowCopy = clone $oRow;
         $oRow->save();
         $oRow->taxRows()->saveMany($oRowCopy->taxRowsAux);
      }

      return true;
  }

  private static function siieToSiieWeb($oSiieRow = '', $lWebDocuments, $lYearsId, $lWebItems, $lWebUnits)
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
    $oRow->document_id = $lWebDocuments[$oSiieRow["id_doc"]];
    $oRow->created_by_id = 1;
    $oRow->updated_by_id = 1;
    $oRow->created_at = $oSiieRow["ts_new"];
    $oRow->updated_at = $oSiieRow["ts_edit"];

     return $oRow;
  }
}