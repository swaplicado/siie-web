<?php
//\Config::get('scperm.OPERATION.EDIT')

return [

  'TP_PERMISSION' => [
                  'MODULE' => '1',
                  'BRANCH' => '2',
                  'WAREHOUSE' => '3',
                  'VIEW' => '4',
                ],

  'UNDEFINED' => '0',

  'MODULES'   =>  [
                  'ERP' => '001',
                  'MMS' => '002',
                  'QMS' => '003',
                  'WMS' => '004',
                  'TMS' => '005',
                ],

  'VIEW_CODE' => [
                  'USERS' => '001',
                  'PERMISSIONS' => '002',
                  'PRIVILEGES' => '003',
                  'ASSIGNAMENTS' => '004',
                  'ACCESS' => '005',
                  'COMPANIES' => '006',
                  'ERP_COMPANIES' => '007',
                  'BRANCHES' => '008',
                  'YEARS' => '009',
                  'MONTHS' => '010',
                  'BPS' => '011',
                  'UNITS' => '012',
                  'WAREHOUSES' => '013',
                  'LOCATIONS' => '014',
                  'ITM_FAM' => '015',
                  'ITM_GRP' => '016',
                  'IMT_GEN' => '017',
                  'ITEMS' => '018',
                  'ITEM_UNIT' => '019',
                  'BARCODES' => '020',
                ],

  'PERMISSION' => [
                  'ADMINISTRATOR' => '001',
                  'CENTRAL_CONFIG' => '002',
                  'ITEM_CONFIG' => '003',
                  'CONTAINERS' => '004',
                  'STK_MOVS' => '005',
                  'STK_MOVS_MANAGE' => '006',
                  'DOCUMENTS_MANAGE' => '007',
                  'WHS_PURCHASES' => '008',
                  'WHS_SALES' => '009',
                  'CONFIG_WHS_STD' => '010',
                  'CONFIG_WHS_MNG' => '011',
                  'ADJUSTS' => '012',
                  'TRANSFERS' => '013',
                  'TRANSFERS_EXTERNAL' => '014',
                  'PALLET_RECONFIG' => '015',
                  'INVENTORY_OPERATION' => '016',
                  'QUALITY' => '050',
                  'MMS_FORMULAS' => '120',
                  'IMPORTATIONS' => '150',

                  'ERP' =>  '101',
                  'MMS' =>  '102',
                  'QMS' =>  '103',
                  'WMS' =>  '104',
                  'TMS' =>  '105',
                ],
];
