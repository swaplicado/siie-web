<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SUserCompany extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_user_access';
  protected $table = "user_access";
  public $timestamps = false;
  protected $fillable = ['id_user_access','user_id','company_id'];

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
   * [company description]
   * Return object SCompany
   * @return SCompany
   */
  public function company()
  {
      return $this->belongsTo('App\SYS\SCompany', 'company_id');
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
