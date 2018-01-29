<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItemClass extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_class';
  protected $table = 'erps_item_classes';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'name',
                          'is_deleted',
                        ];

  /**
   * [types description]
   * Return object SItemType
   * @return SItemType
   */
  public function types()
  {
    return $this->hasMany('App\ERP\SItemType');
  }

}
