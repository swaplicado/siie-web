<?php namespace App\QMS;

use App\ERP\SModel;

class SAnalysisType extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_analysis_type';
    protected $table = 'qmss_analysis_types';

    protected $fillable = [
                             'id_analysis_type',
                              'code',
                              'name',
                              'order',
                              'is_deleted',
                          ];

}
