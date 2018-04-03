class SSearchCore {

    initializateItems() {
      var aColumns = [];
      var oData = [];

      aColumns = [
            {
                "title": "idItem",
                "data": "id_item"
            }, {
                "title": "idUnit",
                "data": "id_unit"
            }, {
                "title": "-",
                "data": "id_item"
            }, {
                "title": "-",
                "data": "id_item"
            }, {
                "title": "-",
                "data": "id_item"
            }, {
                "title": "Clave",
                "data": "item_code"
            }, {
                "title": "Mat/prod",
                "data": "item_name"
            }, {
                "title": "Un.",
                "data": "unit_code"
            }, {
                "title": "Existencia.",
                "data": "available_stock",
                "className": "text-right"
            }
        ];

      oData = globalData.lFItems;

      if (oItemsTable != null) {
        oItemsTable.destroy();
      }

      oItemsTable = $('#search_items_table').DataTable({
          "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
          },
          "scrollY":        "50vh",
          "scrollCollapse": true,
          "paging":         false,
          "data": oData,
          "columns": aColumns
      });


      oItemsTable.column( 0 ).visible( false );
      oItemsTable.column( 1 ).visible( false );
      oItemsTable.column( 2 ).visible( false );
      oItemsTable.column( 3 ).visible( false );
      oItemsTable.column( 4 ).visible( false );

      if (globalData.bIsInputMov) {
        oItemsTable.column( 8 ).visible( false );
      }
    }
}

var searchCore = new SSearchCore();
