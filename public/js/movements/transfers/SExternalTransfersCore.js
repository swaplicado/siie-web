class SExternalTransfers {
  constructor() {
    this.oElement = null;
    this.oMovRow = null;
    this.iAuxIndex = -1;
  }

  get element() {
      return this.oElement;
  }

  getDataFromHeader() {
    globalData.tReceptionDate = document.getElementById('dt_date').value;
    globalData.iWhsDes = document.getElementById('whs_id').value;
  }

  goToServer() {
    showLoading(2000);
    $.get('./' + (globalData.sRoute) +
                  '/data?whs_source=' + globalData.iWhsSrc +
                  '&whs_des=' + globalData.iWhsDes +
                  '&mvt_cls=' + globalData.oMovement.mvt_whs_class_id +
                  '&mvt_type=' + globalData.oMovement.mvt_whs_type_id +
                  '&mvt_id=' + globalData.oMovementSrc.id_mvt,
     function(data) {
        var serverData = JSON.parse(data);

        guiTransfers.showSearchPanel();
        guiTransfers.showButtonReceive();

        externalTransfers.initializate(serverData);
     });
  }

  initializate(serverData) {
    globalData.lFSrcLocations = serverData.lSrcLocations;
    globalData.lFDesLocations = serverData.lDesLocations;
    globalData.lFItems = serverData.lItems;

    locationsJs.updateLocationsTable();
    locationsJs.setDefaultLocation(globalData.lFDesLocations);

    oMovement.iMvtType = globalData.oMovement.mvt_whs_type_id;
    oMovement.iMvtType = globalData.oMovement.mvt_whs_type_id;
    oMovement.iMvtSubType = globalData.oMovement.mvt_trn_type_id;
    oMovement.iBranchDes = globalData.oMovement.branch_id;

    oMovement.iWhsSrc = globalData.iWhsSrc;
    oMovement.iWhsDes = globalData.iWhsDes;
    oMovement.tDate = globalData.tReceptionDate;

    guiTransfers.setQuantity(0);
    guiTransfers.setPrice(0);
  }

  setElement(idMvt, idMvtRow) {
     for (var indexRow = 0; indexRow < globalData.oMovementSrc.rows.length; indexRow++) {
       var oRow = globalData.oMovementSrc.rows[indexRow];

       if (idMvt == oRow.mvt_id && idMvtRow == oRow.id_mvt_row) {
          var oElem = new SMovementRow();

          oElem.iIdMovRow = oRow.id_mvt_row;
          oElem.iItemId = oRow.item_id;
          oElem.iItemId = oRow.item_id;
          oElem.iUnitId = oRow.unit_id;
          oElem.sItemCode = oRow.item.code;
          oElem.sItem = oRow.item.name;
          oElem.bIsLot = oRow.item.is_lot;
          oElem.bIsBulk = oRow.item.is_bulk;
          oElem.bWithoutRotation = oRow.item.without_rotation;
          oElem.sUnit = oRow.unit.code;
          oElem.iPalletId = oRow.pallet_id;
          oElem.sPallet = oRow.pallet_id == '1' ? 'SIN TARIMA' : oRow.pallet_id;
          oElem.iLocationId = oRow.location_id;
          oElem.sLocation = oRow.location.name;
          oElem.dAuxQuantity = oRow.quantity;
          oElem.dQtyIndSupplied = oRow.quantity_received == undefined ? 0 : oRow.quantity_received;

          if (oRow.item.is_lot) {
            for (var indexLot = 0; indexLot < oRow.lot_rows.length; indexLot++) {
                var oLotRowS = oRow.lot_rows[indexLot];
                var oLotRow = new SLotRow();

                oLotRow.iLotId = oLotRowS.lot_id;
                oLotRow.sLot = oLotRowS.lot.lot;
                oLotRow.tExpDate = oLotRowS.lot.dt_expiry;
                oLotRow.dAuxQuantity = oLotRowS.quantity;
                oLotRow.dQuantitySupplied = oLotRowS.quantity_received == undefined ? 0 : oLotRowS.quantity_received;

                oElem.addLotRow(oLotRow);
            }

            guiTransfers.setQtyReadOnly(true);
            transferLots.setAddLots(oElem);
          }
          else {
            guiTransfers.setQtyReadOnly(false);
          }

          externalTransfers.oElement = oElem;
          externalTransfers.oMovRow = oRow;

          break;
       }
     }

     if (externalTransfers.oElement != null) {
        if (externalTransfers.validateItem(externalTransfers.oElement)) {
          guiTransfers.setElementToPanel(externalTransfers.oElement);
        }
     }
  }

  validateItem(oElement) {
      for (var index = 0; index < globalData.lFItems.length; index++) {
         var oItem = globalData.lFItems[index];
         if (oItem.id_item == oElement.iItemId && oItem.id_unit == oElement.iUnitId) {
            return true;
         }
      }

      swal("Error", "No puede ingresar este material/producto en el almacén destino.", "error");
      return false;
  }

  validateElement() {
    if (externalTransfers.oElement.bIsLot) {
      var dRowQuantity = 0;

      if (externalTransfers.oElement.lotRows.size == 0) {
        swal("Error", "Debe confirmar las unidades recibidas por lote.", "error");
        return false;
      }

      for (var [key, lotRow] of externalTransfers.oElement.lotRows) {
          if (lotRow.dQuantity > (lotRow.dAuxQuantity - lotRow.dQuantitySupplied)) {
            swal("Error", "No puede recibir más cantidad de la pendiente.", "error");
            return false;
          }
          if (lotRow.dQuantity > lotRow.dAuxQuantity) {
            swal("Error", "No puede recibir más cantidad de la enviada.", "error");
            return false;
          }

          dRowQuantity += lotRow.dQuantity;
      }

      if (dRowQuantity <= 0) {
        swal("Error", "La cantidad total a recibir no puede ser cero.", "error");
        return false;
      }

      if (dRowQuantity > externalTransfers.oElement.dQuantity) {
        swal("Error", "No puede recibir más cantidad en los lotes que la recibida en el renglón.", "error");
        return false;
      }
    }
    else {
      if (externalTransfers.oElement.dQuantity > externalTransfers.oMovRow.quantity) {
        swal("Error", "No puede recibir más cantidad de la enviada.", "error");
        return false;
      }

    }

    return true;
  }

  addElement() {
    externalTransfers.oElement.iLocationDesId = oLocation.id_whs_location;
    externalTransfers.oElement.sLocationDes = oLocation.name;

    externalTransfers.oElement.iLocationId = externalTransfers.oMovRow.location_id;
    externalTransfers.oElement.sLocation = externalTransfers.oMovRow.location.name;

    externalTransfers.oElement.dPrice = guiTransfers.getPrice();

    if (externalTransfers.oElement.bIsLot) {
        for (var [key, lotRow] of externalTransfers.oElement.lotRows) {
            lotRow.dPrice = guiTransfers.getPrice();
        }
    }

    oMovement.addRow(externalTransfers.oElement);
    guiTransfers.addElementToTable(externalTransfers.oElement);

    guiTransfers.showDelete();
    guiTransfers.cleanEntryPanel();
  }

  deleteElement(row) {
    var iIndex = row[0];

    oMovement.removeRow(iIndex);
    oTransfersMovsTable.row('.selected').remove().draw( false );
  }
}

var externalTransfers = new SExternalTransfers();

function headerContinue() {
    externalTransfers.getDataFromHeader();
    guiTransfers.disableHeader();
    guiTransfers.hideContinue();
    externalTransfers.goToServer();
}

function onPressReceive() {
  var row = oRowsTable.row('.selected').data();

  if (row == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  externalTransfers.iAuxIndex = oRowsTable.row('.selected').index();
  externalTransfers.setElement(row[0], row[1]);
}

function onAddButton() {
  if (externalTransfers.validateElement()) {
      externalTransfers.addElement();
  }
}

function onDeleteButton() {
  var row = oTransfersMovsTable.row('.selected').data();

  if (row == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  externalTransfers.deleteElement(row);
}
