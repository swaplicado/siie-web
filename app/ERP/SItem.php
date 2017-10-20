<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItem extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item';
  protected $table = 'erpu_items';

  protected $fillable = [
                          'code',
                          'name',
                          'length',
                          'surface',
                          'volume',
                          'mass',
                          'external_id',
                          'is_lot',
                          'is_bulk',
                          'is_deleted',
                          'gender_id',
                          'unit_id',
                        ];

  public function gender()
  {
    return $this->belongsTo('App\ERP\SItemGender');
  }

  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit');
  }

  public function scopeSearch($query, $name, $iFilter, $idType)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
          return $query->join('erps_item_types', 'erpu_items.id_item', '=', 'erps_item_types.id_item_type')
                      ->where('erps_item_types.id_item_type', $idType)
                      ->where('erps_item_types.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                      ->where('erps_item_types.name', 'LIKE', "%".$name."%")
                      ->select('erpu_items.*');
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
