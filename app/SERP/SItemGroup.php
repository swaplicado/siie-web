<?php namespace App\SERP;

use Illuminate\Database\Eloquent\Model;

class SItemGroup extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_group';
  protected $table = 'erpu_item_groups';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'name',
                          'external_id',
                          'is_deleted',
                          'item_family_id',
                        ];

  public function genders()
  {
    return $this->hasMany('App\SERP\SItemGender');
  }

  public function family()
  {
    return $this->belongsTo('App\SERP\SItemFamily', 'item_family_id');
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
