oLotsTable = $('#lots_table').DataTable({
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
            "targets": 2, // your case first column
            "className": "text-center",
            "width": "25%"
        },
        {
            "targets": 3, // your case first column
            "className": "text-center",
            "width": "40%"
        },
        {
            "targets": 4, // your case first column
            "className": "text-right",
            "width": "30%"
        }
      ],
  });

  oLotsTable.column( 0 ).visible( false );
  oLotsTable.column( 1 ).visible( false );

  $('#lots_table tbody').on( 'click', 'tr', function () {
      if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
      }
      else {
          oLotsTable.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
      }
  });
