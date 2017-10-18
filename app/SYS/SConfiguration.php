<?php namespace App\SYS;

use Illuminate\Database\Eloquent\Model;

class SConfiguration extends Model
{
  protected $connection = 'ssystem';
  protected $primaryKey = 'id_configuration';
  protected $table = 'sys_configuration';
  protected $fillable = ['version','partner_id'];

}
