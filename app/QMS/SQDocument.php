<?php namespace App\QMS;

use App\ERP\SModel;

class SQDocument extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_document';
    protected $table = 'qms_quality_documents';
  
    protected $fillable = [
                            'id_document',
                            'title',
                            'dt_document',
                            'body_id',
                            'is_closed',
                            'is_deleted',
                            'lot_id',
                            'item_id',
                            'unit_id',
                            'father_po_id',
                            'son_po_id',
                            'sup_quality_id',
                            'sup_process_id',
                            'sup_production_id',
                            'signature_mb_id',
                            'created_by_id',
                            'updated_by_id',
                            'closed_at'
                          ];
  
    public function getTable()
    {
      return $this->table;
    }

    public function lot()
    {
        return $this->belongsTo('App\WMS\SWmsLot');
    }

    public function fatherPO()
    {
        return $this->belongsTo('App\MMS\SProductionOrder', 'father_po_id');
    }

    public function sonPO()
    {
        return $this->belongsTo('App\MMS\SProductionOrder', 'son_po_id');
    }

}