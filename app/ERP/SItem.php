<?php namespace App\ERP;

use App\ERP\SModel;

class SItem extends SModel {

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

  /**
   * [gender description]
   * Return object SItemGender
   * @return SItemGender
   */
  public function gender()
  {
    return $this->belongsTo('App\ERP\SItemGender', 'item_gender_id');
  }

  /**
   * [unit description]
   * Return object SUnit
   * @return SUnit
   */
  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit');
  }

  /**
   * [lot description]
   * Return object SWmsLot
   * @return SWmsLot
   */
  public function lot()
  {
    return $this->hasmany('App\WMS\SWmsLot');
  }

  /**
   * [pallet description]
   * Return object SPallet
   * @return SPallet
   */
  public function pallet()
  {
    return $this->hasmany('App\WMS\SPallet');
  }

  /**
   * [scopeSearch description]
   * @param  string $query         query to do
   * @param  string $name          variable for where clause
   * @param  integer $iFilter       type of filter
   * @param  integer $iFilterLot    filter for lots
   * @param  integer $iFilterBulk   filter for bulk
   * @param  integer $iFilterGender filter for gender
   * @param  integer $idClass       variable for where clause
   * @return string                query
   */
  public function scopeSearch($query, $name, $iFilter, $iFilterLot, $iFilterBulk, $iFilterGender, $idClass)
  {
      $query->join('erpu_item_genders', 'erpu_items.item_gender_id', '=', 'erpu_item_genders.id_item_gender')
                  ->where('erpu_item_genders.item_class_id', $idClass)
                  ->where(function ($q) use ($name) {
                        $q->where('erpu_items.name', 'LIKE', "%".$name."%")
                        ->orWhere('erpu_items.code', 'LIKE', "%".$name."%");
                    })
                    ->select('erpu_items.*');
     if ($iFilterBulk != \Config::get('scsiie.FILTER_BULK.ALL'))
     {
        $query = $query->where('erpu_items.is_bulk', $iFilterBulk);
     }
     if ($iFilterLot != \Config::get('scsiie.FILTER_LOT.ALL'))
     {
        $query = $query->where('erpu_items.is_lot', $iFilterLot);
     }
     if ($iFilterGender != \Config::get('scsiie.FILTER_GENDER.ALL'))
     {
        $query = $query->where('erpu_items.item_gender_id', $iFilterGender);
     }


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
