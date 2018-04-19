class SReconfigurationCore {
  constructor() {
     this.isPalletSet = false;
     this.oPalletRow = null;
     this.iAuxPalletLocationDes = -1;
  }

  cleanAllPallet() {
    reconfigCore.oPalletRow = null;
    guiReconfig.cleanPallet();
  }

  validateElement(oRow) {
    switch (itemSelection.getElementType()) {
      case globalData.lElementsType.ITEMS:
            break;
    }
  }

  isValidSearch(iElementType, oElement) {
    if (iElementType == globalData.lElementsType.LOCATIONS) {
        return true;
    }

    if (reconfigCore.oPalletRow == null) {
      if (iElementType != globalData.lElementsType.PALLETS) {
         swal("Error", "Debe seleccionar o escanear una tarima a dividir.", "error");
         return false;
      }
      else {
          var location = reconfigCore.validatePallet(oElement.id_pallet);
        if (location > 0) {
            oElement.location_id = location;
            reconfigCore.setPalletData(oElement);
            return false;
        }
        else {
          swal("Error", "La tarima no es válida o no " +
                        "se encuentra en el almacén seleccionado.", "error");
          return false;
        }
      }
    }
    else {
      return true;
    }
  }

  validatePallet(iPallet) {
    var iLocation = 0;
    var index;

    for (index = 0; index < globalData.lFStock.length; index++) {
        if (globalData.lFStock[index].pallet_id == iPallet) {
           iLocation = globalData.lFStock[index].location_id;
           break;
        }
    }

    return iLocation;
  }

  setPalletData(oElement) {
     guiReconfig.isPalletSet = true;
     reconfigCore.oPalletRow = reconfigCore.createPalletRow(oElement.id_pallet);
     oMovement.iAuxPallet = oElement.id_pallet;
     oMovement.iAuxPalletLocation = oElement.location_id;

     if (globalData.bIsInputMov) {
         locationsJs.setPalletLocation(globalData.lFDesLocations, oElement.location_id);
     }
     else {
         locationsJs.setPalletLocation(globalData.lFSrcLocations, oElement.location_id);
     }

     guiReconfig.setPalletLabel(reconfigCore.oPalletRow.sPallet);
     guiReconfig.setPalletItemCodeLabel(reconfigCore.oPalletRow.sItemCode);
     guiReconfig.setPalletItemLabel(reconfigCore.oPalletRow.sItem);
     guiReconfig.setPalletItemUnitLabel(reconfigCore.oPalletRow.sUnit);

     reconfigCore.addToTable();
     guiReconfig.showPalletDiv();

     reconfigCore.filterItemsLotsPallets(reconfigCore.oPalletRow);
     iElementType = globalData.lElementsType.ITEMS;
     searchCore.initializateItems(iElementType);
  }

  createPalletRow(iPallet) {
    var objPallet = new SMovementRow();
    var bSet = false;

    var index;
    for (index = 0; index < globalData.lFStock.length; index++) {
        var oStock = globalData.lFStock[index];
        if (oStock.pallet_id == iPallet) {
           if (bSet) {
              if (objPallet.bIsLot) {
                  var objPalletLot = new  SLotRow();

                  objPalletLot.iIdLotRow = 0;
                  objPalletLot.iLotId = oStock.lot_id;
                  objPalletLot.tExpDate = oStock.dt_expiry;
                  objPalletLot.sLot = oStock.lot;
                  objPalletLot.dQuantity = oStock.stock;

                  objPallet.addLotRow(objPalletLot);

                  objPallet.dQuantity += oStock.stock;
              }
           }
           else {
              objPallet.iIdMovRow = 0;
              objPallet.iItemId = oStock.item_id;
              objPallet.iUnitId = oStock.unit_id;
              objPallet.bIsLot = oStock.is_lot;
              objPallet.bIsBulk = oStock.is_bulk;
              objPallet.iPalletId = oStock.pallet_id;
              objPallet.iLocationId = oStock.location_id;
              objPallet.sLocation = oStock.location;
              objPallet.dQuantity = oStock.stock;
              objPallet.dPrice = 0;
              objPallet.sItemCode = oStock.item_code;
              objPallet.sItem = oStock.item;
              objPallet.sUnit = oStock.unit;
              objPallet.sPallet = oStock.pallet;

              if (objPallet.bIsLot) {
                var objPalletLot = new  SLotRow();

                objPalletLot.iIdLotRow = 0;
                objPalletLot.iLotId = oStock.lot_id;
                objPalletLot.tExpDate = oStock.dt_expiry;
                objPalletLot.sLot = oStock.lot;
                objPalletLot.dQuantity = oStock.stock;

                objPallet.addLotRow(objPalletLot);
              }
           }
        }
    }

    return objPallet;
  }

  filterItemsLotsPallets(oPalletRow) {
    var items = new Array();
    var lots = new Array();
    var pallets = new Array();

    var index;
    for (index = 0; index < globalData.lFItems.length; index++) {
        var oItem = globalData.lFItems[index];
        if (oItem.id_item == oPalletRow.iItemId && oItem.id_unit == oPalletRow.iUnitId) {
           items.push(oItem);
           break;
        }
    }

    /**
     * Pallet division or Add to pallet
     */
    if (globalData.isPalletDivision) {
        for (index = 0; index < globalData.lFLots.length; index++) {
            var oLot = globalData.lFLots[index];
            for (var [key, oLotRow] of oPalletRow.lotRows) {
                if (oLot.id_lot == oLotRow.iLotId) {
                    lots.push(oLot);
                }
            }
        }

        var indexPallet = -1;
        for (index = 0; index < globalData.lFPallets.length; index++) {
            var oPallet = globalData.lFPallets[index];
            if (oPallet.id_pallet == oPalletRow.iPalletId) {
                indexPallet = index;
                break;
            }
        }

        globalData.lFPallets.splice(index, 1);
    }
    else {
      for (index = 0; index < globalData.lFLots.length; index++) {
          var oLot = globalData.lFLots[index];
          if (oPalletRow.iItemId == oLot.id_item && oPalletRow.iUnitId == oLot.id_unit) {
              lots.push(oLot);
          }
      }

      for (index = 0; index < globalData.lFPallets.length; index++) {
          var oPallet = globalData.lFPallets[index];
          if (oPallet.id_pallet != oPalletRow.iPalletId) {
              pallets.push(oPallet);
          }
      }

      globalData.lFPallets = pallets;
    }

    globalData.lFLots = lots;
    globalData.lFItems = items;
  }

  addToTable() {
    var iTableIndex = 0;

    if (reconfigCore.oPalletRow.bIsLot) {
      for (var [key, oLotRow] of reconfigCore.oPalletRow.lotRows) {
          guiReconfig.addRowToPalletTable(iTableIndex,
                                          oLotRow.sLot,
                                          oLotRow.tExpDate,
                                          oLotRow.dQuantity,
                                          0,
                                          oLotRow.dQuantity);
          oLotRow.iTableIndex = iTableIndex;
          iTableIndex++;
      }
    }
    else {
        guiReconfig.addRowToPalletTable(0,
                                        'SIN LOTE',
                                        '----/--/--',
                                        reconfigCore.oPalletRow.dQuantity,
                                        0,
                                        reconfigCore.oPalletRow.dQuantity);
    }
  }
}

var reconfigCore = new SReconfigurationCore();
