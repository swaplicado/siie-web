<?php namespace App\ERP;

use App\ERP\SModel;

class SSignature extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_signature';
    protected $table = 'erp_signatures';

    protected $fillable = [
                            'id_signature',
                            'signed',
                            'signature_type_id',
                            'is_deleted',
                            'signed_by_id'
                        ];

    /**
     * [userCreation description]
     * Return object User
     * @return User
    */
    public function signedBy()
    {
        return $this->belongsTo('App\User', 'signed_by_id');
    }
}
