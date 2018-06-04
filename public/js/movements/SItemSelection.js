var elementToAdd = null;
var iElementType = null;

class SItemSelection {

    setElementType(iType) {
      iElementType = iType;
    }

    getElementType() {
      return iElementType;
    }

    searchElement() {
        var sCode = document.getElementById('item').value;
        itemSelection.search(sCode);
    }

    search(sCode) {
      $.get('./' + (globalData.sRoute) +
                    '/search?code=' + sCode,
       function(data) {
          var serverData = JSON.parse(data);
          itemSelection.processSearch(serverData);
       });
    }

    processSearch(serverData) {
        guiFunctions.setSearchCode('');
        var bLoc = false;

        if (globalData.isPalletReconfiguration) {
           if (! reconfigCore.isValidSearch(serverData.iElementType, serverData.oElement)) {
              return false;
           }
        }

        switch (serverData.iElementType) {
          case globalData.lElementsType.ITEMS:
                if (! itemSelection.validateElement(serverData.oElement.id_item,
                                                      serverData.oElement.unit_id)) {
                      elementToAdd = null;
                      swal("Error", "No se puede agregar este material/producto al movimiento.", "error");
                      return false;
                }

                elementToAdd = itemSelection.itemToElement(serverData.oElement);

                if (! serverData.oElement.is_lot) {
                  guiValidations.showPallet();
                  guiValidations.hideLots();
                  guiValidations.showAdd();
                }
                else {
                  guiValidations.showLots();
                  guiValidations.showPallet();
                }

                break;

          case globalData.lElementsType.LOTS:
              if (! itemSelection.validateElement(serverData.oElement.item_id,
                                                    serverData.oElement.unit_id)) {
                    elementToAdd = null;
                    swal("Error", "No se puede agregar este material/producto al movimiento.", "error");
                    return false;
              }

              elementToAdd = itemSelection.lotToElement(serverData.oElement);
              guiValidations.showLots();
              guiValidations.showPallet();

              break;

          case globalData.lElementsType.PALLETS:
              if (parseFloat(serverData.dStock, 10) > 0
                    && globalData.bIsInputMov
                        && !globalData.isPalletReconfiguration) {
                    elementToAdd = null;
                    swal("Error", "No se puede agregar unidades a una tarima.", "error");
                    return false;
              }
              if (! itemSelection.validateElement(serverData.oElement.item_id,
                                                    serverData.oElement.unit_id)) {
                    elementToAdd = null;
                    swal("Error", "No se puede agregar este material/producto al movimiento.", "error");
                    return false;
              }
              if (! palletCore.validatePallet(serverData.lPalletStock)) {
                  return false;
              }

              elementToAdd = itemSelection.palletToElement(serverData.oElement);

              palletCore.setPallet(elementToAdd, serverData.lPalletStock);
              acceptPallet();
              guiValidations.showPallet();

              if (! serverData.oElement.item.is_lot) {
                guiValidations.hideLots();
                guiValidations.showAdd();
              }
              else {
                guiValidations.showLots();
              }
              break;

          case globalData.lElementsType.LOCATIONS:
              var lLocs = null;
              if (globalData.bIsInputMov) {
                  lLocs = globalData.lFDesLocations;
              }
              else {
                  lLocs = globalData.lFDesLocations;
              }

              var bFound = false;
              lLocs.forEach(function(loc) {
                 if (loc.id_whs_location == serverData.oElement.id_whs_location) {
                    bFound = true;
                 }
              });

              if (! bFound) {
                swal("Error", "La ubicación no pertenece al almacén.", "error");
                return false;
              }

              guiValidations.setLocationLabel(serverData.oElement.code + '-'
                                                  + serverData.oElement.name);
              locationsJs.setLocation(serverData.oElement);
              guiValidations.setSearchLocationText('');
              return true;

          case globalData.lElementsType.NOT_FOUND:
              swal("Error", "No se encontraron resultados.", "error");
              return false;
              break;

          default:

        }

        if (elementToAdd == null && !bLoc) {
            swal("Error", "No se encontraron resultados.", "error");
            return false;
        }

        guiValidations.setItemLabel(elementToAdd.sItemCode + '-' + elementToAdd.sItem);
        guiValidations.setUnitLabel(elementToAdd.sUnit);
    }

    validateElement(iidItem, iIdUnit) {
      var bIsFound = false;

       try {
            globalData.lFItems.forEach( function(oItem) {

                if (iidItem == oItem.id_item &&
                      iIdUnit == oItem.id_unit) {

                        bIsFound = true;
                        throw BreakException;
                }
            });
        }
        catch (e) { }

        return bIsFound;
    }

    itemToElement(oItem) {
        var obElement = new SMovementRow(oMovement.rowIdentifier);

        obElement.iElementType = globalData.lElementsType.ITEMS;

        obElement.iItemId = oItem.id_item;
        obElement.iUnitId = oItem.unit_id;
        obElement.iPalletId = 1;
        obElement.iAuxLotId = 1;

        obElement.sItem = oItem.name;
        obElement.sItemCode = oItem.code;
        obElement.sUnit = oItem.unit.code;
        obElement.bIsLot = oItem.is_lot;
        obElement.bIsBulk = oItem.is_bulk;
        obElement.bWithoutRotation = oItem.without_rotation;

        obElement.oElement = oItem;

        return obElement;
    }

