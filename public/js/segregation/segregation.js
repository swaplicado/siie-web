/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vmsQl = new Vue({
  el: '#appQl',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      quantity: 0,
      staevent: 0,
    }
  }
})

/**
 *
 */

function classificateQlty(obj) {
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
      sStatus = oRow.children[7].textContent;
      dQuantity = oRow.children[5].textContent;
    }

    Vue.set(vmsQl.dataItem, 'item_code', sItemCode);
    Vue.set(vmsQl.dataItem, 'item', sItem);
    Vue.set(vmsQl.dataItem, 'unit', sUnit);
    Vue.set(vmsQl.dataItem, 'lot', sLot);
    Vue.set(vmsQl.dataItem, 'pallet', sPallet);
    Vue.set(vmsQl.dataItem, 'status', sStatus);
    Vue.set(vmsQl.dataItem, 'quantity', dQuantity);
}
/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vmsRl = new Vue({
  el: '#appRl',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      quantity: 0,
      staevent: 0,
    }
  }
})

/**
 *
 */
function classificateRls(obj) {
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
      sStatus = oRow.children[7].textContent;
      dQuantity = oRow.children[5].textContent;
    }

    Vue.set(vmsRl.dataItem, 'item_code', sItemCode);
    Vue.set(vmsRl.dataItem, 'item', sItem);
    Vue.set(vmsRl.dataItem, 'unit', sUnit);
    Vue.set(vmsRl.dataItem, 'lot', sLot);
    Vue.set(vmsRl.dataItem, 'pallet', sPallet);
    Vue.set(vmsRl.dataItem, 'status', sStatus);
    Vue.set(vmsRl.dataItem, 'quantity', dQuantity);
}
/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vmsRf = new Vue({
  el: '#appRf',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      quantity: 0,
      staevent: 0,
    }
  }
})

/**
 *
 */
function classificateRfs(obj) {
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
      sStatus = oRow.children[7].textContent;
      dQuantity = oRow.children[5].textContent;
    }
    Vue.set(vmsRf.dataItem, 'item_code', sItemCode);
    Vue.set(vmsRf.dataItem, 'item', sItem);
    Vue.set(vmsRf.dataItem, 'unit', sUnit);
    Vue.set(vmsRf.dataItem, 'lot', sLot);
    Vue.set(vmsRf.dataItem, 'pallet', sPallet);
    Vue.set(vmsRf.dataItem, 'status', sStatus);
    Vue.set(vmsRf.dataItem, 'quantity', dQuantity);
}
/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vmsQlP = new Vue({
  el: '#appQlP',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      staevent: 0,
    }
  }
})

/**
 *
 */

function classificateQltyP(obj) {
    oRow = $(obj).closest('tr')[0];

    var sItemCode = '';
    var sItem = '';
    var sUnit = '';
    var sLot = '';
    var sPallet = '';
    var sStatus = '';

    if (oRow != null) {
      sItemCode = oRow.children[0].textContent;
      sItem = oRow.children[1].textContent;
      sUnit = oRow.children[2].textContent;
      sLot = oRow.children[3].textContent;
      sPallet = oRow.children[4].textContent;
      sStatus = oRow.children[7].textContent;
    }

    Vue.set(vmsQlP.dataItem, 'item_code', sItemCode);
    Vue.set(vmsQlP.dataItem, 'item', sItem);
    Vue.set(vmsQlP.dataItem, 'unit', sUnit);
    Vue.set(vmsQlP.dataItem, 'lot', sLot);
    Vue.set(vmsQlP.dataItem, 'pallet', sPallet);
    Vue.set(vmsQlP.dataItem, 'status', sStatus);
}

/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vmsRlP = new Vue({
  el: '#appRlP',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      staevent: 0,
    }
  }
})

/**
 *
 */
function classificateRlsP(obj) {
    oRow = $(obj).closest('tr')[0];

    var sItemCode = '';
    var sItem = '';
    var sUnit = '';
    var sLot = '';
    var sPallet = '';
    var sStatus = '';

    if (oRow != null) {
      sItemCode = oRow.children[0].textContent;
      sItem = oRow.children[1].textContent;
      sUnit = oRow.children[2].textContent;
      sLot = oRow.children[3].textContent;
      sPallet = oRow.children[4].textContent;
      sStatus = oRow.children[7].textContent;
    }

    Vue.set(vmsRlP.dataItem, 'item_code', sItemCode);
    Vue.set(vmsRlP.dataItem, 'item', sItem);
    Vue.set(vmsRlP.dataItem, 'unit', sUnit);
    Vue.set(vmsRlP.dataItem, 'lot', sLot);
    Vue.set(vmsRlP.dataItem, 'pallet', sPallet);
    Vue.set(vmsRlP.dataItem, 'status', sStatus);
}

