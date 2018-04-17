class SValidation {
  constructor() {

  }

  /*
  * Validate the movement rows in whsmovs.blade.php
  */
  validateMovement(oMovement) {

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

  validatePalletDivision(oMovement, oPalletMovRow) {
      if (oPalletMovRow == null) {
        swal("Error", "No se ha elegido una tarima a dividir.", "error");
        return false;
      }

      var mMapQuantity = new Map();
      var mMapPalletLocs = new Map();
      var dQuantity = parseFloat(0, 10);

      if (oPalletMovRow.bIsLot) {
        for (var [key, row] of oMovement.rows) {
          for (var [keyL, rowL] of row.lotRows) {
              if (mMapQuantity.has(rowL.iLotId)) {
                  mMapQuantity.set(rowL.iLotId, (mMapQuantity.get(rowL.iLotId) + parseFloat(rowL.dQuantity, 10)));
              }
              else {
                  mMapQuantity.set(rowL.iLotId, parseFloat(rowL.dQuantity, 10));
              }
          }
        }

        for (var [key, palletLotRow] of oPalletMovRow.lotRows) {
            if (mMapQuantity.get(palletLotRow.iLotId) > parseFloat(palletLotRow.dQuantity, 10)) {
                swal("Error", "Las unidades del lote " + palletLotRow.sLot +
                      " que pretende mover son más que las existentes en la tarima.", "error");
                return false;
            }
        }
      }
      else {
        for (var [key, row] of oMovement.rows) {
            dQuantity += parseFloat(row.dQuantity, 10);
        }

        if (dQuantity > parseFloat(oPalletMovRow.dQuantity, 10)) {
          swal("Error", "Las unidades que pretende mover son más que las existentes en la tarima.", "error");
          return false;
        }
      }

      for (var [key, row] of oMovement.rows) {
         if (mMapPalletLocs.has(row.iPalletId)) {
            if (mMapPalletLocs.get(row.iPalletId) != row.iLocationId) {
              swal("Error", "No puede agregar unidades a la misma tarima en ubicaciones diferentes.", "error");
              return false;
            }
         }
         else {
            mMapPalletLocs.set(row.iPalletId, row.iLocationId);
         }
      }

      return true;
  }

}

var validation = new SValidation();

/*
* When freeze is pressed, the field of item, quantity and the button
* of add are disabled, but the data of the movement is send to server too
*/
function unfreeze() {
  var fre = document.getElementById("idFreeze"); // freeze button
  var sBut = document.getElementById("saveButton"); // save button

  if (fre.firstChild.data == "Congelar") {
    if (validation.validateMovement(oMovement)) {
        if (globalData.isPalletReconfiguration) {
            if (globalData.isPalletDivision) {
              if (! validation.validatePalletDivision(oMovement, reconfigCore.oPalletRow)) {
                return false;
              }
            }
        }

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
