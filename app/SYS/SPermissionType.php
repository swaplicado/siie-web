<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SPermissionType extends Model {

    protected $connection = 'ssystem';
    protected $primaryKey = 'id_permission_type';
    protected $table = "syss_permission_types";
    protected $fillable = ['id_permission_type', 'name'];

    /**
     * [permissions description]
     * Return object SPermission
     * @return [type] [description]
     */
    public function permissions()
    {
      return $this->hasMany('App\ERP\SPermission');
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
