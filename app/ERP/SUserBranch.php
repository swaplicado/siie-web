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

  public function user()
  {
      return $this->belongsTo('App\User');
  }

  public function branch()
  {
      return $this->belongsTo('App\ERP\SBranch');
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
