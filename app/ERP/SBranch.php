<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SBranch extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_branch';
  protected $table = 'erpu_branches';
  protected $fillable = ['id_branch', 'code', 'name', 'external_id', 'is_headquarters'];

  public function company()
  {
    return $this->belongsTo('App\ERP\SPartner');
  }

  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  public function partner()
  {
    return $this->belongsTo('App\ERP\SPartner', 'partner_id');
  }

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
