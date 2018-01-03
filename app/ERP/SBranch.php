<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SBranch extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_branch';
  protected $table = 'erpu_branches';
  protected $fillable = ['id_branch', 'code', 'name', 'external_id', 'is_headquarters', 'is_deleted'];

  public function company()
  {
    return $this->belongsTo('App\ERP\SPartner');
  }

  public function warehouse()
  {
    return $this->hasmany('App\WMS\SWarehouse');
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

  public function userBranches()
  {
    return $this->hasMany('App\ERP\SUserBranch');
  }

  public function scopeSearch($query, $name, $iFilter)
  {
      $query->join('erpu_partners', 'erpu_partners.id_partner', '=', 'erpu_branches.partner_id')
                ->select('erpu_branches.*')
                ->where(function ($q) use ($name) {
                      $q->where('erpu_partners.name', 'LIKE', "%".$name."%")
                      ->orWhere('erpu_branches.name', 'LIKE', "%".$name."%");
                  });

      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
          return $query->where('erpu_partners.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
          break;

        case \Config::get('scsys.FILTER.DELETED'):
          return $query->where('erpu_partners.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
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
