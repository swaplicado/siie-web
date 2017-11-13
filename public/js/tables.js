var idParentTr = 0;
var idCurrentLot = 0;
var idItem = 0;
var itmQty = 0;
var table = [];
var bandera = 0;

/*
* Add a new row based in the code and quantity that the user writes in the
* fields, in the view of movs
* Row of movement
*/
function addRow(e) {
  var item = document.getElementById("item").value; // gets the code of code bar
  var qty = document.getElementById("quantity").value; // gets the quantity

  var whs = 0;
  if (bInput) {
    whs = document.getElementById("whs_des").value;
  }
  else {
    whs = document.getElementById("whs_src").value;
  }

  if (whs == 0) {
    alert('Debe elegir un almacén');
    document.getElementById("item").value = '';
    return false;
  }
  else {
    if (bInput) {
      document.getElementById('whs_des').setAttribute("readonly", "readonly");
    }
    else {
      document.getElementById('whs_src').setAttribute("readonly", "readonly");
    }
  }

  if (item == '') {
    alert('Debe ingresar un código');
    return false;
  }
  else {
    document.getElementById("item").value = '';
  }

  const DOWN = 0;
  const ITEM_CODE = 1;
  const ITEM_NAME = 2;
  const UNIT_CODE = 3;
  const LOCATION = 4;
  const LOTS = 5;
  const PALLETS = 6;
  const PRICE = 7;
  const QUANTITY = 8;
  const DEL = 9;
  const ITEM_ID = 10;
  const UNIT_ID = 11;
  const LOC_ID = 12;
  const PALL_ID = 13;

  var parent = e.target.value;
  var idLot = 0;
  //ajax
  $.get('./create/children?parent=' + item, function(data) { // executes the method children of controller
      $('.dataTables_empty').remove(); // remove the row with the class: dataTables_empty
      var found = false;
      $.each(data, function(index, oMovementRow) {
        found = true;
        idLot = oMovementRow.aux_lot_id;
        var idRow = movement.identifier; // gets the identifier of current row
        movement.addRow(); // indicates that a new row was added
        var rowQuantity = oMovementRow.quantity > 0 ? oMovementRow.quantity : qty;

        var tblBody = document.getElementById("lbody"); // gets the body of table by id

        var bLocSet = false;
        var firstLoc = 0;
        var options = "";
        for (var iLoc = 0; iLoc < locationsjs.length; iLoc++) {
          if (locationsjs[iLoc].whs_id == whs) {
              if (!bLocSet) {
                firstLoc = locationsjs[iLoc].id_whs_location;
              }
              bLocSet = true;
              options += "<option value=" + locationsjs[iLoc].id_whs_location + ">" + locationsjs[iLoc].code + " - " + locationsjs[iLoc].name + "</option>";
          }
        }

        var bPallSet = false;
        var firstPall = 1;
        var optionsPall = "";
        for (var iPall = 0; iPall < palletsjs.length; iPall++) {
          if ((palletsjs[iPall].item_id == oMovementRow.item_id && palletsjs[iPall].unit_id == oMovementRow.unit_id) || palletsjs[iPall].id_pallet == 1) {
              if (!bPallSet) {
                firstPall = palletsjs[iPall].id_pallet;
              }
              bPallSet = true;
              optionsPall += "<option value=" + palletsjs[iPall].id_pallet + ">" + palletsjs[iPall].pallet + "</option>";
          }
        }

        var values = [ //this is the array of values for the row
                    idRow,
                    oMovementRow.item.code,
                    oMovementRow.item.name,
                    oMovementRow.unit.code,
                    "",
                    "  Lotes",
                    "",
                    parseFloat(0.0).toFixed(8),
                    parseFloat(rowQuantity).toFixed(8),
                    "",
                    parseInt(oMovementRow.item_id),
                    parseInt(oMovementRow.unit_id),
                    firstLoc,
                    firstPall
                  ];

        var oTr = document.createElement("tr");

        oTr.setAttribute("class", "clickable");
        oTr.setAttribute("data-toggle", "collapse");
        oTr.setAttribute("id", idRow);
        oTr.setAttribute("data-target", "." + idRow);

        for (i = 0; i < values.length; i++) {
          var oTd = document.createElement("td");
          var textTd = document.createTextNode(values[i]);

          if (i == DOWN) {
            // oTd.innerHTML = "<button type='button' class='btn btn-success btn-xs' title='Ver lotes'>" +
            // "<i class='glyphicon glyphicon-menu-down'></i>" +
            // "</button>";
            oTd.setAttribute("style", "display:none;");
          }
          if (i == LOTS) {
            oTd.innerHTML = "<button type='button' class='buttlots btn btn-info btn-xs' data-toggle='modal' data-target='#myModal' title='Agregar lote'>" +
            "<i class='glyphicon glyphicon-list-alt'></i>" +
            "</button>";
          }
          if (i == LOCATION) {
            oTr.setAttribute("align", "center");
            oTd.innerHTML = "<select onChange='setLoc(this.value, this)' class='form-control'>" + options + "</select>";
          }
          if (i == PALLETS) {
            oTr.setAttribute("align", "center");
            oTd.innerHTML = "<select onChange='setPall(this.value, this)' class='form-control'>" + optionsPall + "</select>";
          }
          if (i == QUANTITY) {
            oTd.setAttribute("class", "summ clsqty");
            oTd.setAttribute("align", "right");
            oTd.setAttribute("contenteditable", "true");
          }
          if (i == DEL) {
            oTd.innerHTML = "<button type='button' class='removebutton btn btn-danger btn-xs' title='Quitar renglón'>" +
            "<li class='glyphicon glyphicon-remove'></li>"
            "</button>";
          }
          if (i == PRICE) {
            oTd.setAttribute("align", "right");
            oTd.setAttribute("contenteditable", "true");
          }
          if (i > DEL) {
              oTd.setAttribute("style", "display:none;");
              if (i == ITEM_ID) {
                oTd.setAttribute("class", "id_item");
              }
              if (i == UNIT_ID) {
                oTd.setAttribute("class", "id_unit");
              }
              if (i == LOC_ID) {
                oTd.setAttribute("class", "id_loc");
              }
              if (i == PALL_ID) {
                oTd.setAttribute("class", "id_pall");
              }
          }
          oTd.appendChild(textTd);

          oTr.appendChild(oTd);
        }

        tblBody.appendChild(oTr);

        if (idLot != 0) { // if the code was of lot the lot id is send to add a lot row
          console.log("to add lotrow lot: " + idLot + " itm " + oMovementRow.item_id + " u " + oMovementRow.unit_id);
          $('#lotsbody').empty();
          console.log("readed " + idRow);

          if(idRow in datas.lotTables) {
            console.log(datas.lotTables[idRow]);
            for (var i = 0; i < datas.lotTables[idRow].length; i++) {
              $('#lotsbody').append(datas.lotTables[idRow][i]);
            }
          }
          validateLots();
          addLotRow(idLot, 0, qty, oMovementRow.item_id, oMovementRow.unit_id, idRow);
        }
      });

      if (! found) {
        alert("No se encontraron resultados");
      }
    });
}

