/*
* Validate the movement rows in whsmovs.blade.php
*/
function validateMovement(oMovement) {

  var valid = true;
  oMovement.rows.forEach(function(row) {
      if (row.bAuxIsLot && row.lotRows.length == 0) {
          alert("El código " + row.sAuxItemCode + " no tiene lotes asignados.");
          valid = false;
          return false;
      }

      var qtySum = 0.0;
      row.lotRows.forEach(function(lotRow) {
          qtySum += lotRow.dQuantity;

          if (row.bAuxIsLot && lotRow.iLotId <= 0) {
            alert("El código " + row.sAuxItemCode + " no tiene un lote asignado.");
            valid = false;
            return false;
          }
      });

      if (valid && qtySum != row.dQuantity) {
        alert("La cantidad del código " + row.sAuxItemCode + " no coincide con lo asignado.");
        valid = false;
        return false;
      }
  });

  return valid;
}

/*
* Validate the input of user in whsmovs.blade.php
*/
function validateInput() {
    var sItemCode = document.getElementById("item").value; // gets the code of code bar
    var dInputQuantity = document.getElementById("quantity").value; // gets the quantity

    var iWhsId = 0;
    if (bInput) {
      iWhsId = document.getElementById("whs_des").value;
    }
    else {
      iWhsId = document.getElementById("whs_src").value;
    }

    if (iWhsId == 0) {
      alert('Debe elegir un almacén');
      document.getElementById("item").value = '';
      return false;
    }
    else {
      if (globalData.bIsInputMov) {
        document.getElementById('whs_des').setAttribute("readonly", "readonly");
      }
      else {
        document.getElementById('whs_src').setAttribute("readonly", "readonly");
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
