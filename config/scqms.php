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

    'ANALYSIS_TYPE' => [
                        'FQ'=>'1',
                        'MB'=>'2',
                        'OL'=>'3',
                    ],

    'CFG_ZONE' => [
                        'FQ'=>'1',
                        'MB'=>'2',
                        'OL'=>'3',
                    ],

    'ELEM_TYPE' => [
                        'TEXT'=>1,
                        'DECIMAL'=>2,
                        'INT'=>3,
                        'DATE'=>4,
                        'USER'=>5,
                        'ANALYSIS'=>6,
                        'BOOL'=>7,
                        'FILE'=>8,
                    ],

];