/*
* This method removes a tr of table when the button of delete is pressed
* the button should have the removebutton class
*/
$(document).on('click', 'button.removebutton', function () {
    datas.lotRows.splice($(this).closest('tr').attr('id'), 1);
    datas.lotTables.splice($(this).closest('tr').attr('id'), 1);
    $(this).closest('tr').remove();
    return false;
});
/*
* This method removes a tr of table when the button of delete is pressed
* the button should have the removebutton class
*/
$(document).on('click', 'button.removeLotbutton', function () {
    $(this).closest('tr').remove();
    validateLots();
    return false;
});

/*
* This method prepare the data to process in modal window
*/
$(document).on('click', 'button.buttlots', function () {
    idParentTr = $(this).closest('tr').attr('id');
    idItem = $(this).closest('tr').children('td.id_item').text();
    idUnit= $(this).closest('tr').children('td.id_unit').text();
    itmQty= $(this).closest('tr').children('td.clsqty').text();

    document.getElementById("qtyComplete").value = parseFloat(itmQty).toFixed(8);

    $('#lotsbody').empty();
    console.log("readed " + idParentTr);

    if(idParentTr in datas.lotTables) {
      console.log(datas.lotTables[idParentTr]);
      for (var i = 0; i < datas.lotTables[idParentTr].length; i++) {
        $('#lotsbody').append(datas.lotTables[idParentTr][i]);
      }
    }
    validateLots();
    return false;
});

/*
* This method calls the method to add row of lot in modal window
*/
$(document).on('click', 'button.addbutton', function () {
    addLotRow(0, 0, 0);
    return false;
});

