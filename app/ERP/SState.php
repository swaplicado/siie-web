<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SState extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_state';
  protected $table = 'erps_country_states';

  protected $fillable = [
                          'id_state',
                          'code',
                          'name',
                          'abbreviation',
                          'state_lan',
                          'is_deleted',
                          'country_id'
                        ];

  /**
   * [country description]
   * Return object SCountry
   * @return SCountry
   */
  public function country()
  {
    return $this->belongsTo('App\ERP\SCountry',  'country_id');
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
