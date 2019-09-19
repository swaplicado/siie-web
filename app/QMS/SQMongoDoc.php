<?php namespace App\QMS;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SQMongoDoc extends Eloquent {

    protected $connection = 'mongodbcom';
    protected $collection = 'quality_documents';

    // qlty_document es un objeto de tipo SQDocument
    // configurations es una lista de objetos o arreglos de tipo SQDocConfiguration
    // results es una lista de objetos o arreglos de tipo asociativo id_configuration-id_field => resultado

    protected $fillable = ['qlty_document', 'configurations', 'results'];

}