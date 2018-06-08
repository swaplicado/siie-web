oDocsTable = $('#docs_table').DataTable({
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
            "targets": 0,
            "className": "text-center",
            "width": "10%"
        },
        {
            "targets": 1,
            "className": "text-center",
            "width": "5%"
        },
        {
            "targets": 2,
            "className": "text-right",
            "width": "8%"
        },
        {
            "targets": 3,
            "width": "13%"
        },
        {
            "targets": [7,8,9],
            "width": "5%"
        },
        {
            "targets": [10,11,12,13,14],
            "width": "20%"
        }
      ],
      "dom": 'Bfrtip',
      "buttons": [
            'copy', 'csv', 'excel', 'print'
        ]
  });
