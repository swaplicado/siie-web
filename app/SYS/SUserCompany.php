<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SUserCompany extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_user_access';
  protected $table = "user_access";
  public $timestamps = false;
  protected $fillable = ['id_user_access','user_id','company_id'];

  public function user()
  {
      return $this->belongsTo('App\User');
  }

  public function company()
  {
      return $this->belongsTo('App\SYS\SCompany', 'company_id');
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
