<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SWhsType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_type';
  protected $table = "wmss_whs_types";

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];

  public function warehouses()
  {
    return $this->hasMany('App\WMS\SWarehouse');
  }

}