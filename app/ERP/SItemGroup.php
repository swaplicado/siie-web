<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SItemGroup extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_item_group';
  protected $table = 'erpu_item_groups';

  public $aux_fam_id = 0;

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'name',
                          'external_id',
                          'is_deleted',
                          'item_family_id',
                        ];

  /**
   * [genders description]
   * Return object SItemGender
   * @return SItemGender
   */
  public function genders()
  {
    return $this->hasMany('App\ERP\SItemGender');
  }

  /**
   * [family description]
   * Return object SItemFamily
   * @return SItemFamily
   */
  public function family()
  {
    return $this->belongsTo('App\ERP\SItemFamily', 'item_family_id');
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
