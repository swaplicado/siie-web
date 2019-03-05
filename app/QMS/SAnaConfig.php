<?php namespace App\QMS;

use App\ERP\SModel;

class SAnaConfig extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_config';
    protected $table = 'qms_ana_configs';

    protected $fillable = [
                            'id_config',
                            'analysis_id',
                            'item_link_type_id',
                            'item_link_id',
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
