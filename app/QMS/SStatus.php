<?php namespace App\QMS;

use App\ERP\SModel;

class SStatus extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_segregation_event';
    protected $table = 'qmss_segregation_events';

    protected $fillable = [
                             'id_segregation_event',
                              'code',
                              'name',
                              'is_deleted',
                          ];

}
