<?php
//\Config::get('scsiie.ATT.IS_COMP')

return [

  'CONFIGURATION' => [
                  'PARTNER_ID' => '1',
                  'DECIMALS_AMT' => '2',
                  'DECIMALS_QTY' => '3',
                  'LOC_ENABLED' => '4',
                  'LOCK_TIME' => '5',
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

  'FRMT' =>       [
                    'AMT' => '1',
                    'QTY' => '2',
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
];
