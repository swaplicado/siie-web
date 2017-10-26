<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtExpType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_exp_type';
  protected $table = 'wmss_mvt_exp_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];
}
