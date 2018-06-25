$(function() {
  $('input[id="filterDate"]').daterangepicker({
    locale: {
          format: 'DD/MM/YYYY'
      }
  });
});

$('#filterDate').on('apply.daterangepicker', function(ev, picker) {
  // console.log(picker.startDate.format('YYYY-MM-DD'));
  // console.log(picker.endDate.format('YYYY-MM-DD'));
});

  $('#docTable').DataTable({
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
      "scrollX": true,
      "colReorder": true,
      "columnDefs": [
        {
            "targets": 0,
            "className": "text-center"
        },
        {
            "targets": 1,
            "className": "text-center"
        },
        {
            "targets": 2,
            "className": "text-center"
        }
      ],
      "dom": 'Bfrtip',
      "lengthMenu": [
        [ 10, 25, 50, 100, -1 ],
        [ 'Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100', 'Mostrar todo' ]
      ],
      "buttons": [
            'pageLength', 'copy', 'csv', 'excel', 'print'
        ]
    });

    $(document).ready( function () {
      var table = $('#docTable').DataTable();
    });
