<?php namespace App\WMS\Segregation;

use App\ERP\SModel;

class SSegregationRow extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_segregation_row';
    protected $table = 'wms_segregation_rows';
    public $timestamps = false;

    protected $fillable = [
                            'id_segregation_row',
                            'quantity_increment',
                            'quantity_decrement',
                            'segregation_id',
                            'pallet_id',
                            'whs_id',
                            'branch_id',
                            'year_id',
                            'item_id',
                            'unit_id',
                            'quality_status_id',
                          ];

    public function segregation()
    {
      return $this->belongsTo('App\WMS\Segregation\SSegregation');
    }

    public function lotRows()
    {
      return $this->hasmany('App\WMS\Segregation\SSegregationRow', 'segregation_row_id', 'id_segregation_row');
    }

}
