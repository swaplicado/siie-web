<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtTrnType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_type';
  protected $table = 'wmss_mvt_trn_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];
}
