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
                          'item_gender_id',
                          'unit_id',
                        ];

  public function gender()
  {
    return $this->belongsTo('App\ERP\SItemGender', 'item_gender_id');
  }

  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit');
  }

  public function scopeSearch($query, $name, $iFilter, $idClass)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
          return $query->join('erpu_item_genders', 'erpu_items.item_gender_id', '=', 'erpu_item_genders.id_item_gender')
                      ->where('erpu_item_genders.item_class_id', $idClass)
                      ->where('erpu_items.is_deleted', '=', \Config::get('scsys.STATUS.ACTIVE'))
                      ->where('erpu_items.name', 'LIKE', "%".$name."%")
                      ->select('erpu_items.*');
          break;

        case \Config::get('scsys.FILTER.DELETED'):
        return $query->join('erpu_item_genders', 'erpu_items.item_gender_id', '=', 'erpu_item_genders.id_item_gender')
                    ->where('erpu_item_genders.item_class_id', $idClass)
                    ->where('erpu_items.is_deleted', '=', \Config::get('scsys.STATUS.DEL'))
                    ->where('erpu_items.name', 'LIKE', "%".$name."%")
                    ->select('erpu_items.*');
          break;

        case \Config::get('scsys.FILTER.ALL'):
        return $query->join('erpu_item_genders', 'erpu_items.item_gender_id', '=', 'erpu_item_genders.id_item_gender')
                    ->where('erpu_item_genders.item_class_id', $idClass)
                    ->where('erpu_items.name', 'LIKE', "%".$name."%")
                    ->select('erpu_items.*');
          break;

        default:
        return $query->join('erpu_item_genders', 'erpu_items.item_gender_id', '=', 'erpu_item_genders.id_item_gender')
                    ->where('erpu_item_genders.item_class_id', $idClass)
                    ->where('erpu_items.is_deleted', '=', \Config::get('scsys.STATUS.ACTIVE'))
                    ->where('erpu_items.name', 'LIKE', "%".$name."%")
                    ->select('erpu_items.*');
          break;
      }
  }

}
