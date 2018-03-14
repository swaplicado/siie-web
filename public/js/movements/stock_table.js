oStockTable = $('#stock_table').DataTable({
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
      "columnDefs": [
        {
            "targets": 0,
            "className": "text-center",
            "width": "20%"
        },
        {
            "targets": 1,
            "className": "text-center",
            "width": "20%"
        },
        {
            "targets": 2,
            "className": "text-center",
            "width": "30%"
        },
        {
            "targets": 3,
            "className": "text-right",
            "width": "25%"
        },
        {
            "targets": 4,
            "className": "text-center",
            "width": "5%"
        }
      ],
  });

oCompleteStockTable = $('#stock_com_table').DataTable({
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
      "columnDefs": [
        {
            "targets": 0,
            "width": "10%"
        },
        {
            "targets": 1,
            "width": "30%"
        },
        {
            "targets": 2,
            "className": "text-center",
            "width": "20%"
        },
        {
            "targets": 3,
            "className": "text-center",
            "width": "20%"
        },
        {
            "targets": 4,
            "className": "text-center",
            "width": "10%"
        },
        {
            "targets": 5,
            "className": "text-right",
            "width": "5%"
        },
        {
            "targets": 6,
            "className": "text-center",
            "width": "5%"
        }
      ],
  });
