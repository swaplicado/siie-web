<?php namespace App\WMS;

use App\ERP\SModel;

class SLocation extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_whs_location';
  protected $table = 'wmsu_whs_locations';

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                          'whs_id',
                        ];


  public function getTable()
  {
    return $this->table;
  }

  /**
   * [getTable description]
   * Return object SWarehouse
   * @return SWarehouse
   */
  public function warehouse()
  {
    return $this->belongsTo('App\WMS\SWarehouse', 'whs_id');
  }

  /**
   * [pallet description]
   * Return object SPallet
   * @return SPallet
   */
  public function pallet()
  {
    return $this->hasmany('App\WMS\SPallet');
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
                        ->where('name', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                          ->where('name', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.ALL'):
            return $query->where('name', 'LIKE', "%".$name."%");
          break;

        default:
            return $query->where('name', 'LIKE', "%".$name."%");
          break;
      }
  }

}
