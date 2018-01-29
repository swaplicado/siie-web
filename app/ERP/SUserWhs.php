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

  /**
   * [user description]
   * Return object User
   * @return User
   */
  public function user()
  {
      return $this->belongsTo('App\User');
  }

  /**
   * [warehouses description]
   * Return object SWarehouse
   * @return SWarehouse
   */
  public function warehouses()
  {
      return $this->belongsTo('App\WMS\SWarehouse','whs_id');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query   query to do
   * @param  integer $iFilter type of filter
   * @return string          query
   */
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