/*
* This method add a row in modal window
*/
function addLotRow(iIdLot, dPrice, dQuantity, iIdItem, iIdUnit, iParentId) {
    var tbloBody = document.getElementById("lotsbody"); // gets the body of table by id

    var oTr = document.createElement("tr");
    idCurrentLot = lotsCounter;
    idUnit = iIdUnit == 0 ? idUnit : iIdUnit;
    idItem = iIdItem == 0 ? idItem : iIdItem;
    idParentTr = iParentId == 0 ? idParentTr : iParentId;

    oTr.setAttribute("id", "trLot" + idCurrentLot);

    var options = "";
    var itemLots = [];
    var firstLot = 1;
    var bLotSet = false;
    console.log("antes lotes");
    for (var iLot = 0; iLot < lotsjs.length; iLot++) {
      console.log("en lotes: " + idItem + " u: " + idUnit);
        if (lotsjs[iLot].item_id == idItem && lotsjs[iLot].unit_id == idUnit) {
            if (! bLotSet) {
              firstLot = lotsjs[iLot].id_lot;
            }
            if (iIdLot != 0) {}
            options += "<option " + (iIdLot != 0 && iIdLot == lotsjs[iLot].id_lot ? "selected" : "") + " value=" + lotsjs[iLot].id_lot + ">" + lotsjs[iLot].lot + " / " + lotsjs[iLot].dt_expiry + "</option>";
            bLotSet = true;
        }
    }

    var lot = iIdLot != 0 ? iIdLot : firstLot;
    var quantity = dQuantity != 0 ? dQuantity : 1;

    const LOT = 0;
    const QTY = 1;
    const PRICE = 2;
    const TOTAL = 3;
    const BTN_DEL = 4;
    const PARENT_TR = 5;
    const LOT_VALUE = 6;

    var valuesRow = [
                "",
                parseFloat(quantity).toFixed(8),
                parseFloat(dPrice).toFixed(8),
                parseFloat(quantity * dPrice).toFixed(8),
                "",
                idParentTr,
                lot
              ];

    for (var iTd = 0; iTd < valuesRow.length; iTd++) {
      var oTd = document.createElement("td");
      var textTd = document.createTextNode(valuesRow[iTd]);
      oTd.appendChild(textTd);

      if (iTd != LOT) {
        oTd.setAttribute("align", "right");
        if (iTd == QTY || iTd == PRICE) {
          oTd.setAttribute("contenteditable", "true");
        }
        if (iTd == QTY) {
          oTd.setAttribute("onkeyup", "validateLots()");
        }
        if (iTd == BTN_DEL) {
          oTd.innerHTML = "<button type='button' onClick='validateLots()' class='removeLotbutton btn btn-danger btn-xs' title='Quitar renglón'>" +
                            "<li class='glyphicon glyphicon-remove'></li>"
                            "</button>";
        }
        if (iTd == PARENT_TR) {
          oTd.setAttribute("style", "display:none;");
        }
        if (iTd == LOT_VALUE) {
          oTd.setAttribute("class", "lot_value");
          oTd.setAttribute("style", "display:none;");
        }
      }
      else {
        oTr.setAttribute("align", "center");
        oTd.innerHTML = "<select onChange='setLot(this.value, this)' value=" + lot + " class='form-control'>" + options + "</select>";
    }

    oTr.appendChild(oTd);
    }

    tbloBody.appendChild(oTr);
    lotsCounter++;
    validateLots();
    console.log("before push");
    pushLotRow(iParentId);
}


/*
* Calls the method to save table when the button close of modal window is clicked
*/
$('#closeModal').on('click', function(e) {
  pushLotRow(0);
});

function pushLotRow(parent) {
  var tableObj = document.getElementById('lotsTable');
  tableTrs = [];
  console.log("push " + idParentTr);
  $("tbody#lotsbody tr").each(function() {
    tableTrs.push($(this));
  });

  datas.addRow(idParentTr, $('#lotsTable').tableToJSON(), tableTrs);
}

function setLot(value, obj) {
  $(obj).closest('tr').children('td.lot_value').html(value);
}

function setLoc(value, obj) {
  $(obj).closest('tr').children('td.id_loc').html(value);
}

function setPall(value, obj) {
  $(obj).closest('tr').children('td.id_pall').html(value);
}

/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setData(data) {
    // var table = $('#example').tableToJSON();

    var data = { value : data };
      $.ajax({
        type: "POST",
        url: './create/storetable',
        data: data,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          console.log("Value added ");
        }
      });
}

/*
* Validate sum of quantity in modal window
* and enable or disable the close button
*/
function validateLots() {
  var total = 0.0;
  var column = 1;
  var columnLot = 6;
  var bCeros = false;
  var bLots = false;

  // In this way using eq we select the second row, since the first one is 0
  $("#lotsbody tr").find('td:eq(' + column + ')').each(function () {

   //get the value from cell
    valor = $(this).html();
    if (valor <= 0) {
      bCeros = true;
    }

   //parse and sum
    total += parseFloat(valor)
  })

  $("#lotsbody tr").find('td:eq(' + columnLot + ')').each(function () {

   //get the value from cell
    valor = $(this).html();
    if (valor == 0 || valor == '') {
      bLots = true;
    }
  })

  btnClose = document.getElementById('closeModal');

  // Valid if the amount to complete is correct and enable and disable the button
  if (bCeros) {
    btnClose.disabled = true;
    alert("No puede haber renglones con cantidad menor o igual a cero");
    return false;
  }
  if (bLots) {
    btnClose.disabled = true;
    alert("No puede haber renglones sin lote");
    return false;
  }
  if (itmQty == total) {
    btnClose.disabled = false;
  }
  else {
    btnClose.disabled = true;
  }
}
