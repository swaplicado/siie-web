/*
* language of datatables
*/
$('#table_id').DataTable({
      "language": {
          "lengthMenu": "Mostrar _MENU_ renglones",
          "zeroRecords": "No se encontraron registros",
          "info": "Mostrando _START_ - _END_ de _TOTAL_ renglones",
          "infoEmpty": "No hay renglones disponibles",
          "infoFiltered": "(filtered from _MAX_ total records)",
          "sPrevious": "Anterior",
          "sNext": "Siguiente",
          "Previous": "Anterior",
          "Next": "Siguiente",
          "sSearch": "Buscar:"
      }
  });

  $(document).ready( function () {
    $('#table_id').DataTable();
} );
