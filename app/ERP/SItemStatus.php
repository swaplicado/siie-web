<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItemStatus extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_status';
  protected $table = 'erps_item_status';

  protected $fillable = [
                          'id_item_status',
                          'code',
                          'name',
                          'is_deleted',
                        ];

  /**
   * [genders description]
   * Return object SItem
   * @return SItem
   */
  public function items()
  {
    return $this->hasMany('App\ERP\SItem');
  }

}
