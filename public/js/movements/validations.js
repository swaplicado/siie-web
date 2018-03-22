/*
* When freeze is pressed, the field of item, quantity and the button
* of add are disabled, but the data of the movement is send to server too
*/
function unfreeze() {
  var fre = document.getElementById("idFreeze"); // freeze button
  var sBut = document.getElementById("saveButton"); // save button

  if (fre.firstChild.data == "Congelar") {
    if (validateMovement(oMovement)) {
        guiValidations.hidePanel();
        guiValidations.hideDelete();
        guiTransSupp.hideSetDataButton();
        guiTransSupp.hideCleanDataButton();
        sBut.disabled = false;
        setMovementToForm();

        fre.innerHTML = "Descongelar";
    }
  }
  else {
    guiValidations.showPanel();
    guiValidations.showDelete();
    guiTransSupp.showSetDataButton();
    sBut.disabled = true;
    fre.innerHTML = "Congelar";
  }
}

/*
* Validate the movement rows in whsmovs.blade.php
*/
function validateMovement(oMovement) {

  if (oMovement.rows.size == 0) {
    swal("Error", "No ha agregado movimientos.", "error");
    return false;
  }

  var valid = true;
  for (var [key, row] of oMovement.rows) {
    if (row.iPalletId == 0) {
        swal("Error", "El renglón " + row.sItem + " no tiene una tarima seleccionada.", "error");
        valid = false;
        break;
    }

    if (row.bIsLot && row.lotRows.size == 0) {
        swal("Error", "El renglón " + row.sItem  + " no tiene lotes asignados.", "error");
        valid = false;
        break;
    }

    if (Number.isNaN(Number.parseFloat(row.dQuantity))) {
        swal("Error", "Debe ingresar sólo números en los campos cantidad y precio.", "error");
        valid = false;
        break;
    }

    if (!row.bIsBulk && isFloat(parseFloat(row.dQuantity))) {
        swal("Error", "El renglón " + row.sItem + " no acepta decimales.", "error");
        valid = false;
        break;
    }
  }

  if (! valid) {
    return false;
  }

  return true;
}

class QtyItemAux {
   constructor(quantity, item) {
     this.dQuantity = quantity;
     this.sItem = item;
   }
}

class LotRow {
   constructor(lot, quantity) {
     this.iLotId = lot;
     this.dQuantity = quantity;
   }
}

function isItemLot(sItemCode) {
    var isLot = false;
    movement.rows.forEach(function(element) {
      if (sItemCode == element.oAuxItem.code) {
        isLot = element.oAuxItem.is_lot;
        return true;
      }
    });

    return isLot;
}

/*
* Validate the input of user in whsmovs.blade.php
*/
function validateInput() {
    var sItemCode = document.getElementById("item").value; // gets the code of code bar
    var dInputQuantity = document.getElementById("quantity").value; // gets the quantity
    var iMovType = document.getElementById('mvt_whs_type_id').value;

    var iWhsSrcId = 0;
    var iWhsDesId = 0;

    if (iMovType == globalData.MVT_TP_OUT_TRA) {
      iWhsSrcId = document.getElementById("whs_src").value;
      iWhsDesId = document.getElementById("whs_des").value;
    }
    else {
      if (!globalData.bIsInputMov) {
          iWhsSrcId = document.getElementById("whs_src").value;
      }
      else {
          iWhsDesId = document.getElementById("whs_des").value;
      }
    }

    if (iWhsSrcId == 0 && (!globalData.bIsInputMov || iMovType == globalData.MVT_TP_OUT_TRA)) {
      swal("Aviso", "Debe elegir un almacén origen.", "warning");
      document.getElementById("item").value = '';
      return false;
    }
    if (iWhsDesId == 0 && (globalData.bIsInputMov || iMovType == globalData.MVT_TP_OUT_TRA)) {
      swal("Aviso", "Debe elegir un almacén destino.", "warning");
      document.getElementById("item").value = '';
      return false;
    }

    if (iMovType == globalData.MVT_TP_OUT_TRA) {
      movement.iWhsDes = parseInt(document.getElementById("whs_des").value);
      $('#whs_des').prop('disabled', true).trigger("chosen:updated");
      // document.getElementById('whs_des').setAttribute("disabled", "disabled");

      movement.iWhsSrc = parseInt(document.getElementById("whs_src").value);
      $('#whs_src').prop('disabled', true).trigger("chosen:updated");
      // document.getElementById('whs_src').setAttribute("disabled", "disabled");
    }
    else {
      if (globalData.bIsInputMov) {
        movement.iWhsDes = parseInt(document.getElementById("whs_des").value);
        $('#whs_des').prop('disabled', true).trigger("chosen:updated");
        // document.getElementById('whs_des').setAttribute("disabled", "disabled");
      }
      else {
        movement.iWhsSrc = parseInt(document.getElementById("whs_src").value);
        $('#whs_src').prop('disabled', true).trigger("chosen:updated");
        // document.getElementById('whs_src').prop('disabled', true).trigger("chosen:updated");
      }
    }

    if (sItemCode == '') {
      swal("Aviso", "Debe ingresar un código.", "warning");
      return false;
    }
    else {
      // document.getElementById("item").value = '';
    }

    if (dInputQuantity <= 0) {
      swal("Aviso", "La cantidad no puede ser menor o igual a cero.", "warning");
      return false;
    }

    return true;
}

