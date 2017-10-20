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
          return $query->join('erpu_item_genders', 'erpu_items.gender_id', '=', 'erpu_item_genders.id_item_gender')
                      ->where('erpu_item_genders.item_class_id', $idType)
                      ->where('erpu_item_genders.is_deleted', '=', \Config::get('scsys.STATUS.ACTIVE'))
                      ->where('erpu_items.name', 'LIKE', "%".$name."%")
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
