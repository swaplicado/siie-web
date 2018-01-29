<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SUnit extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_unit';
  protected $table = 'erpu_units';

  protected $fillable = [
                          'code',
                          'name',
                          'base_unit_equivalence_opt',
                          'external_id',
                          'is_deleted',
                          'base_unit_id_opt',
                        ];

  /**
   * [equivalence description]
   * Return object SUnit
   * @return SUnit
   */
  public function equivalence()
  {
    return $this->belongsTo('App\ERP\SUnit', 'base_unit_id_opt');
  }

  /**
   * [lot description]
   * Return object SWmLot
   * @return SWmLot
   */
  public function lot()
  {
    return $this->hasmany('App\WMS\SWmLot');
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
   * [userCreation description]
   * Return object User
   * @return User
   */
  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  /**
   * [userUpdate description]
   * Return object User
   * @return User
   */
  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query   query to do
   * @param  string $unit    variable for where clause
   * @param  int $iFilter type of filter
   * @return string          query
   */
  public function scopeSearch($query, $unit, $iFilter)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                        ->where('name', 'LIKE', "%".$unit."%");
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                          ->where('name', 'LIKE', "%".$unit."%");
          break;

        case \Config::get('scsys.FILTER.ALL'):
            return $query->where('name', 'LIKE', "%".$unit."%");
          break;

        default:
            return $query->where('name', 'LIKE', "%".$unit."%");
          break;
      }
  }

}
