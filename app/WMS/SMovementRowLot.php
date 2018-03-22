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
                          'is_deleted',
                          'mvt_row_id',
                          'lot_id'
                        ];

  /**
   * [row description]
   * Return object SMovementRow
   * @return SMovementRow
   */
  public function row()
  {
    return $this->belongsTo('App\WMS\SMovementRow', 'id_mvt_row', 'mvt_row_id');
  }

  /**
   * [lot description]
   * Return object SWmsLot
   * @return SWmsLot
   */
  public function lot()
  {
    return $this->belongsTo('App\WMS\SWmsLot', 'lot_id');
  }
}
