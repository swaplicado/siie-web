<?php namespace App\QMS;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class SQMongoDoc extends Eloquent {

    protected $connection = 'mongodbcom';
    protected $collection = 'quality_documents';

    protected $fillable = ['name', 'contribs', 'awards'];

}