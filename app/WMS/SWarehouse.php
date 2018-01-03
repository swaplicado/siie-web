<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;
use App\WMS\SLocation;

class SWarehouse extends Model {

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
                          'is_deleted',
                          'branch_id',
                          'whs_type_id',
                        ];

  public function whsType()
  {
    return $this->belongsTo('App\WMS\SWhsType', 'whs_type_id');
  }

  public function branch()
  {
    return $this->belongsTo('App\ERP\SBranch', 'branch_id');
  }

  public function locations()
  {
    return $this->hasMany('App\ERP\SLocation');
  }

  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  public function userWhs()
  {
    return $this->hasMany('App\ERP\SUserWhs');
  }

  public function getDefaultLocation()
  {
     return (SLocation::where('whs_id', $this->id_whs)->where('is_default', true)->get())[0];
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
