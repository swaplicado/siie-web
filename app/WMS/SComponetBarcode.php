<?php

namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SComponetBarcode extends Model
{
  protected $connection = 'siie';
  protected $primaryKey = 'id_component';
  protected $table = "wms_component_barcodes";

  public function getTable()
  {
    return $this->table;
  }

  protected $fillable = [
                          'id_component',
                          'name',
                          'digits',
                          'type_barcode'
                        ];
}
