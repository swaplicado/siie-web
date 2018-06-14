<?php namespace App\WMS\Segregation;

use App\ERP\SModel;

class SSegregationRow extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_segregation_row';
    protected $table = 'wms_segregation_rows';

    protected $fillable = [
                            'id_segregation_row',
                            'quantity',
                            'is_deleted',
                            'segregation_id',
                            'segregation_mtv_type_id',
                            'segregation_event_id',
                            'branch_id',
                            'whs_id',
                            'pallet_id',
                            'lot_id',
                            'year_id',
                            'item_id',
                            'unit_id',
                            'notes',
                            'created_by_id',
                            'updated_by_id',
                          ];

    /**
     * [segregation description]
     * Return object SSegregation
     * @return SSegregation
     */
    public function segregation()
    {
      return $this->belongsTo('App\WMS\Segregation\SSegregation');
    }

}
