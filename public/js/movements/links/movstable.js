var oMovscTable = $('#movs_table').DataTable({
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
      "colReorder": true,
      "scrollX": true,
      "columnDefs": [
        {
            "targets": 2,
            "className": "text-center",
            "width": "10%"
        },
        {
            "targets": 3,
            "className": "text-center",
            "width": "10%"
        },
        {
            "targets": 4,
            "className": "text-center",
            "width": "10%"
        },
        {
            "targets": 5,
            "className": "text-center",
            "width": "10%"
        },
        {
            "targets": 6,
            "className": "text-right",
            "width": "10%"
        },
        {
            "targets": 7,
            "className": "text-right",
            "width": "12%"
        },
        {
            "targets": 8,
            "className": "text-right",
            "width": "12%"
        },
        {
            "targets": 9,
            "className": "text-right",
            "width": "12%"
        },
        {
            "targets": 10,
            "className": "text-right",
            "width": "12%"
        },
        {
            "targets": 11,
            "className": "text-center",
            "width": "6%"
        }
      ],
  });

  oMovscTable.column( 0 ).visible( false );
  oMovscTable.column( 1 ).visible( false );
