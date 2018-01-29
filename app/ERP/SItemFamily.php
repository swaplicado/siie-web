<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItemFamily extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_family';
  protected $table = 'erpu_item_families';

  protected $fillable = [
                          'name',
                          'external_id',
                          'is_deleted',
                        ];

  /**
   * [groups description]
   * Return object SItemGroup
   * @return SItemGroup
   */
  public function groups()
  {
    return $this->hasMany('App\ERP\SItemGroup');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query   query to do
   * @param  string $name    variable for where clause
   * @param  integer $iFilter type of filter
   * @return string          query
   */
  public function scopeSearch($query, $name, $iFilter)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                        ->where('name', 'LIKE', "%".$name."%");
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
