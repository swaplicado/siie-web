class SPalletCore {

  setPallet(elementToAdd, lPalletStock) {
      if (elementToAdd != null) {
        guiFunctions.setPalletItemLabel(elementToAdd.sItemCode + '-' + elementToAdd.sItem);
        guiFunctions.setPalletUnitLabel(elementToAdd.sUnit);
        guiFunctions.setPalletNameLabel(elementToAdd.sPallet);
      }

      if (! globalData.bIsInputMov) {
        if (elementToAdd.bIsLot) {
          lLotsToAdd = new Map();
          var index = 0;
          var dQuantity = 0;
          var oLotRow;
          lPalletStock.forEach(function (oStock) {
              oLotRow = new SLotRow();

              oLotRow.iLotId = oStock.id_lot;
              oLotRow.sLot = oStock.lot;
              oLotRow.tExpDate = oStock.dt_expiry;
              oLotRow.dQuantity = parseFloat(oStock.stock, 10);
              oLotRow.dPrice = 0;

              lLotsToAdd.set(index, oLotRow);

              elementToAdd.dAuxQuantity += oLotRow.dQuantity;

              lotsCore.addRowToTable(oLotRow, index);

              index++;
          });

          guiFunctions.setAccumQuantityLabel(elementToAdd.dAuxQuantity);
          guiFunctions.setQuantity(elementToAdd.dAuxQuantity);
          guiValidations.disableQuantity();

          document.getElementById('lot_edition').style.display = 'none';
          document.getElementById('delete_lot').style.display = 'none';
          // document.getElementById('lot_accep_div').style.display = 'none';
          document.getElementById('accLots').disabled = true;
        }
        else {
          guiFunctions.setQuantity(lPalletStock[0].length > 0 ? lPalletStock[0].stock : 0);
          guiValidations.disableQuantity();
        }
      }

      if (globalData.isPalletReconfiguration) {
        reconfigCore.iAuxPalletLocationDes = lPalletStock[0].length > 0 ? lPalletStock[0].id_whs_location : 0;
      }
  }

  searchPallets() {
     var sCode = guiFunctions.getSearchPallet();
     palletCore.search(sCode);
  }

  search(sCode) {
    $.get('./' + (globalData.sRoute) +
                  '/search?code=' + sCode,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);
        palletCore.processPallet(serverData);
     });
  }

  processPallet(serverData) {
    if (serverData.oElement == null) {
       swal("Error", "No se encontraron resultados.", "error");
       guiFunctions.setSearchPallet('');
       return false;
    }

    if (serverData.iElementType != globalData.lElementsType.PALLETS) {
       swal("Error", "Sólo pueden escanearse tarimas.", "error");
       guiFunctions.setSearchPallet('');
       return false;
    }

    if (serverData.oElement.item_id != elementToAdd.iItemId ||
         serverData.oElement.unit_id != elementToAdd.iUnitId) {
       swal("Error", "La tarima escaneada no corresponde con el " +
                       "material/producto elegido.", "error");
       guiFunctions.setSearchPallet('');
       return false;
    }

    if (parseFloat(serverData.dStock, 10) > 0 && globalData.bIsInputMov) {
          elementToAdd = null;
          swal("Error", "No se puede agregar unidades a una tarima.", "error");
          return false;
    }

    if (! palletCore.validatePallet(serverData.lPalletStock)) {
        return false;
    }

    if (elementToAdd != null) {
      elementToAdd.iPalletId = serverData.oElement.id_pallet;
      elementToAdd.sPallet = serverData.oElement.pallet;

      palletCore.setPallet(elementToAdd, serverData.lPalletStock);
    }
  }

  isCodeToSearchValid() {
    return !(guiFunctions.getSearchPallet() == null || guiFunctions.getSearchPallet() == '');
  }

  cleanPallet() {
    guiFunctions.setPalletNameLabel('--');
    guiFunctions.setSearchPallet('');

    if (elementToAdd != null) {
      elementToAdd.iPalletId = 1;
      elementToAdd.sPallet = 'N/A';
    }
  }

  validatePallet(lPalletStock) {
      var bHaveSegregated = false;

      if (lPalletStock.length > 0) {
         if (globalData.isPalletReconfiguration) {
              if ( reconfigCore.oPalletRow != null
                && lPalletStock[0].id_pallet == reconfigCore.oPalletRow.iPalletId) {
               swal("Error", "No puede agregar unidades a la misma tarima.", "error");
               return false;
             }

             if (lPalletStock[0].id_whs != oMovement.iWhsDes) {
               swal("Error", "La tarima no existe en el almacén destino.", "error");
               return false;
             }
         }
      }

      lPalletStock.forEach(function (oStock) {
          if (parseFloat(oStock.segregated, 10) > 0) {
              bHaveSegregated = true;
          }
      });

      if (bHaveSegregated) {
        swal("Error", "La tarima tiene unidades segregadas y no se "+
                      "pueden hacer operaciones con ella.", "error");
        return false;
      }

      return true;
  }

}

var palletCore = new SPalletCore();

function searchPallet(e) {
    if (e.keyCode == 13) {
      if (palletCore.isCodeToSearchValid()) {
        palletCore.searchPallets();
      }
      else {
        swal("Error", "Debe ingresar un código para la búsqueda.", "error");
        return false;
      }
    }
}

function searchPall() {
  if (palletCore.isCodeToSearchValid()) {
    palletCore.searchPallets();
  }
  else {
    swal("Error", "Debe ingresar un código para la búsqueda.", "error");
    return false;
  }
}

function showPalletModal() {
  guiFunctions.setPalletItemLabel(elementToAdd.sItem);
  guiFunctions.setPalletUnitLabel(elementToAdd.sUnit);

  $('#pallet_modal').modal('show');
  $('#search_pallet').focus();
}

function acceptPallet() {
  if (elementToAdd.bIsLot) {
      if (lLotsToAdd.size > 0) {
        guiValidations.showAdd();
      }
  }
  else {
    guiValidations.showAdd();
  }

  guiFunctions.changeClassToSuccess('btn_pallet');
}

$('#accPallet').on('click', function(e) {
    acceptPallet();
});

var bPallets = false;
var bItems = false;
function showPallets() {
    updateTable(globalData.lElementsType.PALLETS);
    itemSelection.setElementType(globalData.lElementsType.PALLETS);

    $('#pallet_modal').modal('hide');
    $('#item_search').modal('show');

    bPallets = true;
    bItems = true;
}

$('#item_search').on('hidden.bs.modal', function () {
  if (bPallets) {
    $('#pallet_modal').modal('show');
  }
  bItems = false;
})

$('#pallet_modal').on('hidden.bs.modal', function () {
   bPallets = bItems;
   if (elementToAdd.iPalletId != 1) {
      guiFunctions.changeClassToSuccess('btn_pallet');
   }
})

function cleanPallet() {
    palletCore.cleanPallet();
}
