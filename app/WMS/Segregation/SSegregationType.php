<?php namespace App\WMS\Segregation;

use App\ERP\SModel;

class SSegregationType extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_segregation_type';
    protected $table = 'wmss_segregation_types';

    protected $fillable = [
                            'id_segregation_type',
                            'code',
                            'name',
                            'is_deleted',
                          ];

}
