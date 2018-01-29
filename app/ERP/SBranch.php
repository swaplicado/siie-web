<?php

namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SBranch extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_branch';
  protected $table = 'erpu_branches';
  protected $fillable = ['id_branch', 'code', 'name', 'external_id', 'is_headquarters', 'is_deleted'];

  /**
   * [company description]
   * Return object SPartner
   * @return SPartner
   */
  public function company()
  {
    return $this->belongsTo('App\ERP\SPartner');
  }

  /**
   * [warehouse description]
   * Return object SWarehouse
   * @return SWarehouse
   */
  public function warehouse()
  {
    return $this->hasmany('App\WMS\SWarehouse');
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
   * [partner description]
   * Return object SPartner
   * @return SPartner
   */
  public function partner()
  {
    return $this->belongsTo('App\ERP\SPartner', 'partner_id');
  }

  /**
   * [userBranches description]
   * Return object SUserBranch
   * @return SUserBranch
   */
  public function userBranches()
  {
    return $this->hasMany('App\ERP\SUserBranch');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query   query to do
   * @param  string $name    variable for where clause
   * @param  integer $iFilter type of filter
   * @return string        query
   */
  public function scopeSearch($query, $name, $iFilter)
  {
      $select = 'erpu_branches.*';
      $query->join('erpu_partners', 'erpu_partners.id_partner', '=', 'erpu_branches.partner_id')
                ->where(function ($q) use ($name) {
                      $q->where('erpu_partners.name', 'LIKE', "%".$name."%")
                      ->orWhere('erpu_branches.name', 'LIKE', "%".$name."%");
                  })
                  ->select(\DB::raw($select));

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
