<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtInternalType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_internal_type';
  protected $table = 'wmss_mvt_internal_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];
}
