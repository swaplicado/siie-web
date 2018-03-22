<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SDocumentClass extends Model {

    protected $connection = 'siie';
    protected $primaryKey = 'id_doc_class';
    protected $table = 'erps_doc_classes';

    protected $fillable = [
                            'id_doc_class',
                            'code',
                            'name',
                            'is_deleted',
                          ];


    public function documents()
    {
      return $this->hasmany('App\ERP\SDocument', 'doc_class_id');
    }

}
