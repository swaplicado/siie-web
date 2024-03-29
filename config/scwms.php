<?php
//\Config::get('scwms.MVT_CLS_IN')

return [

    'MVT_CLS_IN' => '1', //
    'MVT_CLS_OUT' => '2',

    'MVT_TP_IN_SAL' => '1',
    'MVT_TP_IN_PUR' => '2',
    'MVT_TP_IN_ADJ' => '3',
    'MVT_TP_IN_TRA' => '4', // transfer (traspaso)
    'MVT_TP_IN_CON' => '5', // conversion
    'MVT_TP_IN_PRO' => '6', // production
    'MVT_TP_IN_EXP' => '7', // expenses
    'MVT_TP_OUT_SAL' => '8',
    'MVT_TP_OUT_PUR' => '9',
    'MVT_TP_OUT_ADJ' => '10',
    'MVT_TP_OUT_TRA' => '11',
    'MVT_TP_OUT_CON' => '12',
    'MVT_TP_OUT_PRO' => '13',
    'MVT_TP_OUT_EXP' => '14',

    'PALLET_RECONFIG_IN' => '15',
    'PALLET_RECONFIG_OUT' => '16',

    'MVT_OUT_DLVRY_RM' => '17', // output delivery raw material
    'MVT_OUT_RTRN_RM' => '18', // output return raw material
    'MVT_OUT_DLVRY_PP' => '19', // output delivery product in process
    'MVT_OUT_RTRN_PP' => '20', // output return product in process
    'MVT_OUT_DLVRY_FP' => '21', // output delivery finished product
    'MVT_OUT_RTRN_FP' => '22', // output return finished product
    'MVT_OUT_CONSUMPTION' => '23', // output consumption
    'MVT_IN_DLVRY_RM' => '24',
    'MVT_IN_RTRN_RM' => '25',
    'MVT_IN_DLVRY_PP' => '26',
    'MVT_IN_RTRN_PP' => '27',
    'MVT_IN_DLVRY_FP' => '28',
    'MVT_IN_RTRN_FP' => '29',
    'MVT_IN_CONSUMPTION' => '30',
    'MVT_IN_ASSIGN_PP' => '31',
    'MVT_OUT_ASSIGN_PP' => '32',

    'PHYSICAL_INVENTORY'  =>  '404',

    // applies only for sales and purchases
    'N_A' => '1', // supply (surtido) and return
    'MVT_SPT_TP_STK_RET' => '2', // supply (surtido) and return
    'MVT_SPT_TP_CHA' => '3', // change
    'MVT_SPT_TP_WAR' => '4', // warranty
    'MVT_SPT_TP_CON' => '5', // consignment

    // applies only for production
    'MVT_MFG_TP_MAT' => '2', // materials
    'MVT_MFG_TP_PRO' => '3', // products
    'MVT_MFG_TP_PACK' => '4', // packing material
    'MVT_MFG_TP_FP' => '5', // finished product

    // applies only for adjustments
    'MVT_ADJ_TP_IFI' => '2', // initial and final inventory
    'MVT_ADJ_TP_DIS' => '3', // discrepancy
    'MVT_ADJ_TP_MAL' => '4', // malfunction
    'MVT_ADJ_TP_OBS' => '5', // obsolescence
    'MVT_ADJ_TP_EXP' => '6', // expiration
    'MVT_ADJ_TP_DAM' => '7', // damage
    'MVT_ADJ_TP_COM' => '8', // commercial sample
    'MVT_ADJ_TP_PRO' => '9', // promotional sample
    'MVT_ADJ_TP_IYD' => '10', // investigation and development
    'MVT_ADJ_TP_LAB' => '11', // laboratory
    'MVT_ADJ_TP_TAS' => '12', // tasting
    'MVT_ADJ_TP_DON' => '13', // donation
    'MVT_ADJ_TP_OTH' => '14', // others
    'MVT_ADJ_TP_PRO' => '15', // others

    // applies only for expenses
    'MVT_EXP_TP_PUR' => '1', // purchases
    'MVT_EXP_TP_PRO' => '2', // production

    // applies only for internal movements
    'MVT_INTERNAL_NA' => '1', // purchases
    'MVT_INTERNAL_ADJUST' => '2', // purchases
    'MVT_INTERNAL_TRANSFER' => '3', // production
    'MVT_INTERNAL_DIV_PALLET' => '4', // production
    'MVT_INTERNAL_ADD_TO_PALLET' => '5', // production

    'FILTER_ALL_WHS' => '0',

    'OPERATION_TYPE' => [
                            'CREATION' => '1',
                            'EDITION' => '2',
                            'SUPPLY' => '3',
                        ],

    'OPERATION' => [
                            'INPUT' => '1',
                            'OUTPUT' => '2',
                        ],

   'MOV_ACTION' =>     [
                          'ERASE' => '0',
                          'ACTIVATE' => '1',
                       ],

    'STOCK_TYPE'  =>  [
                        'STK_BY_ITEM' => '1',
                        'STK_BY_PALLET' => '2',
                        'STK_BY_LOT' => '3',
                        'STK_BY_LOCATION' => '4',
                        'STK_BY_WAREHOUSE' => '5',
                        'STK_BY_BRANCH' => '8',
                        'STK_BY_LOT_BY_WAREHOUSE' => '6',
                        'STK_BY_PALLET_BY_LOT' => '7',
                        'STK_GENERAL' => '10',
                      ],

    'RECONFIG_PALLETS' => '100',

    'STOCK_PARAMS' => [
                        'SSELECT' => '0',
                        'ITEM' => '1',
                        'UNIT' => '2',
                        'LOT' => '3',
                        'PALLET' => '4',
                        'LOCATION' => '5',
                        'WHS' => '6',
                        'BRANCH' => '7',
                        'ID_YEAR' => '8',
                        'DATE' => '9',
                        'ID_MVT' => '10',
                        'PROD_ORD' => '11',
                        'WITH_SEGREGATED' => '100',
                        'AS_AS' => '101',
                      ],

    'STOCK' => [
                  'GROSS' => '3',
                  'SEGREGATED' => '2',
                  'RELEASED' => '1',
                  'AVAILABLE' => '0',
                ],

    'CONTAINERS' => [
                  'NA' => '1',
                  'LOCATION' => '2',
                  'WAREHOUSE' => '3',
                  'BRANCH' => '4',
                  'COMPANY' => '5',
                ],

    'DOC_VIEW' => [
                  'NA' => '1',
                  'NORMAL' => '2',
                  'DETAIL' => '3',
                ],

    'DOC_VIEW_S' => [
                  'SUPP' => '1',
                  'BY_SUPP' => '2',
                ],

                'SEG_PARAM' => [
                              'ID_ITEM' => '0',
                              'ID_UNIT' => '1',
                              'ID_LOT' => '2',
                              'ID_PALLET' => '3',
                              'ID_WHS' => '4',
                              'ID_BRANCH' => '5',
                              'ID_REFERENCE' => '6',
                              'ID_STATUS_QLTY_PREV' => '7',
                              'ID_STATUS_QLTY_NEW' => '13',
                              'QUANTITY' => '9',
                              'EVENT' => '10',
                              'WAREHOUSE' => '11',
                              'LOCATION' => '12',
                              'NOTE' => '14',

                            ],

    'ELEMENTS_TYPE' => [
                  'ITEMS' => '0',
                  'LOTS' => '1',
                  'PALLETS' => '2',
                  'LOCATIONS' => '3',
                  'NOT_FOUND' => '404',
                ],


];
