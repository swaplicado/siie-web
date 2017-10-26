<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SPermissionType extends Model {

    protected $connection = 'ssystem';
    protected $primaryKey = 'id_permission_type';
    protected $table = "syss_permission_types";
    protected $fillable = ['id_permission_type', 'name'];

    public function permissions()
    {
      return $this->hasMany('App\ERP\SPermission');
    }

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
