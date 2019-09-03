<?php namespace App\QMS;

use App\ERP\SModel;

class SQDocSection extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_section';
    protected $table = 'qms_doc_sections';
  
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