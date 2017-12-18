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

  public function document()
  {
    return $this->belongsTo('App\ERP\SDocument', 'id_document', 'document_id');
  }

  public function documentRow()
  {
    return $this->belongsTo('App\ERP\SDocumentRow', 'id_document_row', 'document_row_id');
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
