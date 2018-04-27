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
                          'service_num',
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

  /**
   * [rows description]
   * Return object SDocumentRow
   * @return SDocumentRow
   */
  public function rows()
  {
    return $this->hasmany('App\ERP\SDocumentRow', 'document_id');
  }

  /**
   * [movementsOfOrder description]
   * Return object SMovement
   * @return SMovement
   */
  public function movementsOfOrder()
  {
    return $this->hasmany('App\WMS\SMovement', 'doc_order_id');
  }

  /**
   * [movementsOfInvoice description]
   * Return object SMovement
   * @return SMovement
   */
  public function movementsOfInvoice()
  {
    return $this->hasmany('App\WMS\SMovement', 'doc_invoice_id');
  }

  /**
   * [partner description]
   * Return object SPartner
   * @return SPartner
   */
  public function partner()
  {
    return $this->belongsTo('App\ERP\SPartner', 'partner_id');
  }

  /**
   * [sourceDocument description]
   * Return object SDocument
   * @return SDocument
   */
  public function sourceDocument()
  {
    return $this->belongsTo('App\ERP\SDocument', 'doc_src_id');
  }

  /**
   * [currency description]
   * Return object SCurrency
   * @return SCurrency
   */
  public function docClass()
  {
    return $this->belongsTo('App\ERP\SDocumentClass', 'doc_class_id');
  }

  /**
   * [currency description]
   * Return object SCurrency
   * @return SCurrency
   */
  public function currency()
  {
    return $this->belongsTo('App\ERP\SCurrency', 'currency_id');
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
   * to search in a query
   * @param  string $query          query to do
   * @param  string $iDocCategory   variable for where clause
   * @param  string $iDocumentClass variable for where clause
   * @return string                 query
   */
  public function scopeSearch($query, $iDocCategory, $iDocumentClass)
  {
      $query->join('erpu_partners', 'erpu_documents.partner_id', '=', 'erpu_partners.id_partner')
                  ->where('doc_category_id', $iDocCategory)
                  ->where('doc_class_id', $iDocumentClass);
  }

}
