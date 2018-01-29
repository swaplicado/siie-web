<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMvtClass extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_class';
  protected $table = 'wmss_mvt_classes';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];

  /**
   * [mvtTypes description]
   * Return object SMvtType
   * @return SMvtType
   */
  public function mvtTypes()
  {
    return $this->hasMany('App\WMS\SMvtTypes');
  }
}
