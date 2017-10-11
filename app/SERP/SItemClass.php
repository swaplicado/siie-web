<?php namespace App\SERP;

use Illuminate\Database\Eloquent\Model;

class SItemClass extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_class';
  protected $table = 'erps_item_classes';

  public static function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'name',
                          'is_deleted',
                        ];

  public function types()
  {
    return $this->hasMany('App\SERP\SItemType');
  }

}
