<?php namespace App\ERP;

use App\ERP\SModel;

class SDocumentRowTax extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_row_tax';
  protected $table = 'erpu_doc_row_taxes';
  public $timestamps = false;

  protected $fillable = [
                        'id_row_tax',
                        'percentage',
                        'value_unit',
                        'value',
                        'tax',
                        'tax_currency',
                        'document_row_id',
                        'document_id',
                        'year_id',
                      ];

  /**
   * [document description]
   * Return object SDocument
   * @return SDocument
   */
  public function document()
  {
    return $this->belongsTo('App\ERP\SDocument', 'id_document', 'document_id');
  }

  /**
   * [documentRow description]
   * Return object SDocumentRow
   * @return SDocumentRow
   */
  public function documentRow()
  {
    return $this->belongsTo('App\ERP\SDocumentRow', 'id_document_row', 'document_row_id');
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
   * [userCreation description]
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
