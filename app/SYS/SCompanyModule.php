<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SCompanyModule extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = "id_com_mod";
  protected $table = "syss_company_modules";
  protected $fillable = ['id_com_mod'];

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
   * [module description]
   * Return object SModule
   * @return SModule
   */
  public function module()
  {
      return $this->belongsTo('App\SYS\SModule');
  }
}