/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vmsRfP = new Vue({
  el: '#appRfP',
  data: {
    dataItem : {
      item_code: 'NA',
      item: 'NA',
      unit: 'NA',
      lot: 'NA',
      pallet: 'NA',
      status: 'NA',
      staevent: 0,
    }
  }
})

/**
 *
 */
function classificateRfsP(obj) {
    oRow = $(obj).closest('tr')[0];

    var sItemCode = '';
    var sItem = '';
    var sUnit = '';
    var sLot = '';
    var sPallet = '';
    var sStatus = '';

    if (oRow != null) {
      sItemCode = oRow.children[0].textContent;
      sItem = oRow.children[1].textContent;
      sUnit = oRow.children[2].textContent;
      sLot = oRow.children[3].textContent;
      sPallet = oRow.children[4].textContent;
      sStatus = oRow.children[7].textContent;
    }
    Vue.set(vmsRfP.dataItem, 'item_code', sItemCode);
    Vue.set(vmsRfP.dataItem, 'item', sItem);
    Vue.set(vmsRfP.dataItem, 'unit', sUnit);
    Vue.set(vmsRfP.dataItem, 'lot', sLot);
    Vue.set(vmsRfP.dataItem, 'pallet', sPallet);
    Vue.set(vmsRfP.dataItem, 'status', sStatus);
}
function setAllQl(obj) {
    var dQuantity = 0;

    bCheck = document.getElementById('to_all_ql').checked;

    if (bCheck) {
      if (vmsQl.dataItem != 'undefined' && vmsQl.dataItem != null) {
        dQuantity = parseFloat(vmsQl.dataItem.quantity, 10);
      }
    }

    document.getElementById('quantityQl').value = dQuantity;
}
function setAllRl(obj) {
    var dQuantity = 0;

    bCheck = document.getElementById('to_all_rl').checked;

    if (bCheck) {
      if (vmsRl.dataItem != 'undefined' && vmsRl.dataItem != null) {
        dQuantity = parseFloat(vmsRl.dataItem.quantity, 10);
      }
    }

    document.getElementById('quantityRl').value = dQuantity;
}
function setAllRf(obj) {
    var dQuantity = 0;

    bCheck = document.getElementById('to_all_rf').checked;

    if (bCheck) {
      if (vmsRf.dataItem != 'undefined' && vmsRf.dataItem != null) {
        dQuantity = parseFloat(vmsRf.dataItem.quantity, 10);
      }
    }

    document.getElementById('quantityRf').value = dQuantity;
}


