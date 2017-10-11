<?php namespace App\SERP;

use Illuminate\Database\Eloquent\Model;

class SItemFamily extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_family';
  protected $table = 'erpu_item_families';

  protected $fillable = [
                          'name',
                          'external_id',
                          'is_deleted',
                        ];

  public function groups()
  {
    return $this->hasMany('App\SERP\SItemGroup');
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
