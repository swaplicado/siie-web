<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SModule extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_module';
  protected $table = "syss_modules";
  protected $fillable = ['id_module','name'];

  public function companyModule()
  {
      return $this->hasMany('App\SYS\SCompanyModule');
  }

  public function permission()
  {
      return $this->hasMany('App\SYS\SPermission');
  }
}
