<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMovementRowLot extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_row_lot';
  protected $table = 'wms_mvt_row_lots';
  public $timestamps = false;

  protected $fillable = [
                          'id_mvt_row_lot',
                          'quantity',
                          'amount_unit',
                          'amount',
                          'length',
                          'surface',
                          'volume',
                          'mass',
                          'mvt_row_id',
                          'lot_id'
                        ];

  public function row()
  {
    return $this->belongsTo('App\WMS\SMovementRow', 'id_mvt_row', 'mvt_row_id');
  }
}