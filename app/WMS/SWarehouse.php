<?php namespace App\WMS;

use App\ERP\SModel;

use App\WMS\SLocation;

class SWarehouse extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_whs';
  protected $table = 'wmsu_whs';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'code',
                          'name',
                          'is_quality',
                          'is_deleted',
                          'branch_id',
                          'whs_type_id',
                        ];

  /**
   * [whsType description]
   * Return object SWhsType
   * @return SWhsType
   */
  public function whsType()
  {
    return $this->belongsTo('App\WMS\SWhsType', 'whs_type_id');
  }

  /**
   * [branch description]
   * Return object SBranch
   * @return SBranch
   */
  public function branch()
  {
    return $this->belongsTo('App\ERP\SBranch', 'branch_id');
  }

  /**
   * [locations description]
   * Return object SLocation
   * @return SLocation
   */
  public function locations()
  {
    return $this->hasMany('App\WMS\SLocation', 'whs_id');
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
   * [userWhs description]
   * Return object SUserWhs
   * @return SUserWhs
   */
  public function userWhs()
  {
    return $this->hasMany('App\ERP\SUserWhs');
  }

  /**
   * [getDefaultLocation description]
   * Return object SLocation
   * @return SLocation
   */
  public function getDefaultLocation()
  {
     $oLocation = SLocation::where('whs_id', $this->id_whs)
                            ->where('is_default', true)
                            ->first();
     return $oLocation;
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