    lotToElement(oLot) {
        var obElement = new SMovementRow(oMovement.rowIdentifier);

        obElement.iElementType = globalData.lElementsType.LOTS;

        obElement.iItemId = oLot.item_id;
        obElement.iUnitId = oLot.unit_id;
        obElement.iPalletId = 1;
        obElement.iLotId = oLot.id_lot;
        obElement.bIsLot = true;
        obElement.bIsBulk = oLot.item.is_bulk;
        obElement.bWithoutRotation = oLot.item.without_rotation;

        obElement.sItem = oLot.item.name;
        obElement.sItemCode = oLot.item.code;
        obElement.sUnit = oLot.unit.code;
        obElement.tExpDate = oLot.dt_expiry
        obElement.sLot = oLot.lot;

        obElement.oElement = oLot;

        return obElement;
    }

    palletToElement(oPallet) {
        var obElement = new SMovementRow(oMovement.rowIdentifier);

        obElement.iElementType = globalData.lElementsType.LOTS;

        obElement.iItemId = oPallet.item_id;
        obElement.iUnitId = oPallet.unit_id;
        obElement.iPalletId = oPallet.id_pallet;
        obElement.bIsLot = oPallet.item.is_lot;
        obElement.bIsBulk = oPallet.item.is_bulk;
        obElement.bWithoutRotation = oPallet.item.without_rotation;
        obElement.sPallet = oPallet.id_pallet;

        obElement.sItem = oPallet.item.name;
        obElement.sItemCode = oPallet.item.code;
        obElement.sUnit = oPallet.unit.code;

        obElement.oElement = oPallet;

        return obElement;
    }
}

var itemSelection = new SItemSelection();

function addElement() {
    if (guiValidations.validateInputRow()) {
        if (elementToAdd != null) {
            if (guiValidations.validateQtyPrice()) {
              elementToAdd.dQuantity = guiFunctions.getQuantity();
              elementToAdd.dPrice = guiFunctions.getPrice();

              rowsCore.addRow(elementToAdd);
            }
        }
        else {
            itemSelection.searchElement();
        }
    }

    guiValidations.cleanToNewRow();
}

function searchElem(e) {
    if (e.keyCode == 13) {
      itemSelection.searchElement();
    }
}

$('#select_item_button').on('click', function(e) {
    var row = oItemsTable.row('.selected').data();

    if (row == undefined) {
      swal("Error", "Debe seleccionar un elemento.", "error");
      return false;
    }

    if (globalData.isPalletReconfiguration && itemSelection.getElementType() == globalData.lElementsType.PALLETS) {
       if (! reconfigCore.isValidSearch(itemSelection.getElementType(), row)) {
          return true;
       }
    }

    guiFunctions.setSearchCode(row['item_code']);
    guiValidations.setItemLabel(row['item_name'] + '-' + row['unit_code']);
    guiValidations.setUnitLabel(row['unit_code']);

    elementToAdd = new SMovementRow(oMovement.rowIdentifier);

    elementToAdd.iItemId = row['id_item'];
    elementToAdd.iUnitId = row['id_unit'];
    elementToAdd.iLocationId = oLocation.id_whs_location;

    elementToAdd.sItem = row['item_name'];
    elementToAdd.sItemCode = row['item_code'];
    elementToAdd.sUnit = row['unit_code'];
    elementToAdd.bIsLot = row['is_lot'];
    elementToAdd.bIsBulk = row['is_bulk'];
    elementToAdd.bWithoutRotation = row['without_rotation'];
    elementToAdd.sLocation = oLocation.code;

    elementToAdd.oElement = row;
    elementToAdd.iElementType = itemSelection.getElementType();
    if (! row['is_lot']) {
      guiValidations.hideLots();
      guiValidations.showPallet();
    }
    else {
      guiValidations.showLots();
      guiValidations.showPallet();
    }

});


$('#select_button').on('click', function(e) {
    var row = oElementsTable.row('.selected').data();

    if (row == undefined) {
      swal("Error", "Debe seleccionar un elemento.", "error");
      return false;
    }

    switch (itemSelection.getElementType()) {
      case globalData.lElementsType.ITEMS:
            break;

      case globalData.lElementsType.LOTS:
            lotToAdd = new SLotRow();
            lotToAdd.iLotId = row.id_lot;
            lotToAdd.sLot = row.lot;
            lotToAdd.tExpDate = row.dt_expiry;

            guiFunctions.setTextLot(row.lot);
            guiFunctions.setExpDateLot(row.dt_expiry);
            guiFunctions.setQuantityLot(guiFunctions.getQuantity());

            break;

      case globalData.lElementsType.PALLETS:
            guiFunctions.setPalletNameLabel(row.id_pallet);
            elementToAdd.iPalletId = row.id_pallet;
            elementToAdd.sPallet = row.id_pallet;

            break;

      default:
            break;
    }
});

function showItems() {
    updateTable(globalData.lElementsType.ITEMS);
    itemSelection.setElementType(globalData.lElementsType.ITEMS);

    $('#item_search').modal('show');
}
