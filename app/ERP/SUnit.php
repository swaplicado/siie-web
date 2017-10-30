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

  public function equivalence()
  {
    return $this->belongsTo('App\ERP\SUnit', 'base_unit_id_opt');
  }

  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

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
