<?php namespace App\WMS;

use App\ERP\SModel;

class SExternalTransfer extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_external_transfer';
  protected $table = 'wms_external_transfers';

  protected $fillable = [
                          'id_external_transfer',
                          'is_deleted',
                          'src_branch_id',
                          'des_branch_id',
                          'mvt_reference_id',
                          'created_by_id',
                          'updated_by_id',
                        ];


  public function getTable()
  {
    return $this->table;
  }

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
