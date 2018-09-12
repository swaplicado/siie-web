<?php
//\Config::get('scmms.PO_STATUS.ST_NEW')

return [

    'IS_EXPLODED' => '1', //
    'IS_NOT_EXPLODED' => '0',
    'NEXT_ST' => '2',
    'PREVIOUS_ST' => '1',

    'PO_STATUS' => [
          'ST_ALL' => '0',
          'ST_NEW' => '1',
          'ST_HEAVY' => '2',
          'ST_FLOOR' => '3',
          'ST_PROCESS' => '4',
          'ST_ENDED' => '5',
          'ST_CLOSED' => '6',
    ],

    'ASSIGN_TYPE' => [
          'MP' => '1',
          'PP' => '2',
          'FP' => '3',
          'PACK' => '4',
    ],

    'MOVS_QUERY' => [
                'RM_DELIVERY' => '1',
                'RM_RETURN' => '2',
                'PM_DELIVERY' => '3',
                'PM_RETURN' => '4',
                'PP_DELIVERY' => '5',
                'PP_ASSIGNAMENT' => '6',
                'FP_DELIVERY' => '7',
                'CONSUMPTION_MVTS' => '8',
              ],

];
