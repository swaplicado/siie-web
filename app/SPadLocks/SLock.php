<?php namespace App\SPadLocks;

use Illuminate\Database\Eloquent\Model;

class SLock extends Model {

  protected $connection = 'ssystem';
  protected $primaryKey = 'id_lock';
  protected $table = 'syss_locks';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'id_lock',
                          'company_id',
                          'table_name',
                          'record_id',
                          'user_id',
                        ];



  public function user()
  {
    return $this->belongsTo('App\User', 'user_id');
  }

  public function company()
  {
    return $this->belongsTo('App\SYS\Scompany', 'company_id');
  }

}
