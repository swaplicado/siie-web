<?php namespace App\MMS;

use App\ERP\SModel;

class SProductionPlan extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_production_plan';
    protected $table = 'mms_production_planes';

    protected $fillable = [
                            'id_production_plan',
                            'folio',
                            'production_plan',
                            'dt_start',
                            'dt_end',
                            'is_deleted',
                            'floor_id',
                            'created_by_id',
                            'updated_by_id',
                          ];

}
