/*
* Validate the movement rows in whsmovs.blade.php
*/
function validateMovement(oMovement) {

  if (oMovement.rows.length == 0) {
    alert("No ha agregado movimientos.");
    return false;
  }

  var valid = true;
  oMovement.rows.forEach(function(row) {
      if (row.oAuxItem.is_lot && row.lotRows.length == 0) {
          alert("El renglón " + row.oAuxItem.name + " no tiene lotes asignados.");
          valid = false;
          return false;
      }

      var qtySum = 0.0;
      row.lotRows.forEach(function(lotRow) {
          qtySum += lotRow.dQuantity;

          if (row.oAuxItem.is_lot && lotRow.iLotId <= 0) {
            alert("El código " + row.sAuxItemCode + " no tiene un lote asignado.");
            valid = false;
            return false;
          }
      });

      // if (valid && qtySum != row.dQuantity) {
      //   alert("La cantidad del código " + row.oAuxItem.name + " no coincide con lo asignado.");
      //   valid = false;
      //   return false;
      // }

      // if (valid) {
      //   if (!globalData.bIsInputMov && row.aStock[0] < row.dQuantity) {
      //       alert("No hay suficientes existencias en el almacén para realizar el movimiento");
      //       valid = false;
      //       return false;
      //   }
      // }
  });

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
      alert('Debe elegir un almacén origen');
      document.getElementById("item").value = '';
      return false;
    }
    if (iWhsDesId == 0 && (globalData.bIsInputMov || iMovType == globalData.MVT_TP_OUT_TRA)) {
      alert('Debe elegir un almacén destino');
      document.getElementById("item").value = '';
      return false;
    }

    if (iMovType == globalData.MVT_TP_OUT_TRA) {
      document.getElementById('whs_des').disabled = true;
      movement.iWhsDes = parseInt(document.getElementById("whs_des").value);
      document.getElementById('whs_src').disabled = true;
      movement.iWhsSrc = parseInt(document.getElementById("whs_src").value);
    }
    else {
      if (globalData.bIsInputMov) {
        document.getElementById('whs_des').disabled = true;
        movement.iWhsDes = parseInt(document.getElementById("whs_des").value);
      }
      else {
        document.getElementById('whs_src').disabled = true;
        movement.iWhsSrc = parseInt(document.getElementById("whs_src").value);
      }
    }

    if (sItemCode == '') {
      alert('Debe ingresar un código');
      return false;
    }
    else {
      // document.getElementById("item").value = '';
    }

    if (dInputQuantity <= 0) {
      alert('La cantidad no puede ser menor o igual a cero');
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

    // var dQuantity = document.getElementById('qtyComplete').value;
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
    var iMovType = document.getElementById('mvt_whs_type_id').value;
    var parentRow = movement.getRow(parentId);

    if (iMovType == globalData.MVT_TP_OUT_TRA && parentRow.iPalletId != 1) {
      btnClose.disabled = true;
      return false;
    }

    btnClose.disabled = false;
}
