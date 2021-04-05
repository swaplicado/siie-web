/*
* language of datatables
*/
var oSegTable = $('#table_seg').DataTable({
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
      "colReorder": true
  });

  oSegTable.column( 0 ).visible( false );
  oSegTable.column( 1 ).visible( false );
  oSegTable.column( 2 ).visible( false );
  oSegTable.column( 3 ).visible( false );
  oSegTable.column( 4 ).visible( false );
  oSegTable.column( 5 ).visible( false );
  oSegTable.column( 6 ).visible( false );
  oSegTable.column( 7 ).visible( false );
  oSegTable.column( 21 ).visible(false );
  //oSegTable.column( 22 ).visible(false );
