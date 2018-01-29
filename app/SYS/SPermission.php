<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SPermission extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_permission';
  protected $table = "syss_permissions";
  protected $fillable = ['id_permission','name', 'code_siie', 'name', 'is_deleted', 'permission_type_id', 'module_id'];

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
   * [coUsPermission description]
   * Return object SCoUsPermission
   * @return SCoUsPermission
   */
  public function coUsPermission()
  {
    return $this->hasMany('App\SYS\SCoUsPermission');
  }

  /**
   * [module description]
   * Return object SModule
   * @return SModule
   */
  public function module()
  {
      return $this->belongsTo('App\SYS\SModule');
  }

  /**
   * [permissionType description]
   * Return object SPermissionType
   * @return [type] [description]
   */
  public function permissionType()
  {
      return $this->belongsTo('App\SYS\SPermissionType');
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
