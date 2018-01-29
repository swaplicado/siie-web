<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_type';
  protected $table = 'wmss_mvt_types';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                          'mvt_class_id',
                        ];

  /**
   * [mvtClass description]
   * Return object SMvtClass
   * @return SMvtClass
   */
  public function mvtClass()
  {
    return $this->belongsTo('App\WMS\SMvtClass');
  }
}
