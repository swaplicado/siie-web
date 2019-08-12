<?php
//\Config::get('scsiie.ATT.IS_COMP')

return [

  'CONFIGURATION' => [
                  'PARTNER_ID' => '1',
                  'DECIMALS_AMT' => '2',
                  'DECIMALS_QTY' => '3',
                  'LOC_ENABLED' => '4',
                  'LOCK_TIME' => '5',
                  'DB_IMPORT' => '6',
                  'DB_HOST' => '7',
                  'PERCENT_SUPPLY' => '8',
                  'WHS_ITEM_TRANSIT' => '9',
                  'CAN_CREATE_LOT_PAL_MAT' => '10',
                  'CAN_CREATE_LOT_PAL_PROD' => '11',
                  'LOCAL_CURRENCY' => '12',
                  'DECIMALS_PERC' => '13',
                  'FOLIOS_LONG' => '14',
                  'PALLETS' => '15',
                ],

  'ATT' => [

                'IS_COMP' => '1',
                'IS_SUPP' => '2',
                'IS_CUST' => '3',
                'IS_CRED' => '4',
                'IS_DEBT' => '5',
                'IS_BANK' => '6',
                'IS_EMPL' => '7',
                'IS_AGTS' => '8',
                'IS_PART' => '9',
                'ALL' => '10',

              ],

  'ITEM_CLS' => [
                    'MATERIAL' => '1',
                    'PRODUCT' => '2',
                    'SPENDING' => '3',
                  ],

  'ITEM_LINK' => [
                    'ALL' => '1',
                    'CLASS' => '2',
                    'TYPE' => '3',
                    'FAMILY' => '4',
                    'GROUP' => '5',
                    'GENDER' => '6',
                    'ITEM' => '7',
                  ],

  'ITEM_TYPE' => [
                    'DIRECT_MATERIAL_MATERIAL' => '1',
                    'DIRECT_PACKING_MATERIAL' => '2',
                    'INDIRECT_MATERIAL' => '3',
                    'REPROCESS' => '4',
                    'PRODUCT' => '5',
                    'BASE_PRODUCT' => '6',
                    'FINISHED_PRODUCT' => '7',
                    'SUBPRODUCT' => '8',
                    'DUMPS' => '9',
                    'EXPENSES_PURCHASE' => '10',
                    'EXPANSES_DIRECT' => '11',
                    'EXPENSES_INDIRECT' => '12',
                  ],

  'ITEM_STATUS' => [
                    'ACTIVE' => '1',
                    'RESTRICTED' => '2',
                    'LOCKED' => '3',
                  ],

  'DOC_CAT' => [
                    'PURCHASES' => '1',
                    'SALES' => '2',
                  ],

  'DOC_CLS' => [
                    'QUOTE' => '1',
                    'ORDER' => '2',
                    'DOCUMENT' => '3',
                    'TRANSFER' => '4',
                    'ADJUST' => '5',
                  ],

  'DOC_TYPE' => [
                    'QUOTE' => '1',
                    'CONTRACT' => '2',
                    'ORDER' => '1',
                    'INVOICE' => '1',
                    'REMISSION' => '2',
                    'SALE_NOTE' => '3',
                    'TICKET' => '4',
                    'LETTER' => '1',
                    'CREDIT_NOTE' => '1',
                  ],

  'DOC_SYS_STATUS' => [
                    'NEW' => '1',
                    'ISSUED' => '2',
                    'ANNULLED' => '3',
                  ],

  'FRMT' =>       [
                    'AMT' => '1',
                    'QTY' => '2',
                    'PERC' => '3',
                  ],

  'FILTER_BULK' =>       [
                    'RETAIL' => '0',
                    'BULK' => '1',
                    'ALL' => '2',
                  ],

  'FILTER_LOT' =>       [
                    'NLOT' => '0',
                    'LOT' => '1',
                    'ALL' => '2',
                  ],

  'FILTER_GENDER' =>       [
                    'ALL' => '0',
                  ],

  'DOC_OPER' =>   [
                    'CLOSE' => '0',
                    'OPEN' => '1',
                  ],

  'OP_FROM' =>   [
                    'PRODUCTION' => '1',
                    'QUALITY' => '2',
                  ],
];
