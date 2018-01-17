<?php
//\Config::get('scqms.TO_EVALUATE')

return [

    'TO_EVALUATE' => '1',
    'REJECTED' => '2',
    'QUARANTINE' => '3',
    'PARTIAL_RELEASED' => '4',
    'RELEASED' => '5',
    'RELEASED_EARLY' => '6',
    'RET_TO_EVALUATE' => '7',
    'RECONDITIONING' => '8',
    'REWORK' => '9',
    'DESTROY' => '10',

    'SEGREGATION' => [
                        'INCREMENT' => '1',
                        'DECREMENT' => '2',
                      ],

    'SEGREGATION_TYPE' => [
                          'SHIPMENT' => '1',
                          'PRODUCTION_ORDER' => '2',
                          'QUALITY' => '3',
                        ],

];
