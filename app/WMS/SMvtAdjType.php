<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtAdjType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_adj_type';
  protected $table = 'wmss_mvt_adj_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];
}
