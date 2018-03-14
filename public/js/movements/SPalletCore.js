class SPalletCore {

  setPallet(elementToAdd) {
      if (elementToAdd != null) {
        guiFunctions.setPalletItemLabel(elementToAdd.sItemCode + '-' + elementToAdd.sItem);
        guiFunctions.setPalletUnitLabel(elementToAdd.sUnit);
        guiFunctions.setPalletNameLabel(elementToAdd.sPallet);
      }
  }

  searchPallets() {
     var sCode = guiFunctions.getSearchPallet();
     palletCore.search(sCode);
  }

  search(sCode) {
    $.get('./' + (globalData.oDocument != 0 ? 'supply' : 'create' ) +
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

    if (elementToAdd != null) {
      elementToAdd.iPalletId = serverData.oElement.id_pallet;
      elementToAdd.sPallet = serverData.oElement.pallet;

      palletCore.setPallet(elementToAdd);
    }
  }

  isCodeToSearchValid() {
    return !(guiFunctions.getSearchPallet() == null || guiFunctions.getSearchPallet() == '');
  }

  cleanPallet() {
    guiFunctions.setPalletNameLabel('--');
    elementToAdd.iPalletId = 1;
    elementToAdd.sPallet = 'N/A';
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
