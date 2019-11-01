<?php namespace App\ERP;

use App\ERP\SModel;

class SAuthorization extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_authorization';
    protected $table = 'erp_sign_autorizations';

    protected $fillable = [
                            'id_authorization',
                            'is_deleted',
                            'user_id',
                            'signature_type_id',
                            'created_by_id',
                            'updated_by_id'
                        ];
}