/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setDataQl() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();

    var dQuantity = parseFloat(document.getElementById('quantityQl').value, 10);
    var iStatusNew = parseInt(document.getElementById('statusQl').value, 10);

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
      },
      error: function () {
        console.log("error");
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
function setDataRl() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();

    var dQuantity = parseFloat(document.getElementById('quantityRl').value, 10);
    var iStatusNew = parseInt(document.getElementById('statusRl').value, 10);
    var sNote = document.getElementById('notesRl').value;

    dataRow.push(dQuantity);
    dataRow.push(iStatusNew);
    dataRow.push(sNote);

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
function setDataRf() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();

    var dQuantity = parseFloat(document.getElementById('quantityRf').value, 10);
    var iStatusNew = parseInt(document.getElementById('statusRf').value, 10);
    var warehouse = parseInt(document.getElementById('almacen').value,10);
    var ubicacion = parseInt(document.getElementById('ubicacion').value,10);
    var sNote = document.getElementById('notesRF').value;

    dataRow.push(dQuantity);
    dataRow.push(iStatusNew);
    dataRow.push(warehouse);
    dataRow.push(ubicacion);
    dataRow.push(sNote);


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

/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setDataQlP() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();
    var dQuantity = 0;
    var iStatusNew = parseInt(document.getElementById('statusQlP').value, 10);

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
      },
      error: function () {
        console.log("error");
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
function setDataRlP() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();
    var dQuantity = 0;
    var iStatusNew = parseInt(document.getElementById('statusRlP').value, 10);
    var sNote = document.getElementById('notesRlP').value;

    dataRow.push(dQuantity);
    dataRow.push(iStatusNew);
    dataRow.push(sNote);

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
function setDataRfP() {
    var table = $('#table_seg').DataTable();
    var row = table.row(oRow);
    var dataRow = row.data();
    var dQuantity = 0;
    var iStatusNew = parseInt(document.getElementById('statusRfP').value, 10);
    var warehouse = parseInt(document.getElementById('almacenP').value,10);
    var ubicacion = parseInt(document.getElementById('ubicacionP').value,10);
    var sNote = document.getElementById('notesRFP').value;

    dataRow.push(dQuantity);
    dataRow.push(iStatusNew);
    dataRow.push(warehouse);
    dataRow.push(ubicacion);
    dataRow.push(sNote);

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

$('#closeClassQl').on('click', function(e) {
    var dQuantity = parseFloat(document.getElementById('quantityQl').value, 10);
    var iStatusNew = parseInt(document.getElementById('statusQl').value, 10);

    if (dQuantity <= 0) {
      swal("Error", "La cantidad debe ser mayor a cero.", "error");
      return false;
    }

    if (vmsQl.dataItem != 'undefined' && vmsQl.dataItem != null) {
      dQuantityMax = parseFloat(vmsQl.dataItem.quantity, 10);
    }

    if (dQuantity > dQuantityMax){
      swal("Error", "La cantidad debe ser menor o igual al maximo.", "error");
      return false;
    }

    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    setDataQl();
});
$('#closeClassRl').on('click', function(e) {
    var dQuantity = parseFloat(document.getElementById('quantityRl').value, 10);
    var iStatusNew = parseInt(document.getElementById('statusRl').value, 10);

    if (dQuantity <= 0) {
      swal("Error", "La cantidad debe ser mayor a cero.", "error");
      return false;
    }

    if (vmsRl.dataItem != 'undefined' && vmsRl.dataItem != null) {
      dQuantityMax = parseFloat(vmsRl.dataItem.quantity, 10);
    }

    if (dQuantity > dQuantityMax){
      swal("Error", "La cantidad debe ser menor o igual al maximo.", "error");
      return false;
    }

    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    setDataRl();
});
$('#closeClassRf').on('click', function(e) {
    var dQuantity = parseFloat(document.getElementById('quantityRf').value, 10);
    var iStatusNew = parseInt(document.getElementById('statusRf').value, 10);
    var iLocation = parseInt(document.getElementById('ubicacion').value,10);
    var iWarehouse = parseInt(document.getElementById('almacen').value,10);

    if (dQuantity <= 0) {
      swal("Error", "La cantidad debe ser mayor a cero.", "error");
      return false;
    }

    if (vmsRf.dataItem != 'undefined' && vmsRf.dataItem != null) {
      dQuantityMax = parseFloat(vmsRf.dataItem.quantity, 10);
    }

    if (dQuantity > dQuantityMax){
      swal("Error", "La cantidad debe ser menor o igual al maximo.", "error");
      return false;
    }

    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    if(iWarehouse == 0) {
      swal("Error", "Debe seleccionar un almacen.", "error");
      return false;
    }

    if(iLocation == 0) {
      swal("Error", "Debe seleccionar una ubicacion.", "error");
      return false;
    }





    setDataRf();
});

$('#closeClassQlP').on('click', function(e) {
    var iStatusNew = parseInt(document.getElementById('statusQlP').value, 10);
    console.log(iStatusNew);
    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    setDataQlP();
});
$('#closeClassRlP').on('click', function(e) {

    var iStatusNew = parseInt(document.getElementById('statusRlP').value, 10);
    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    setDataRlP();
});
$('#closeClassRfP').on('click', function(e) {
    var iStatusNew = parseInt(document.getElementById('statusRfP').value, 10);
    var iLocation = parseInt(document.getElementById('ubicacionP').value,10);
    var iWarehouse = parseInt(document.getElementById('almacenP').value,10);

    if (! iStatusNew >= 1) {
      swal("Error", "Debe seleccionar un nuevo estatus.", "error");
      return false;
    }

    if(iWarehouse == 0) {
      swal("Error", "Debe seleccionar un almacen.", "error");
      return false;
    }

    if(iLocation == 0) {
      swal("Error", "Debe seleccionar una ubicacion.", "error");
      return false;
    }

    setDataRfP();
});
