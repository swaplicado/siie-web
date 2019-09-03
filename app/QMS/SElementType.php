<?php namespace App\QMS;

use App\ERP\SModel;

class SElementType extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_element_type';
    protected $table = 'qmss_element_types';
  
    protected $fillable = [
                            'id_section',
                            'title',
                            'dt_section',
                            'comments',
                            'is_deleted',
                            'created_by_id',
                            'updated_by_id',
                          ];
  
  
    public function getTable()
    {
      return $this->table;
    }

}