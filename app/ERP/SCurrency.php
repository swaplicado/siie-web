<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SCurrency extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_cur';
  protected $table = 'erps_currencies';

  public static function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];

}
