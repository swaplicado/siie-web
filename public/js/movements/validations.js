/*
* When freeze is pressed, the field of item, quantity and the button
* of add are disabled, but the data of the movement is send to server too
*/
function unfreeze() {
  var fre = document.getElementById("idFreeze"); // freeze button
  var but = document.getElementById("tButton"); // Add button
  var item = document.getElementById("item"); // item field
  var qty = document.getElementById("quantity"); // quantity field
  var sBut = document.getElementById("saveButton"); // save button

  if (fre.firstChild.data == "Congelar") {
    if (globalData.isPalletReconfiguration) {
      movement.auxPalletRow = oPalletRow;
    }
    else {
      movement.auxPalletRow = '';
    }

    if (validateMovement(movement)) {
        but.disabled = true;
        item.disabled = true;
        qty.disabled = true;
        sBut.disabled = false;

        $(function(){
          $("button.removebutton").attr("disabled", true);
          $("button.buttlots").attr("disabled", true);
          // $("button.butstk").attr("disabled", true);
          $("select.selPallet").attr("disabled", true);
        });

        if (globalData.iMvtType == globalData.PALLET_RECONFIG_IN) {
          discountMovements(movement);
        }

        setData(movement); //the table is sends to the server

        fre.innerHTML = "Descongelar";
    }
  }
  else {
    but.disabled = false;
    item.disabled = false;
    qty.disabled = false;
    sBut.disabled = true;

    $(function() {
      $("button.removebutton").attr("disabled", false);
      if (globalData.iMvtType != globalData.MVT_TP_OUT_TRA && !globalData.isPalletReconfiguration) {
        $("select.selPallet").attr("disabled", false);
      }
      $("button.buttlots").attr("disabled", false);
      // $("button.butstk").attr("disabled", false);

    });

    setData("");

    fre.innerHTML = "Congelar";
  }
}

/*
* Validate the movement rows in whsmovs.blade.php
*/
function validateMovement(oMovement) {

  if (oMovement.rows.length == 0) {
    swal("Error", "No ha agregado movimientos.", "error");
    return false;
  }

  var valid = true;
  var rowIndex = 1;
  oMovement.rows.forEach(function(row) {
      if (row.oAuxItem.is_lot && row.lotRows.length == 0) {
          swal("Error", "El renglón " + row.oAuxItem.name + " no tiene lotes asignados.", "error");
          valid = false;
          return true;
      }

      if (Number.isNaN(Number.parseFloat(row.dQuantity))) {
          swal("Error", "Debe ingresar sólo números en los campos cantidad y precio.", "error");
          valid = false;
          return true;
      }

      if (!row.oAuxItem.is_bulk && isFloat(parseFloat(row.dQuantity))) {
          swal("Error", "El renglón " + row.oAuxItem.name + " no acepta decimales.", "error");
          valid = false;
          return true;
      }

      var qtySum = 0.0;
      row.lotRows.forEach(function(lotRow) {
          qtySum += lotRow.dQuantity;

          if (row.oAuxItem.is_lot && lotRow.iLotId <= 0) {
            swal("Error", "El código " + row.sAuxItemCode + " no tiene un lote asignado.", "error");
            valid = false;
            return true;
          }
      });

      if (row.oAuxItem.is_lot && qtySum != row.dQuantity) {
          swal("Error", "Asignación de lotes inconsistente en el material/producto " + row.oAuxItem.name + ", renglón " + rowIndex + ".", "error");
          valid = false;
          return true;
      }
      rowIndex++;
  });

  if (!valid) {
    return false;
  }

  if (oMovement.iMvtType == globalData.PALLET_RECONFIG_IN) {
      var totalQuantity = 0;
      var palletLotRows = [];

      oMovement.auxPalletRow.lotRows.forEach(function(row) {
          palletLotRows.push(new LotRow(row.iLotId, row.dQuantity)); //LotRow in pallets.js
      });

      oMovement.rows.forEach(function(row) {
        totalQuantity += row.dQuantity;

        row.lotRows.forEach(function(lotRow) {
            palletLotRows.forEach(function(palletLot) {
                if (lotRow.iLotId == palletLot.iLotId) {
                    palletLot.dQuantity -= lotRow.dQuantity;
                }
            });
        });
      });

      if (totalQuantity > oMovement.auxPalletRow.dQuantity) {
        swal("Error", "No puede mover más unidades de las que contiene la tarima.", "error");
        valid = false;
        return false;
      }

      palletLotRows.forEach(function(palletLot) {
          if (palletLot.dQuantity < 0) {
            swal("Error", "No hay suficientes existencias de lote " + palletLot.iLotId + " en la tarima.", "error");
            valid = false;
            return true;
          }
      });
  }

  return valid;
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
    var columnLot = 6;
    var bCeros = false;
    var bLots = false;
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

    // $("#lotsbody tr").find('td:eq(' + columnLot + ')').each(function () {
    //
    //  //get the value from cell
    //   valor = $(this)[0].children[0].value;
    //   if (valor == 0 || valor == '') {
    //     bLots = true;
    //   }
    // })

    btnClose = document.getElementById('closeModal');

    // var dQuantity = document.getElementById('qtyComplete').value;
    // Valid if the amount to complete is correct and enable and disable the button
    if (!bNumber) {
      swal("Error", "Debe ingresar sólo números en los campos cantidad y precio.", "error");
      return false;
    }
    if (bCeros) {
      // btnClose.disabled = true;
      swal("Aviso", "No puede haber renglones con cantidad menor o igual a cero.", "warning");
      return false;
    }
    if (!bBulk) {
      // btnClose.disabled = true;
      swal("Aviso", "No se admiten decimales para este material/producto.", "warning");
      return false;
    }
    // if (bLots) {
    //   btnClose.disabled = true;
    //   alert("No puede haber renglones sin lote");
    //   return false;
    // }
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
