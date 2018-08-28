<?php

# trans('wms.CATALOGUES')

return [
      'MODULE'  => 'Módulo Almacenes',
      'STOCK_QUERY'  => 'Consulta existencias',
      'DEFAULT_CODE'  => 'PRED',
      'DEFAULT'  => 'PREDETERMINADA',

      'CATALOGUES' => 'Catálogos',
          'WAREHOUSES' => 'Almacenes',
          'LOCATIONS' => 'Ubicaciones',
          'PALLETS' => 'Tarimas',
          'LOTS' => 'Lotes',
          'BAR_CODES' => 'Códigos de barras',

      'CONFIGURATION' => 'Configuración',

      'INVENTORY' => 'Inventarios',
          'ITEM_STK' => 'Existencias por material-producto',
          'LOT_STK' => 'Existencias por lote',
          'PALLET_STK' => 'Existencias por tarima',
          'LOC_STK' => 'Existencias por ubicación',
          'WHS_STK' => 'Existencias por almacén',
          'WHS_IN_STK' => 'Existencias en almacén actual',
          'BRANCH_STK' => 'Existencias por sucursal',
          'LOT_WHS_STK' => 'Existencias por lote por almacén',
          'GENERAL_STK' => 'Existencias generales',
          'GENERATE_INITIAL_INVENTORY' => 'Generar inventario inicial',
          'PALLET_LOT_STK' => 'Existencias por tarima por lote',
          'PHYSICAL_INVENTORY' => 'Inventario Físico',
          'PHYSICAL_INVENTORY_UPP' => 'INVENTARIO FÍSICO',
          'STK_SEGREGATIONS' => 'Existencias segregadas',
          'STK_SEG_QLTY' => 'Existencias en calidad',
          'MOV_STK' => 'Operaciones',
          'MOV_STK_IN_ADJ' => 'Entrada por ajuste',
          'MOV_STK_OUT_ADJ' => 'Salida por ajuste',
          'MOV_WHS_TRS_OUT' => 'Traspaso de almacén',
          'MOV_WHS_INTERNAL_TRS_OUT' => 'Traspaso interno de almacén',
          'MOV_WHS_SEND_EXTERNAL_TRS_OUT' => 'Enviar traspaso externo de almacén',
          'MOV_WHS_RECEIVE_EXTERNAL_TRS_OUT' => 'Recibir traspaso externo de almacén',
          'MOV_WHS_PUR_IN' => 'Entrada por compra',
          'WHS_MOVS_QUERY' => 'Movimientos almacén',
          'WHS_MOVS' => 'Movimientos de almacén',
          'WHS_MOVS_DETAIL' => 'Movimientos de almacén (detalle)',
          'WHS_DOCS' => 'Documentos de almacén',
          'WHS_MOVS_FOLIOS' => 'Folios de movimientos de almacén',
          'RECONFIG_PALLETS' => 'Reconfiguracion tarimas',
          'PALLET_DIVISION' => 'Dividir tarima',
          'PALLET_ADD' => 'Agregar a tarima',
          'EMPTY_WAREHOUSE' => 'Vaciar almacén',

      'DOCS' => 'Documentos',
      'PUR_DOCS' => 'Documentos compra',
      'SAL_DOCS' => 'Documentos venta',

      'REPORTS' => 'Reportes',
          'REPORT_STK' => 'Reporte de existencias',
          'REPORT_INV' => 'Reporte de inventarios',

      'labels'  => [
                      'ACCUM_QUANTITY' => 'Cantidad acum',
                      'ASSIGNED' => 'Asignado',
                      'AVAILABLE' => 'Disponible',
                      'BRANCH' => 'Sucursal',
                      'BCH' => 'Suc.',
                      'BRANCH_DESTINY' => 'Sucursal destino',
                      'CLOSED' => 'Cerrado',
                      'CODE' => 'Código',
                      'MVT_CODE' => 'Código Mov',
                      'COMPANY' => 'Empresa',
                      'CUTOFF_DATE' => 'Fecha de corte',
                      'ELEMENTS_TO_MOVE' => 'Elementos a mover',
                      'EXPIRATION' => 'Vencimiento',
                      'EXPIRATION_DATE' => 'Fecha vencimiento',
                      'FOLIO' => 'Folio',
                      'FOLIO_START' => 'Folio inicial',
                      'IGNORE_ROTATION' => 'Ignorar rotación de lotes',
                      'ITEM_TYPE' => 'Tipo de ítem',
                      'IN_MOVEMENT' => 'En movimiento',
                      'INDIRECT_SUPPLY' => 'Surtido indirecto',
                      'IS_ROTATION' => 'Requiere rotación de lotes',
                      'INPUTS' => 'Entradas',
                      'LEVEL' => 'Nivel',
                      'LINKED' => 'Enlazado',
                      'LOCATION' => 'Ubicación',
                      'LOCATION_DESTINY' => 'Ubicación destino',
                      'LOC_DES' => 'Ubic. dest.',
                      'LOTS_ASSIGNAMENT' => 'Asignación de lotes',
                      'LOT' => 'Lote',
                      'LOTS' => 'Lotes',
                      'LOT_PALLET' => 'Lote/Tarima',
                      'MAX' => 'Máximo',
                      'MIN' => 'Mínimo',
                      'MAT_PROD' => 'Material/producto',
                      'MOVEMENT' => 'Movimiento',
                      'MVT_CLASS' => 'Clase de movimiento',
                      'MVT_DATE' => 'Fecha movimiento',
                      'MVT_TYPE' => 'Tipo de movimiento',
                      'OPENED' => 'Abierto',
                      'OUTPUTS' => 'Salidas',
                      'PALLET' => 'Tarima',
                      'PALLETS' => 'Tarimas',
                      'PALLET_TO_DIVIDE' => 'Tarima a dividir',
                      'PALLET_TO_FILL' => 'Tarima destino',
                      'PENDING' => 'Pendiente',
                      'PRICE' => 'Precio',
                      'QTY' => 'Cantidad',
                      'QTY_TO_MOVE' => 'Cantidad a mover',
                      'QTY_REMAINING' => 'Cantidad restante',
                      'QTY_FOR_COMPLETE' => 'Cantidad por completar',
                      'RECEIVED' => 'Recibido',
                      'RECEPTION_DATE' => 'Fecha de recepción',
                      'REFERENCE' => 'Referencia',
                      'REORDER' => 'Punto Reorden',
                      'SEE_PO' => 'Ver orden',
                      'SEGREGATED' => 'Segregado',
                      'SOURCE_BRANCH' => 'Sucursal origen',
                      'STOCK' => 'Existencia',
                      'STOCKS' => 'Existencias',
                      'SUPPLIED' => 'Surtido',
                      'UNIT' => 'Unidad',
                      'UN' => 'Un',
                      'WAREHOUSE' => 'Almacén',
                      'WAREHOUSES' => 'Almacenes',
                      'WHS' => 'Alm.',
                      'WITHOUT_PALLET' => 'Sin tarima',
                      'WITHOUT_ROTATION' => 'Sin rotación',
                    ],

      'placeholders'  =>  [
                          'FOLIO_START' => 'Ingrese folio inicial...',
                          'ITEM_TYPE' => 'Tipo de ítem...',
                          'SELECT_MVT_CLASS' => 'Seleccione clase de movimiento...',
                          'SELECT_MVT_TYPE' => 'Seleccione tipo de movimiento...',
                          'SELECT_MAT_PROD' => 'Seleccione material/producto...',
                          'SELECT_LEVEL' => 'Seleccione nivel...',
                          'SELECT_PALLET' => 'Seleccione tarima...',
                          'SELECT_REFERENCE' => 'Seleccione referencia...',
                          'SELECT_WAREHOUSE' => 'Seleccione almacén...',
                          'SELECT_WAREHOUSES' => 'Seleccione almacenes...',
                          'BAR_CODE_LOCATION' => 'Código barras ubicación...',
                          'SEARCH_ELEMENT' => 'Tarima, lote, material/producto...',
                        ],

      'tooltips'     => [
                          'ONLY_BARCODES' => 'Sólo se podrán buscar códigos de barras',
                          'ELEMENT_MULTIPLE' => 'Se pueden buscar códigos de barras de materiales/productos, '.
                                                'de tarimas y de lotes, así como código de un material/productos '.
                                                'en específico',
                          'LOTS' => 'Introduzca un lote nuevo o uno existente, si la fecha de vencimiento del lote '.
                                      'existente no coincide, se generará un error',
                        ],

      'buttons'  => [
                      'ADD_LOT' => 'Agregar lote',
                    ],

      'QRY_INVENTORY' =>  'Consulta de existencias',
      'QRY_INVENTORY_T' =>  'Apartado en el que se pueden consultar las existencias de la empresa por diferentes niveles y agrupaciones.',

      'MOV_WAREHOUSES' =>  'Movimientos de almacén',
      'MOV_WAREHOUSES_T' =>  'Consulta de los movimientos de almacén realizados en la empresa por períodos.',

      'LBL_GENERATION' =>  'Generación de etiquetas',
      'LBL_GENERATION_T' =>  'En este apartado se generan las etiquetas para lotes y tarimas.',

      'DOC_ASSORTMENT' =>  'Surtido de documentos',
      'DOC_ASSORTMENT_T' =>  'Aquí se podrán surtir documentos importados de compras y de ventas.',

      'DOC_RETURNS' =>  'Devoluciones',
      'DOC_RETURNS_T' =>  'En este apartado se podrá realizar la devolución de materiales/productos de clientes o a proveedores.',

      'REPORTS_T' =>  'Aquí se encuentran los reportes que puede generar el sistema y que pueden ser impresos.',

];
