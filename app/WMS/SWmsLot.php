<?php

namespace App\WMS;

use App\ERP\SModel;

class SWmsLot extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_lot';
  protected $table = "wms_lots";

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'lot',
                          'dt_expiry',
                          'item_id',
                          'unit_id',
                        ];

  /**
   * [item description]
   * Return object SItem
   * @return SItem
   */
  public function item()
  {
    return $this->belongsTo('App\ERP\SItem');
  }

  /**
   * [unit description]
   * Return object SUnit
   * @return SUnit
   */
  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit');
  }

  /**
   * [userCreation description]
   * Return object User
   * @return User
   */
  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  /**
   * [userUpdate description]
   * Return object User
   * @return User
   */
  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query   query to do
   * @param  string $name    where clause
   * @param  integer $iFilter type of filter
   * @return string          query
   */
  public function scopeSearch($query, $name, $iFilter)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                        ->where('lot', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                          ->where('lot', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.ALL'):
            return $query->where('lot', 'LIKE', "%".$name."%");
          break;

        default:
            return $query->where('lot', 'LIKE', "%".$name."%");
          break;
      }
  }


}
