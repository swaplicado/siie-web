var idParentTr = 0;
var vue = '';
/*
* This method prepare the data to process in modal window
*/
$(document).on('click', 'button.buttlots', function () {
    idParentTr = $(this).closest('tr').attr('id');

    // document.getElementById("qtyComplete").value = parseFloat(movement.getRow(idParentTr).dQuantity).toFixed(globalData.DEC_QTY);

    // $('#lotsbody').empty();
    console.log("readed " + idParentTr);

    var row = movement.getRow(idParentTr);

    if(row) {
      attachRow(row, false);
    }

    // validateLots(idParentTr);
    return false;
});

function attachRow(row, bPalletReconfiguration) {
    $('#lotsbody').empty();

    if (row.iPalletId != 1 && movement.iMvtType == globalData.MVT_TP_OUT_TRA) {
      document.getElementById('addBtn').disabled = true;
    }
    else
    {
      document.getElementById('addBtn').disabled = false;
    }

    row.lotRows.forEach(function(lotRow) {
        $('#lotsbody').append(createLotRow(row, lotRow.identifier, lotRow.iLotId, lotRow.dQuantity, lotRow.dPrice, bPalletReconfiguration));
    });
}


function addRowAux() {
    $('#lotsbody').append(createLotRow(null, 0, 0, 0, 0, false));
}

/*
* Creates a row of table in HTML
*/
function createLotRow(row, id, lotId, quantity, price, bPalletReconfiguration) {
    var oTr = document.createElement("tr");
    var iMovType = parseInt(document.getElementById('mvt_whs_type_id').value);
    var parentRow = row == null ? movement.getRow(idParentTr) : row;

    oTr.setAttribute("id", "l" + id);

    var options = "";
    var itemLots = [];
    var firstLot = 1;
    var bLotSet = false;

    globalData.lLots.forEach(function(oLot) {
      if (oLot.item_id == parentRow.iItemId && oLot.unit_id == parentRow.iUnitId) {
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
                parseFloat(quantity).toFixed(globalData.DEC_QTY),
                parseFloat(price).toFixed(globalData.DEC_AMT),
                parseFloat(quantity * price).toFixed(globalData.DEC_AMT),
                "",
                id,
                iDefaultLot
              ];

    var oTdLOT = document.createElement("td");
    oTdLOT.appendChild(document.createTextNode(valuesRow[LOT]));
    oTdLOT.setAttribute("align", "center");
    oTdLOT.innerHTML = "<select " +
                          ((iMovType == globalData.MVT_TP_OUT_TRA && parentRow.iPalletId != 1) || bPalletReconfiguration ? "disabled='true'" : "") +
                           " onChange='setLot(this.value, this)' class='form-control'>" + options + "</select>";

    var oTdQTY = document.createElement("td");
    oTdQTY.innerHTML = "<input align='right' class='form-control' type='number' " +
                                ((iMovType == globalData.MVT_TP_OUT_TRA && parentRow.iPalletId != 1) || bPalletReconfiguration ? "readonly='readonly'" : "") +
                                " placeholder='1.00' step='0.01' min='0' maxlength='15' size='5' value='" +
                                valuesRow[QTY] + "'>";

    var oTdPRICE = document.createElement("td");
    oTdPRICE.innerHTML = "<input align='right' class='form-control' type='number' " +
                                ((iMovType == globalData.MVT_TP_OUT_TRA && parentRow.iPalletId != 1) || bPalletReconfiguration ? "readonly='readonly'" : "") +
                                " placeholder='1.00' step='0.01' min='0' maxlength='15' size='5' value='" +
                                valuesRow[PRICE] + "'>";

    var oTdTOTAL = document.createElement("td");
    oTdTOTAL.appendChild(document.createTextNode(valuesRow[TOTAL]));
    oTdTOTAL.setAttribute("align", "right");

    var oTdBTN_DEL = document.createElement("td");
    oTdBTN_DEL.appendChild(document.createTextNode(valuesRow[BTN_DEL]));
    if ((parentRow.iPalletId == 1 || (iMovType != globalData.MVT_TP_OUT_TRA && parentRow.iPalletId != 1)) && !bPalletReconfiguration ) {
      oTdBTN_DEL.innerHTML = "<button type='button' " +
      // "onClick='validateLots(" + idParentTr + ")'"  +
      "class='removeLotbutton btn btn-danger btn-xs' title='Quitar renglón'>" +
                        "<li class='glyphicon glyphicon-remove'></li>"
                        "</button>";
    }

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
    movement.getRow(idParentTr).removeLotRow(id);
    console.log("removed " + id);
    // validateLots(idParentTr);
    return false;
});

/*
* Set value of lot when is changed on html table
*/
function setLot(value, obj) {
  var id = $(obj).closest('tr').attr('id').substring(1, $(obj).closest('tr').attr('id').length);
  $(obj).closest('tr').children('td.lot_value').html(value);
  movement.getRow(idParentTr).updateLot(id, parseInt(value));
}

/*
* Calls the method to save table when the button close of modal window is clicked
*/
$('#closeModal').on('click', function(e) {
    if (! validateLots(idParentTr)) {
      return false;
    }

    pushLotTableRows(idParentTr);

    var qty = 0;
    movement.getRow(idParentTr).lotRows.forEach(function(row) {
      qty += row.dQuantity;
    });

    updateRowTr(idParentTr, qty);
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
  console.log("push " + parent);

  movement.getRow(idParentTr).lotRows = [];
  $('#lotsbody tr').each(function(index, element) {
    var dQuantity = parseFloat($(element).find("td").eq(QTY)[0].children[0].value);
    var dPrice = parseFloat($(element).find("td").eq(PRICE)[0].children[0].value);
    var dLotId = parseInt($(element).find("td").eq(LOT_VALUE).html());

    addOrUpdateLotRow(parent, dLotId, dQuantity, dPrice);
  });
}

/*
* If lot exists just update the quantity, else add a lot row
*/
function addOrUpdateLotRow(idParent, idLot, quantity, price) {
    var bLotExists = false;
    movement.getRow(idParent).lotRows.forEach(function(lotR) {
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
    var lotRow = new SLotRow(movement.getRow(parent).idLotRow, lot, qty, price);
    movement.getRow(parent).addLotRow(lotRow);
}
