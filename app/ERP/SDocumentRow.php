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

  /**
   * [document description]
   * Return object SDocument
   * @return SDocument
   */
  public function document()
  {
    return $this->belongsTo('App\ERP\SDocument', 'document_id');
  }

  /**
   * [item description]
   * Return object SItem
   * @return SItem
   */
  public function item()
  {
    return $this->belongsTo('App\ERP\SItem', 'item_id');
  }

  /**
   * [unit description]
   * Return object SUnit
   * @return SUnit
   */
  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit', 'unit_id');
  }

  /**
   * [taxRows description]
   * Return object SDocumentRowTax
   * @return SDocumentRowTax
   */
  public function taxRows()
  {
    return $this->hasmany('App\ERP\SDocumentRowTax', 'document_row_id');
  }

  /**
   * [userCreation description]
   * Return object User
   * @return User
   */
  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  /**
   * [userUpdate description]
   * Return object User
   * @return User
   */
  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  /**
   * [scopeSearch description]
   * @param  string $query   query to do
   * @param  string $name    not used at the moment
   * @param  integer $iFilter not used at the moment
   * @return string          query
   */
  public function scopeSearch($query, $name, $iFilter)
  {
      return $query;
  }
}
