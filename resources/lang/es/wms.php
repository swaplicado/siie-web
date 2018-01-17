<?php

# trans('wms.CATALOGUES')

return [
      'MODULE'  => 'Módulo Almacenes',
      'STOCK_QUERY'  => 'Consulta de existencias',

      'CATALOGUES' => 'Catálogos',
          'WAREHOUSES' => 'Almacenes',
          'LOCATIONS' => 'Ubicaciones',
          'PALLETS' => 'Tarimas',
          'LOTS' => 'Lotes',
          'BAR_CODES' => 'Códigos de barras',

      'CONFIGURATION' => 'Configuración',

      'INVENTORY' => 'Inventarios',
          'ITEM_STK' => 'Existencias por material/producto',
          'LOT_STK' => 'Existencias por lote',
          'PALLET_STK' => 'Existencias por tarima',
          'LOC_STK' => 'Existencias por ubicación',
          'WHS_STK' => 'Existencias por almacén',
          'BRANCH_STK' => 'Existencias por sucursal',
          'LOT_WHS_STK' => 'Existencias por lote por almacén',
          'PALLET_LOT_STK' => 'Existencias por tarima por lote',
          'STK_SEGREGATIONS' => 'Existencias segregadas',
          'STK_SEG_QLTY' => 'Existencias en calidad',
          'MOV_STK' => 'Operaciones',
          'MOV_STK_IN_ADJ' => 'Entrada por ajuste',
          'MOV_STK_OUT_ADJ' => 'Salida por ajuste',
          'MOV_WHS_TRS_OUT' => 'Traspaso de almacén',
          'MOV_WHS_PUR_IN' => 'Entrada por compra',
          'WHS_MOVS_QUERY' => 'Movimientos de inventario',
          'WHS_MOVS' => 'Movimientos de inventario',
          'WHS_MOVS_FOLIOS' => 'Folios de movimientos de inventario',
          'RECONFIG_PALLETS' => 'Reconfiguracion tarimas',
          'PALLET_DIVISION' => 'Dividir tarima',
          'PALLET_ADD' => 'Agregar a tarima',

      'DOCS' => 'Documentos',
      'PUR_DOCS' => 'Documentos de compra',
      'SAL_DOCS' => 'Documentos de venta',

      'REPORTS' => 'Reportes',
          'REPORT_STK' => 'Reporte de existencias',
          'REPORT_INV' => 'Reporte de inventarios',

      'labels'  => [
                      'CODE' => 'Código',
                      'FOLIO' => 'Folio',
                      'FOLIO_START' => 'Folio inicial',
                      'MAX' => 'Máximo',
                      'MIN' => 'Mínimo',
                      'MAT_PROD' => 'Material/producto',
                      'UNIT' => 'Unidad',
                      'LOCATION' => 'Ubicación',
                      'LOT' => 'Lote',
                      'STOCK' => 'Existencia',
                      'STOCKS' => 'Existencias',
                      'PALLET' => 'Tarima',
                      'PRICE' => 'Precio',
                      'QTY' => 'Cantidad',
                      'LOTS_ASSIGNAMENT' => 'Asignación de lotes',
                      'QTY_FOR_COMPLETE' => 'Cantidad por completar',
                      'WAREHOUSE' => 'Almacén',
                      'BRANCH' => 'Sucursal',
                      'COMPANY' => 'Empresa',
                      'MVT_CLASS' => 'Clase de movimiento',
                      'MVT_TYPE' => 'Tipo de movimiento',
                      'PALLET_TO_DIVIDE' => 'Tarima a dividir',
                      'PALLET_TO_FILL' => 'Tarima destino',
                      'ELEMENTS_TO_MOVE' => 'Elementos a mover',
                      'LEVEL' => 'Nivel',
                      'REFERENCE' => 'Referencia',
                    ],

      'placeholders'  =>  [
                          'FOLIO_START' => 'Ingrese folio inicial...',
                          'SELECT_MVT_CLASS' => 'Seleccione clase de movimiento...',
                          'SELECT_MVT_TYPE' => 'Seleccione tipo de movimiento...',
                          'SELECT_MAT_PROD' => 'Seleccione material/producto...',
                          'SELECT_LEVEL' => 'Seleccione nivel...',
                          'SELECT_REFERENCE' => 'Seleccione referencia...',
                        ],

      'buttons'  => [
                      'ADD_LOT' => 'Agregar lote',
                    ],

      'QRY_INVENTORY' =>  'Consulta de inventarios',
      'QRY_INVENTORY_T' =>  'Apartado en el que se pueden consultar las existencias de la empresa por diferentes niveles y agrupaciones.',

      'MOV_WAREHOUSES' =>  'Movimientos de inventarios',
      'MOV_WAREHOUSES_T' =>  'Consulta de los movimientos de inventarios realizados en la empresa por períodos.',

      'LBL_GENERATION' =>  'Generación de etiquetas',
      'LBL_GENERATION_T' =>  'En este apartado se generan las etiquetas para lotes y tarimas.',

      'DOC_ASSORTMENT' =>  'Surtido de documentos',
      'DOC_ASSORTMENT_T' =>  'Aquí se podrán surtir documentos importados de compras y de ventas.',

      'DOC_RETURNS' =>  'Devoluciones',
      'DOC_RETURNS_T' =>  'En este apartado se podrá realizar la devolución de materiales/productos de clientes o a proveedores.',

      'REPORTS_T' =>  'Aquí se encuentran los reportes que puede generar el sistema y que pueden ser impresos.',

];
