$('#movs_table').DataTable({
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
            "width": "5%"
        },
        {
            "targets": 1,
            "className": "text-center",
            "width": "7%"
        },
        {
            "targets": 2,
            "width": "5%"
        },
        {
            "targets": 3,
            "width": "15%"
        },
        {
            "targets": [4, 5],
            "width": "10%"
        },
        {
            "targets": 6,
            "className": "text-center",
            "width": "5%"
        }
      ],
  });
