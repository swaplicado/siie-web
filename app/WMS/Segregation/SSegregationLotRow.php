<?php namespace App\WMS\Segregation;

use App\ERP\SModel;

class SSegregationLotRow extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_segregation_row';
    protected $table = 'wms_seg_lot_rows';
    public $timestamps = false;

    protected $fillable = [
                            'id_seg_lot_row',
                            'quantity',
                            'move_type_id',
                            'segregation_row_id',
                            'lot_id',
                            'year_id',
                            'item_id',
                            'unit_id',
                            'quality_status_id',
                          ];

  /**
   * [rows description]
   * Return object SSegregartionRow
   * @return SSegregationRow
  */
    public function row()
    {
      return $this->belongsTo('App\WMS\Segregation\SSegregationRow');
    }

}
