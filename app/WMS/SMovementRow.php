<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMovementRow extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_row';
  protected $table = 'wms_mvt_rows';
  public $timestamps = false;
  protected $auxLots = array();

  protected $fillable = [
                          'id_mvt_row',
                          'quantity',
                          'amount_unit',
                          'amount',
                          'length',
                          'surface',
                          'volume',
                          'mass',
                          'is_deleted',
                          'mvt_id',
                          'item_id',
                          'unit_id',
                          'pallet_id',
                          'location_id',
                          'doc_order_row_id',
                          'doc_invoice_row_id',
                          'doc_debit_note_row_id',
                          'doc_credit_note_row_id'
                        ];

  public function movement()
  {
    return $this->belongsTo('App\WMS\SMovement', 'id_mvt', 'mvt_id');
  }

  public function lotRows()
  {
    return $this->hasMany('App\WMS\SMovementRowLot', 'mvt_row_id');
  }

  public function item()
  {
    return $this->belongsTo('App\ERP\SItem', 'item_id');
  }

  public function pallet()
  {
    return $this->belongsTo('App\WMS\SPallet', 'pallet_id');
  }

  public function setAuxLots($value = array())
  {
     $this->auxLots = $value;
  }

  public function getAuxLots()
  {
     return $this->auxLots;
  }

}
