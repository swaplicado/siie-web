<?php namespace App\QMS;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SQMongoDoc extends Eloquent {

    protected $connection = 'mongodbcom';
    protected $collection = 'quality_documents';

    // qlty_document es un objeto de tipo SQDocument
    // configurations es una lista de objetos o arreglos de tipo SQDocConfiguration
    // results es una lista de objetos o arreglos de tipo asociativo id_configuration-id_field => resultado

    protected $fillable = [
                            'lot_id',
                            'lot',
                            'dt_expiry',
                            'lot_date',
                            'qlty_doc_id', 
                            'item_id',
                            'unit_id',
                            'results',
                            'usr_creation',
                            'usr,upd'
                        ];

}