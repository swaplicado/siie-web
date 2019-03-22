<?php namespace App\QMS;

use App\ERP\SModel;

class SAnalysis extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_analysis';
    protected $table = 'qms_analysis';

    protected $fillable = [
                            'id_analysis',
                            'code',
                            'name',
                            'standard',
                            'min_value',
                            'max_value',
                            'specification',
                            'is_deleted',
                            'type_id',
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
