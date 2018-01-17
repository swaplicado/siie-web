/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vms = new Vue({
  el: '#app',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      quantity: 0
    }
  }
})

/**
 *
 */
function classificateUnits(obj) {
    oRow = $(obj).closest('tr')[0];

    var sItemCode = '';
    var sItem = '';
    var sUnit = '';
    var sLot = '';
    var sPallet = '';
    var sStatus = '';
    var dQuantity = 0;

    if (oRow != null) {
      sItemCode = oRow.children[0].textContent;
      sItem = oRow.children[1].textContent;
      sUnit = oRow.children[2].textContent;
      sLot = oRow.children[3].textContent;
      sPallet = oRow.children[4].textContent;
      sStatus = oRow.children[7].outerText;
      dQuantity = oRow.children[5].textContent;
    }

    Vue.set(vms.dataItem, 'item_code', sItemCode);
    Vue.set(vms.dataItem, 'item', sItem);
    Vue.set(vms.dataItem, 'unit', sUnit);
    Vue.set(vms.dataItem, 'lot', sLot);
    Vue.set(vms.dataItem, 'pallet', sPallet);
    Vue.set(vms.dataItem, 'status', sStatus);
    Vue.set(vms.dataItem, 'quantity', dQuantity);
}

function setAll(obj) {
    var dQuantity = 0;

    bCheck = document.getElementById('to_all').checked;

    if (bCheck) {
      if (vms.dataItem != 'undefined' && vms.dataItem != null) {
        dQuantity = parseFloat(vms.dataItem.quantity, 10);
      }
    }

    document.getElementById('quantity').value = dQuantity;
}

/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setData() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();

    var dQuantity = parseFloat(document.getElementById('quantity').value, 10);
    var iStatusNew = parseInt(document.getElementById('status').value, 10);

    dataRow.push(dQuantity);
    dataRow.push(iStatusNew);

    var oData = { value : dataRow };
    console.log(oData);

    $.ajax({
      type: "POST",
      url: './index/process',
      data: oData,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function() {
        console.log("Value added ");
      }
    });

    swal({
        title: 'Espere...',
        text: 'Clasificando unidades.',
        timer: 300000,
        onOpen: () => {
          swal.showLoading()
        }
      }).then((result) => {
        if (result.dismiss === 'timer') {
          console.log('I was closed by the timer');
        }
      });

    $(document).ajaxStop(function(){
        window.location.reload();
    });
}

$('#closeClass').on('click', function(e) {
    var dQuantity = parseFloat(document.getElementById('quantity').value, 10);
    var iStatusNew = parseInt(document.getElementById('status').value, 10);

    if (dQuantity <= 0) {
      swal("Error", "La cantidad debe ser mayor a cero.", "error");
      return false;
    }

    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    setData();
});
