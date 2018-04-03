oMovsTable = $('#example').DataTable({
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
            "targets": [6,7], // your case first column
            "className": "text-right"
        },
        {
            "targets": [1,3,4,5,8,9], // your case first column
            "className": "text-center"
        },
        {
            "targets": 1, // your case first column
            "width": "10%"
        },
        {
            "targets": 2,
            "width": "35%"
        },
        {
            "targets": 3,
            "width": "5%"
        },
        {
            "targets": 4,
            "width": "10%"
        },
        {
            "targets": 5,
            "width": "10%"
        },
        {
            "targets": 6,
            "width": "10%"
        },
        {
            "targets": 7,
            "width": "10%"
        },
        {
            "targets": 8,
            "width": "5%"
        },
        {
            "targets": 9,
            "width": "5%"
        }
     ],
  });

  oMovsTable.column( 0 ).visible( false );

  $('#example tbody').on( 'click', 'tr', function () {
      if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
      }
      else {
          oMovsTable.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
      }
  });

oDocsTable = $('#doc_table').DataTable({
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
            "targets": [7, 8, 9, 10],
            "className": "text-right"
        }
     ],
  });

  oDocsTable.column( 0 ).visible( false );
  oDocsTable.column( 1 ).visible( false );
  oDocsTable.column( 2 ).visible( false );
  oDocsTable.column( 3 ).visible( false );

  $('#doc_table tbody').on( 'click', 'tr', function () {
      if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
      }
      else {
          oDocsTable.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
      }
  });
