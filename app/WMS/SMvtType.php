<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_whs_type';
  protected $table = 'wmss_mvt_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                          'class_id',
                        ];

  public function mvtClass()
  {
    return $this->belongsTo('App\WMS\SMvtClass');
  }
}
