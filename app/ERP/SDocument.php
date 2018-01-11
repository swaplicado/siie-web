<?php namespace App\ERP;

use App\ERP\SModel;

class SDocument extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_document';
  protected $table = 'erpu_documents';

  protected $fillable = [
                          'id_document',
                          'dt_date',
                          'dt_doc',
                          'num',
                          'subtotal',
                          'tax_charged',
                          'tax_retained',
                          'total',
                          'exchange_rate',
                          'exchange_rate_sys',
                          'subtotal_cur',
                          'tax_charged_cur',
                          'tax_retained_cur',
                          'total_cur',
                          'is_closed',
                          'is_deleted',
                          'year_id',
                          'doc_category_id',
                          'doc_class_id',
                          'doc_type_id',
                          'doc_status_id',
                          'doc_src_id',
                          'currency_id',
                          'partner_id',
                          'created_by_id',
                          'updated_by_id',
                        ];

  public function rows()
  {
    return $this->hasmany('App\ERP\SDocumentRow', 'document_id');
  }

  public function movementsOfOrder()
  {
    return $this->hasmany('App\WMS\SMovement', 'doc_order_id');
  }

  public function movementsOfInvoice()
  {
    return $this->hasmany('App\WMS\SMovement', 'doc_invoice_id');
  }

  public function partner()
  {
    return $this->belongsTo('App\ERP\SPartner', 'partner_id');
  }

  public function sourceDocument()
  {
    return $this->belongsTo('App\ERP\SDocument', 'doc_src_id');
  }

  public function currency()
  {
    return $this->belongsTo('App\ERP\SCurrency', 'currency_id');
  }

  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  public function scopeSearch($query, $iDocCategory, $iDocumentClass)
  {
      $query->join('erpu_partners', 'erpu_documents.partner_id', '=', 'erpu_partners.id_partner')
                  ->where('doc_category_id', $iDocCategory)
                  ->where('doc_class_id', $iDocumentClass);
  }

}
