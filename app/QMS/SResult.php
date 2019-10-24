<?php namespace App\QMS;

use App\ERP\SModel;

class SResult extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_result';
    protected $table = 'qms_results';

    protected $fillable = [
                            'id_result',
                            'dt_date',
                            'result_value',
                            'is_deleted',
                            'lot_id',
                            'analysis_id',
                            'created_by_id',
                            'updated_by_id',
                        ];

    /**
     * [userCreation description]
     * Return object User
     * @return User
    */
    public function userCreation()
    {
        return $this->belongsTo('App\User', 'created_by_id');
    }

    /**
     * [userUpdate description]
     * Return object User
     * @return User
     */
    public function userUpdate()
    {
        return $this->belongsTo('App\User', 'updated_by_id');
    }
}
