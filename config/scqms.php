<?php
//\Config::get('scqms.TO_EVALUATE')

return [

    'SHIPMENTS' => '1',
    'PRODUCTIONS' => '2',
    'BYINSPECTING' => '3',
    'INQUARANTINE' => '4',
    'ADVANCERELEASE' => '5',
    'PARTIALRELEASE' => '6',
    'TOTALRELEASE' => '7',
    'RECONDITION' => '8',
    'REPROCESS' => '9',
    'DESTROY' => '10',

    'SEGREGATION' => [
                        'INCREMENT' => '1',
                        'DECREMENT' => '2',
                      ],

    'SEGREGATION_TYPE' => [
                          'SHIPMENT_ORDER' => '1',
                          'PRODUCTION_ORDER' => '2',
                          'INSPECTED' => '3',
                          'QUARANTINE' => '4',
                        ],

    'QMS_VIEW' => [
                      'BY_STATUS' => '1',
                      'INSPECTION' => '2',
                      'CLASSIFY' => '3',
                      'INSPECTIONCLASSIFY' => '4',
                      'QUARANTINECLASSIFY' => '5',
                  ],

    'TYPE_VIEW' => [
                      'BY_LOT'=>'0',
                      'BY_PALLET'=>'1',
                      'BY_ONLY_LOT'=>'2',
                  ],

];
