<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SCoUsPermission extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_cup';
  protected $table = "sys_com_usr_prmssns";
  protected $fillable = ['id_cup'];

  /**
   * [permission description]
   * Return object SPermission
   * @return SPermission
   */
  public function permission()
  {
      return $this->belongsTo('App\SYS\SPermission');
  }

  /**
   * [company description]
   * Return object SCompany
   * @return SCompany
   */
  public function company()
  {
      return $this->belongsTo('App\SYS\SCompany');
  }

  /**
   * [user description]
   * Return object User
   * @return User
   */
  public function user()
  {
      return $this->belongsTo('App\User');
  }
}
