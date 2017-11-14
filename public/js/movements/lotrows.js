var idParentTr = 0;
/*
* This method prepare the data to process in modal window
*/
$(document).on('click', 'button.buttlots', function () {
    idParentTr = $(this).closest('tr').attr('id');

    document.getElementById("qtyComplete").value = parseFloat(movement.rows[idParentTr].dQuantity).toFixed(8);

    $('#lotsbody').empty();
    console.log("readed " + idParentTr);

    if(idParentTr in movement.rows) {
      movement.rows[idParentTr].lotRows.forEach(function(lotRow) {
          $('#lotsbody').append(createLotRow(lotRow.identifier, lotRow.iLotId, lotRow.dQuantity, lotRow.dPrice));
      });
    }

    validateLots();
    return false;
});


function addRowAux() {
    $('#lotsbody').append(createLotRow(0, 0, 0, 0));
}

/*
* Creates a row of table in HTML
*/
function createLotRow(id, lotId, quantity, price) {
    var oTr = document.createElement("tr");

    oTr.setAttribute("id", "l" + id);

    var options = "";
    var itemLots = [];
    var firstLot = 1;
    var bLotSet = false;

    globalData.lLots.forEach(function(oLot) {
      if (oLot.item_id == movement.rows[idParentTr].iItemId && oLot.unit_id == movement.rows[idParentTr].iUnitId) {
          if (! bLotSet) {
            firstLot = oLot.id_lot;
          }
          options += "<option " + (lotId != 0 && lotId == oLot.id_lot ? "selected" : "") +
                              " value=" + oLot.id_lot + ">" +
                                    oLot.lot + " / " + oLot.dt_expiry +
                      "</option>";
          bLotSet = true;
      }
    });

    var iDefaultLot = lotId != 0 ? lotId : firstLot;
    var quantity = quantity != 0 ? quantity : 1;

    const LOT = 0;
    const QTY = 1;
    const PRICE = 2;
    const TOTAL = 3;
    const BTN_DEL = 4;
    const PARENT_TR = 5;
    const LOT_VALUE = 6;

    var valuesRow = [
                "",
                parseFloat(quantity).toFixed(5),
                parseFloat(price).toFixed(2),
                parseFloat(quantity * price).toFixed(2),
                "",
                id,
                iDefaultLot
              ];

    var oTdLOT = document.createElement("td");
    oTdLOT.appendChild(document.createTextNode(valuesRow[LOT]));
    oTdLOT.setAttribute("align", "center");
    oTdLOT.innerHTML = "<select onChange='setLot(this.value, this)' value=" + iDefaultLot + " class='form-control'>" + options + "</select>";

    var oTdQTY = document.createElement("td");
    oTdQTY.appendChild(document.createTextNode(valuesRow[QTY]));
    oTdQTY.setAttribute("contenteditable", "true");
    oTdQTY.setAttribute("align", "right");
    oTdQTY.setAttribute("onkeyup", "validateLots()");

    var oTdPRICE = document.createElement("td");
    oTdPRICE.appendChild(document.createTextNode(valuesRow[PRICE]));
    oTdPRICE.setAttribute("contenteditable", "true");
    oTdPRICE.setAttribute("align", "right");

    var oTdTOTAL = document.createElement("td");
    oTdTOTAL.appendChild(document.createTextNode(valuesRow[TOTAL]));
    oTdTOTAL.setAttribute("align", "right");

    var oTdBTN_DEL = document.createElement("td");
    oTdBTN_DEL.appendChild(document.createTextNode(valuesRow[BTN_DEL]));
    oTdBTN_DEL.innerHTML = "<button type='button' onClick='validateLots()' class='removeLotbutton btn btn-danger btn-xs' title='Quitar renglón'>" +
                      "<li class='glyphicon glyphicon-remove'></li>"
                      "</button>";

    var oTdPARENT_TR = document.createElement("td");
    oTdPARENT_TR.appendChild(document.createTextNode(valuesRow[PARENT_TR]));
    oTdPARENT_TR.setAttribute("style", "display:none;");

    var oTdLOT_VALUE = document.createElement("td");
    oTdLOT_VALUE.appendChild(document.createTextNode(valuesRow[LOT_VALUE]));
    oTdLOT_VALUE.setAttribute("class", "lot_value");
    oTdLOT_VALUE.setAttribute("style", "display:none;");

    oTr.appendChild(oTdLOT);
    oTr.appendChild(oTdQTY);
    oTr.appendChild(oTdPRICE);
    oTr.appendChild(oTdTOTAL);
    oTr.appendChild(oTdBTN_DEL);
    oTr.appendChild(oTdPARENT_TR);
    oTr.appendChild(oTdLOT_VALUE);

    return oTr;
}

/*
* This method removes a tr of table when the button of delete is pressed
* the button should have the removebutton class
*/
$(document).on('click', 'button.removeLotbutton', function () {
    var id = $(this).closest('tr').attr('id').substring(1, $(this).closest('tr').attr('id').length);

    $(this).closest('tr').remove();
    movement.rows[idParentTr].removeLotRow(id);
    console.log("removed " + id);
    validateLots();
    return false;
});

/*
* Set value of lot when is changed on html table
*/
function setLot(value, obj) {
  $(obj).closest('tr').children('td.lot_value').html(value);
}

/*
* Calls the method to save table when the button close of modal window is clicked
*/
$('#closeModal').on('click', function(e) {
  pushLotTableRows(idParentTr);
});

/*
* Saves the lot rows in the js object
*/
function pushLotTableRows(parent) {
  const LOT = 0;
  const QTY = 1;
  const PRICE = 2;
  const TOTAL = 3;
  const BTN_DEL = 4;
  const PARENT_TR = 5;
  const LOT_VALUE = 6;

  var tableObj = document.getElementById('lotsTable');
  console.log("push " + idParentTr);

  movement.rows[idParentTr].lotRows = [];
  $('#lotsbody tr').each(function(index, element) {
    var dQuantity = $(element).find("td").eq(QTY).html();
    var dPrice = $(element).find("td").eq(PRICE).html();
    var dLotId = $(element).find("td").eq(LOT_VALUE).html();

    addRowOfLot(idParentTr, dLotId, dQuantity, dPrice);
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

  var dQuantity = document.getElementById('qtyComplete').value;
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
  if (dQuantity == total) {
    btnClose.disabled = false;
  }
  else {
    btnClose.disabled = true;
  }
}

/*
* If lot exists just update the quantity, else add a lot row
*/
function addOrUpdateLotRow(idParent, idLot, quantity, price) {
    var bLotExists = false;
    movement.rows[idParent].lotRows.forEach(function(lotR) {
        if (lotR.iLotId == idLot) {
            lotR.dQuantity += quantity;
            bLotExists = true;
            return true;
        }
    });

    if (! bLotExists) {
      addRowOfLot(idParent, idLot, quantity, price);
    }
}

/*
* Add a new lotRow on the new position
*/
function addRowOfLot(parent, lot, qty, price) {
    var lotRow = new SLotRow(movement.rows[parent].identifier, lot, qty, price);
    movement.rows[parent].addLotRow(lotRow);
}
