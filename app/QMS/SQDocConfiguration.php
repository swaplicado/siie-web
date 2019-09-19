<?php namespace App\QMS;

use App\ERP\SModel;

class SQDocConfiguration extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_configuration';
    protected $table = 'qms_doc_configurations';
  
    protected $fillable = [
                            'id_configuration',
                            'is_deleted',
                            'item_link_type_id',
                            'item_link_id',
                            'section_id',
                            'element_id',
                            'created_by_id',
                            'updated_by_id',
                        ];
  
    public function getTable()
    {
      return $this->table;
    }

}