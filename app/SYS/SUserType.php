<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SUserType extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_user_type';
  protected $table = 'syss_user_types';
  protected $fillable = ['id_user_type','name'];

  /**
   * [user description]
   * Return object User
   * @return [type] [description]
   */
  public function user()
  {
      return $this->hasMany('App\User');
  }
}
