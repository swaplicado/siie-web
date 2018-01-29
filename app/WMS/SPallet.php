<?php namespace App\WMS;

use App\ERP\SModel;

class SPallet extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_pallet';
  protected $table = 'wms_pallets';
  public $timestamps = false;


  protected $fillable = [
                          'pallet',
                          'is_deleted',
                          'item_id',
                          'unit_id',
                          'loc_id',
                          'quantity',
                        ];



  public function getTable()
  {
    return $this->table;
  }

  /**
   * [item description]
   * Return Object SItem
   * @return SItem
   */
  public function item()
  {
    return $this->belongsTo('App\ERP\SItem');
  }

  /**
   * [unit description]
   * Return Object SUnit
   * @return SUnit
   */
  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit');
  }

  /**
   * [loc description]
   * Return object SLocation
   * @return SLocation
   */
  public function loc()
  {
    return $this->belongsTo('App\WMS\SLocation');
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
                        ->where('pallet', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                          ->where('pallet', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.ALL'):
            return $query->where('pallet', 'LIKE', "%".$name."%");
          break;

        default:
            return $query->where('pallet', 'LIKE', "%".$name."%");
          break;
      }
  }

}
