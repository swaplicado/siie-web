<?php namespace App\SERP;

use Illuminate\Database\Eloquent\Model;

class SItemLinkType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_link_type';
  protected $table = 'erps_item_link_types';

  public static function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'name',
                          'is_deleted',
                        ];

}
