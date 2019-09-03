<?php namespace App\QMS;

use App\ERP\SModel;

class SElementField extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_field';
    protected $table = 'qms_element_fields';
  
    protected $fillable = [
                            'id_field',
                            'field_name',
                            'is_deleted',
                            'element_id',
                          ];
  
  
    public function getTable()
    {
      return $this->table;
    }

    public function element()
    {
        return $this->belongsTo('App\QMS\SQDocElement', 'element_id');
    }

}