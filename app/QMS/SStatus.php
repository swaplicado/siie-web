<?php namespace App\QMS;

use App\ERP\SModel;

class SStatus extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_status';
    protected $table = 'qmss_quality_status';

    protected $fillable = [
                             'id_status',
                              'code',
                              'name',
                              'is_deleted',
                          ];

}
