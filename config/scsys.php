<?php
//\Config::get('scsys.OPERATION.EDIT')

return [

  'UNDEFINED' => '0',

  'MODULES'   =>  [
                  'ERP' => '1',
                  'MMS' => '2',
                  'QMS' => '3',
                  'WMS' => '4',
                  'TMS' => '5',
                ],

  'TP_USER' => [
                  'STANDART' => '1',
                  'MANAGER' => '2',
                  'ADMIN' => '3',
                ],

	'PRIVILEGES' => ['NA' => '1',
                    'READER' => '2',
                    'AUTHOR' => '3',
                    'EDITOR' => '4',
                    'MANAGER' => '5'],

  'OPERATION' => [
                  'CREATE' => '0',
                  'EDIT' => '1',
                  'DEL' => '2',
                  'SUPER' => '3',
                ],

  'STATUS' => [
                'ACTIVE' => '0',
                'DEL' => '1',
                'CLOSED' => '1',
                'OPENED' => '0',
              ],

  'FILTER' => ['DELETED' => '1',
                'ACTIVES' => '2',
                'ALL' => '3'],

  'OPTIONS' => [
                'EDIT' => '001',
                'DESTROY' => '002',
                'ACTIVATE' => '003',
                'COPY' => '004',
                'MOD_PASS' => '005',
              ],
];
