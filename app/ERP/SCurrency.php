<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SCurrency extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_currency';
  protected $table = 'erps_currencies';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'code',
                          'name',
                          'is_deleted',
                        ];

}
