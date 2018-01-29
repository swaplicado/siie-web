<?php namespace App\WMS\Segregation;

use App\ERP\SModel;

class SSegregation extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_segregation';
    protected $table = 'wms_segregations';

    protected $fillable = [
                          'id_segregation',
                          'dt_date',
                          'is_deleted',
                          'segregation_type_id',
                          'reference_id',
                          'created_by_id',
                          'updated_by_id',
                          ];

    /**
     * [rows description]
     * Return object SSegregartionRow
     * @return SSegregationRow
     */
    public function rows()
    {
      return $this->hasmany('App\WMS\Segregation\SSegregationRow', 'segregation_id', 'id_segregation');
    }

}
