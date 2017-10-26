<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItemType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_type';
  protected $table = 'erps_item_types';

  protected $fillable = [
                          'name',
                          'id_item_type',
                          'is_deleted',
                          'item_class_id',
                        ];

  public function class()
  {
    return $this->belongsTo('App\ERP\SItemClass');
  }

  public function classTypes($idClass)
  {
    return SItemType::where('item_class_id', '=', $idClass)->get();
  }

}
