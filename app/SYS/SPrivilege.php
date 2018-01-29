<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SPrivilege extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_privilege';
  protected $table = "syss_privileges";
  protected $fillable = ['id_privilege','name'];

  /**
   * [userPermission description]
   * Return object SUserPermission
   * @return SUserPermission
   */
  public function userPermission()
  {
      return $this->hasMany('App\SYS\SUserPermission');
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
        return $query->where('name', 'LIKE', "%$name%");
    }
}
