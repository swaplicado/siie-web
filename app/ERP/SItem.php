<?php namespace App\ERP;

use App\ERP\NewModel;

class SItem extends NewModel {

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

  public function lot()
  {
    return $this->hasmany('App\WMS\SWmsLot');
  }

  public function pallet()
  {
    return $this->hasmany('App\WMS\SPallet');
  }

  public function scopeSearch($query, $name, $iFilter, $idClass)
  {
    $query->join('erpu_item_genders', 'erpu_items.item_gender_id', '=', 'erpu_item_genders.id_item_gender')
                ->where('erpu_item_genders.item_class_id', $idClass)
                ->where(function ($q) use ($name) {
                      $q->where('erpu_items.name', 'LIKE', "%".$name."%")
                      ->orWhere('erpu_items.code', 'LIKE', "%".$name."%");
                  })
                  ->select('erpu_items.*');

      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
          return $query->where('erpu_items.is_deleted', '=', \Config::get('scsys.STATUS.ACTIVE'));
          break;

        case \Config::get('scsys.FILTER.DELETED'):
        return $query->where('erpu_items.is_deleted', '=', \Config::get('scsys.STATUS.DEL'));
          break;

        case \Config::get('scsys.FILTER.ALL'):
        return $query;
          break;

        default:
        return $query;
          break;
      }
  }

}
