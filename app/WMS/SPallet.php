<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SPallet extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_pallet';
  protected $table = 'wms_pallets';

  protected $fillable = [
                          'pallet',
                          'is_deleted',
                          'item_id',
                          'unit_id',
                        ];

  public function getTable()
  {
    return $this->table;
  }

  public function warehouse()
  {
    return $this->belongsTo('App\WMS\SWarehouse', 'whs_id');
  }

  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
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
