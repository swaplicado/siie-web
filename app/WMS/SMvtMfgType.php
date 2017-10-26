<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtMfgType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_mfg_type';
  protected $table = 'wmss_mvt_mfg_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];
}
