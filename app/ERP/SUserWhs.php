<?php

namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SUserWhs extends Model
{
  protected $connection = 'siie';
  protected $primaryKey = 'id_access_whs';
  protected $table = "erpu_access_whs";
  public $timestamps = false;
  protected $fillable = ['id_access_whs','user_id','whs_id'];

  public function user()
  {
      return $this->belongsTo('App\User');
  }

  public function warehouses()
  {
      return $this->belongsTo('App\ERP\SWarehouse');
  }

  public function scopeSearch($query, $iFilter)
    {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ALL'):
          return $query;
          break;

        default:
          return $query;
          break;
      }
  }
}
