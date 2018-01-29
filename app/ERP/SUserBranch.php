<?php

namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SUserBranch extends Model
{
  protected $connection = 'siie';
  protected $primaryKey = 'id_access_branch';
  protected $table = "erpu_access_branch";
  public $timestamps = false;
  protected $fillable = ['id_access_branch','user_id','branch_id','is_universal'];

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
   * [branch description]
   * Return object SBranch
   * @return SBranch
   */
  public function branch()
  {
      return $this->belongsTo('App\ERP\SBranch');
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
