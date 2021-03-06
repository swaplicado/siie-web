<?php
//\Config::get('scsys.OPERATION.EDIT')

return [

  'UNDEFINED' => '0',

  'AREA' => [
                  'STANDARD' => '1',
                  'MANAGER' => '2',
                  'ADMIN' => '3',
                ],

  'MOD_NAVS' =>   [
                    'ERP' => 'navbar-siie',
                    'MMS' => 'navbar-blue',
                    'QMS' => 'navbar-orange',
                    'WMS' => 'navbar-green',
                    'TMS' => 'navbar-blue-light',
                  ],

  'MODULES'   =>  [
                  'ERP' => '1',
                  'MMS' => '2',
                  'QMS' => '3',
                  'WMS' => '4',
                  'TMS' => '5',
                ],

  'TP_USER' => [
                  'STANDARD' => '1',
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
                'ALL' => '3',
                'MONTH' => '01/01/2018 - 31/01/2018'],

  'OPTIONS' => [
                'EDIT' => '001',
                'DESTROY' => '002',
                'ACTIVATE' => '003',
                'COPY' => '004',
                'MOD_PASS' => '005',
                'NEW_BRANCH' => '006',
                'ADDRESS' => '007',
              ],

  'IMPORTATIONS' => [
                'UNITS' => '1',
                'FAMILIES' => '2',
                'GROUPS' => '3',
                'GENDERS' => '4',
                'ITEMS' => '5',
                'PARTNERS' => '6',
                'BRANCHES' => '7',
                'ADDRESS' => '8',
                'DOCUMENTS' => '9',
                'ROWS' => '10',
                'FORMULAS' => '12',
                'PO' => '13',
              ],
];
