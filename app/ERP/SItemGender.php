<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItemGender extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_gender';
  protected $table = 'erpu_item_genders';

  protected $fillable = [
                          'name',
                          'is_length',
                          'is_length_var',
                          'is_surface',
                          'is_surface_var',
                          'is_volume',
                          'is_volume_var',
                          'is_mass',
                          'is_mass_var',
                          'is_lot',
                          'is_bulk',
                          'is_deleted',
                          'item_group_id',
                          'item_class_id',
                          'item_type_id',
                        ];

  public function group()
  {
    return $this->belongsTo('App\ERP\SItemGroup', 'item_group_id');
  }

  public function class()
  {
    return $this->belongsTo('App\ERP\SItemClass', 'item_class_id');
  }

  public function type()
  {
    return $this->belongsTo('App\ERP\SItemType', 'item_type_id');
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
