<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SUserPermission extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_user_permission';
  protected $table = 'user_permissions';
  public $timestamps = false;
  protected $fillable = ['id_usr_per','user_id','permission_id', 'permission_type_id', 'company_id_opt','privilege_id', 'modules_id'];

  public function user()
  {
      return $this->belongsTo('App\User', 'user_id');
  }

  public function privilege()
  {
      return $this->belongsTo('App\SYS\SPrivilege');
  }

  public function permission()
  {
      return $this->belongsTo('App\SYS\SPermission', 'permission_id');
  }

  public function scopeSearch($query, $permission_id, $user_id)
  {
      return $query->where('permission_id', '=', "$permission_id")->where('user_id', '=', "$user_id");
  }
}
