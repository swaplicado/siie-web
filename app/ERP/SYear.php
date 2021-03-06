<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SYear extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_year';
  protected $table = "erpu_years";
  protected $fillable = ['id_year', 'year', 'is_closed'];

  /**
   * [months description]
   * Return object SMonth
   * @return SMonth
   */
  public function months()
  {
    return $this->hasMany('App\ERP\SMonth', 'year_id', 'id_year');
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
   * @param  integer $id      where clause
   * @param  integer $iFilter type of filter
   * @return string          query
   */
  public function scopeSearch($query, $id, $iFilter)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
          return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                      ->where('id_year', 'LIKE', "%".$id."%");
          break;

        case \Config::get('scsys.FILTER.DELETED'):
          return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                        ->where('id_year', 'LIKE', "%".$id."%");
          break;

        case \Config::get('scsys.FILTER.ALL'):
          return $query->where('id_year', 'LIKE', "%".$id."%");
          break;

        default:
          return $query->where('id_year', 'LIKE', "%".$id."%");
          break;
      }
  }
}
