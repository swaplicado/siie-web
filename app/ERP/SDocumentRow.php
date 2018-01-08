<?php namespace App\ERP;

use App\ERP\SModel;

class SDocumentRow extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_document_row';
  protected $table = 'erpu_document_rows';
  protected $fillable = [
                          'id_document_row',
                          'concept_key',
                          'concept',
                          'reference',
                          'quantity',
                          'price_unit',
                          'price_unit_sys',
                          'subtotal',
                          'tax_charged',
                          'tax_retained',
                          'total',
                          'price_unit_cur',
                          'price_unit_sys_cur',
                          'subtotal_cur',
                          'tax_charged_cur',
                          'tax_retained_cur',
                          'total_cur',
                          'length',
                          'surface',
                          'volume',
                          'mass',
                          'is_inventory',
                          'is_deleted',
                          'external_id',
                          'item_id',
                          'unit_id',
                          'year_id',
                          'document_id',
                          'created_by_id',
                          'updated_by_id',
                      ];

  public $taxRowsAux = array();

  public function document()
  {
    return $this->belongsTo('App\ERP\SDocument', 'id_document', 'document_id');
  }

  public function item()
  {
    return $this->belongsTo('App\ERP\SItem', 'item_id');
  }

  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit', 'unit_id');
  }

  public function taxRows()
  {
    return $this->hasmany('App\ERP\SDocumentRowTax', 'document_row_id');
  }

  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  public function scopeSearch($query, $name, $iFilter)
  {
      return $query;
  }
}
