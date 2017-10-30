/*
* language of datatables
*/
$('#example').DataTable( {
      "language": {
          "lengthMenu": "Mostrar _MENU_ renglones",
          "zeroRecords": "No se encontraron registros",
          "info": "Mostrando _START_ - _END_ de _TOTAL_ renglones",
          "infoEmpty": "No hay renglones disponibles",
          "infoFiltered": "(filtered from _MAX_ total records)",
          "sPrevious": "Anterior",
          "sNext": "Siguiente",
          "sSearch": "Buscar:"
      }
  } );

//   /*
//   * Add row to table
//   */
// function ir() {
//     var qty = document.getElementById("quantity").value;
//     var item = document.getElementById("item").value;
//
//     var tblBody = document.getElementById("lbody");
//
//     var values = [
//                 item,
//                 "Volvo",
//                 "Volvo",
//                 "Volvo",
//                 "Volvo",
//                 "Volvo",
//                 "Volvo",
//                 qty
//               ];
//
//     var oTr = document.createElement("tr");
//
//     for (i = 0; i < values.length; i++) {
//       var oTd = document.createElement("td");
//       var textTd = document.createTextNode(values[i]);
//       oTd.appendChild(textTd);
//
//       oTr.appendChild(oTd);
//     }
//
//     tblBody.appendChild(oTr);
// }


$('#quantity').on('click', function(e) {
    var item = document.getElementById("item").value;
    var qty = document.getElementById("quantity").value;

    var parent = e.target.value;
    //ajax
    $.get('movs/children?parent=' + item, function(data) {
        //success data
        console.log(data);
        // $('#item_type_id').empty();
        $.each(data, function(index, dataObject) {
          var tblBody = document.getElementById("lbody");

          var values = [
                      dataObject.code,
                      dataObject.name,
                      dataObject.unit.code,
                      "Volvo",
                      "Volvo",
                      "Volvo",
                      parseFloat(0.0).toFixed(8),
                      parseFloat(qty).toFixed(8)
                    ];

          var oTr = document.createElement("tr");

          for (i = 0; i < values.length; i++) {
            var oTd = document.createElement("td");
            var textTd = document.createTextNode(values[i]);
            if (i == 7) {
              oTd.setAttribute("class", "summ");
              oTd.setAttribute("align", "right");
            }
            if (i == 6) {
              oTd.setAttribute("align", "right");
            }
            oTd.appendChild(textTd);

            oTr.appendChild(oTd);
          }

          tblBody.appendChild(oTr);
        });
    });
});
