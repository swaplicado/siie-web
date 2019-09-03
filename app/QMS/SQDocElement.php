<?php namespace App\QMS;

use App\ERP\SModel;

class SQDocElement extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_element';
    protected $table = 'qms_doc_elements';
  
    protected $fillable = [
                            'id_element',
                            'element',
                            'n_values',
                            'analysis_id',
                            'is_deleted',
                            'element_type_id',
                            'created_by_id',
                            'updated_by_id',
                          ];
  
  
    public function getTable()
    {
      return $this->table;
    }

    public function fields()
    {
      return $this->hasMany('App\QMS\SElementField', 'element_id', 'id_element');
    }

}