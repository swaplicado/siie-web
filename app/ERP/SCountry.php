<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SCountry extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_country';
  protected $table = 'erps_countries';

  protected $fillable = [
                          'id_country',
                          'name',
                          'abbreviation',
                          'country_lan',
                          'is_deleted'
                        ];
  /**
   * [states description]
   * Return object SState
   * @return SState
   */
  public function states()
  {
    return $this->hasMany('App\ERP\SState', 'country_id', 'id_country');
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
   * @param  string $name    variable for where clause
   * @param  integer $iFilter type of filter
   * @return string        query
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
