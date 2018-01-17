/*
* language of datatables
*/
$('#table_seg').DataTable({
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
      }
  });

  $(document).ready( function () {
    $('#table_seg').DataTable();

    var table = $('#table_seg').DataTable();

    table.column( 0 ).visible( false );
    table.column( 1 ).visible( false );
    table.column( 2 ).visible( false );
    table.column( 3 ).visible( false );
    table.column( 4 ).visible( false );
    table.column( 5 ).visible( false );
    table.column( 6 ).visible( false );
    table.column( 7 ).visible( false );
  });
