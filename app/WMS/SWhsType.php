<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SWhsType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_whs_type';
  protected $table = 'wmss_whs_types';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'name',
                          'is_deleted',
                        ];

  /**
   * [warehouses description]
   * Return object SWarehouse
   * @return SWarehouse
   */
  public function warehouses()
  {
    return $this->hasMany('App\WMS\SWarehouse');
  }
}
