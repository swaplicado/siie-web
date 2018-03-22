<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SImportation extends Model {

    protected $connection = 'siie';
    protected $primaryKey = 'id_importation';
    protected $table = 'erps_importations';

    protected $fillable = [
                            'id_importation',
                            'code',
                            'name',
                            'last_importation',
                            'updated_by_id',
                          ];

}
