<?php namespace App\WMS\Segregation;

use App\ERP\SModel;

class SSegregationMov extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_seg_mov_type_id';
    protected $table = 'wmss_seg_mov_types';

    protected $fillable = [
                            'id_seg_mov_type_id',
                            'code',
                            'name',
                            'is_deleted',
                          ];

}
