<?php namespace App\SERP;

use Illuminate\Database\Eloquent\Model;

class SItemType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_type';
  protected $table = 'erps_item_types';

  protected $fillable = [
                          'name',
                          'id_type',
                          'is_deleted',
                          'class_id',
                        ];

  public function class()
  {
    return $this->belongsTo('App\SERP\SItemClass');
  }

  public function classTypes($idClass)
  {
    return SItemType::where('class_id', '=', $idClass)->get();
  }

}
