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

  /**
   * [items description]
   * Return object SItem
   * @return SItem
   */
  public function items()
  {
    return $this->hasMany('App\ERP\SItem', 'id_item');
  }

  /**
   * [group description]
   * Return object SItemGroup
   * @return SItemGroup
   */
  public function group()
  {
    return $this->belongsTo('App\ERP\SItemGroup', 'item_group_id');
  }


  /**
   * [itemClass description]
   * Return object SItemClass
   * @return SItemClass
   */
  public function itemClass()
  {
    return $this->belongsTo('App\ERP\SItemClass', 'item_class_id');
  }

  /**
   * [type description]
   * Return object SItemType
   * @return SItemType
   */
  public function type()
  {
    return $this->belongsTo('App\ERP\SItemType', 'item_type_id');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query    query to do
   * @param  string $name     variable for where class
   * @param  integer $iFilter  type of filter
   * @param  integer $iClassId if search by item_class_id
   * @return string           query
   */
  public function scopeSearch($query, $name, $iFilter, $iClassId)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                        ->where('item_class_id', $iClassId)
                        ->where('name', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                          ->where('item_class_id', $iClassId)
                          ->where('name', 'LIKE', "%".$name."%");
          break;

        case \Config::get('scsys.FILTER.ALL'):
            return $query->where('name', 'LIKE', "%".$name."%")
                            ->where('item_class_id', $iClassId);
          break;

        default:
            return $query->where('name', 'LIKE', "%".$name."%")
                          ->where('item_class_id', $iClassId);
          break;
      }
  }

}
