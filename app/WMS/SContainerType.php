<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SContainerType extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_container_type';
  protected $table = 'wmss_container_types';

  protected $fillable = [
                          'name',
                          'is_deleted',
                        ];
}