/*
* Validate sum of quantity in modal window
* and enable or disable the close button
*/
function validateLots(parentId) {
    var total = 0.0;
    var column = 1;
    var columnLot = 0;
    var bCeros = false;
    var bLots = true;
    var sLotExp = '';
    var bNumber = true;
    var bBulk = true;

    var parentRow = movement.getRow(parentId);

    // In this way using eq we select the second row, since the first one is 0
    $("#lotsbody tr").find('td:eq(' + column + ')').each(function () {

     //get the value from cell
      valor = $(this)[0].children[0].value;
      if (Number.isNaN(Number.parseFloat(valor))) {
          bNumber = false;
          return false;
      }
      if (valor <= 0) {
          bCeros = true;
          return false;
      }

      if (!parentRow.oAuxItem.is_bulk && isFloat(parseFloat(valor))) {
          bBulk = false;
          return false;
      }

     //parse and sum
      total += parseFloat(valor)
    });

    if (parentRow.oAuxItem.is_lot) {
      if ($("#lotsbody tr").find('td:eq(' + columnLot + ')').length <= 0) {
        bLots = false;
      }
    }

    $("#lotsbody tr").find('td:eq(' + columnLot + ')').each(function () {
        valor = $(this)[0].children[0].value;
    //     if (valor == '') {
    //         bLots = false;
    //         return false;
    //     }
    //     else {
          sLotExp = lotsRotation(valor);
    //     }
    });

    btnClose = document.getElementById('closeModal');

    // var dQuantity = document.getElementById('qtyComplete').value;
    // Valid if the amount to complete is correct and enable and disable the button
    if (!bNumber) {
      swal("Error", "Debe ingresar sólo números en los campos cantidad y precio.", "error");
      return false;
    }
    if (bCeros) {
      swal("Aviso", "No puede haber renglones con cantidad menor o igual a cero.", "warning");
      return false;
    }
    if (!bBulk) {
      swal("Aviso", "No se admiten decimales para este material/producto.", "warning");
      return false;
    }
    if (!bLots) {
      swal("Aviso", "Debe seleccionar un lote.", "warning");
      return false;
    }
    if (sLotExp != '') {
      var validation = confirm("el lote " + sLotExp + " no es el más próximo a caducar. \n ¿Desea continuar?");

      if (!validation) {
        return false;
      }
    }

    var iMovType = document.getElementById('mvt_whs_type_id').value;


    if (iMovType == globalData.MVT_TP_OUT_TRA && parentRow.iPalletId != 1) {
      btnClose.disabled = true;
      return false;
    }
    else {
      btnClose.disabled = false;
    }

    return true;
}

function lotsRotation(lotId) {
  var oLot = null;
  globalData.lLots.forEach(function(lot) {
    if (lot.id_lot == lotId) {
      oLot = lot;
      return true;
    }
  });

  var oBestLot = null
  globalData.lLots.forEach(function(lot) {
    if (oLot.unit_id == lot.unit_id && oLot.item_id == lot.item_id) {
        if (oBestLot == null || lot.dt_expiry < oBestLot.dt_expiry) {
          oBestLot = lot;
        }
    }
  });

  if (lotId != oBestLot.id_lot) {
    return oLot.lot;
  }
  else {
    return '';
  }
}
