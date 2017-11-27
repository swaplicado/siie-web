<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SErpConfiguration extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_configuration';
  protected $table = 'erp_configuration';

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'code',
                          'name',
                          'val_int',
                          'val_text',
                          'val_dec',
                          'is_deleted',
                        ];

}
