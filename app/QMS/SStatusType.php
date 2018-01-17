<?php namespace App\QMS;

use App\ERP\SModel;

use App\WMS\SLocation;
use App\WMS\SWarehouse;
use App\ERP\SBranch;
use App\ERP\SPartner;

class SStatusType extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_status_type';
    protected $table = 'qmss_status_types';

    protected $fillable = [
                             'id_status_type',
                              'code',
                              'name',
                              'is_deleted',
                          ];

}
