class STRansLotsCore {
    constructor() {
      this.iTableRowIndex = -1;
    }

    setLotsToModal(index, idRow) {
       oLotsTable.clear().draw();

       for (var rowIndex = 0; rowIndex < globalData.oMovementSrc.rows.length; rowIndex++) {
          var row = globalData.oMovementSrc.rows[rowIndex];
          if (row.id_mvt_row == idRow) {
             if (row.item.is_lot) {
                for (var lotIndex = 0; lotIndex < row.lot_rows.length; lotIndex++) {
                    var lotRow = row.lot_rows[lotIndex];
                    if (! lotRow.is_deleted) {
                        transferLots.addRowToTable(lotIndex, lotRow);
                    }
                }
             }
          }
       }
    }

    addRowToTable(iIndex, olotRow) {
      oLotsTable.row.add([
          iIndex,
          olotRow.lot_id,
          olotRow.lot.lot,
          olotRow.lot.dt_expiry,
          parseFloat(olotRow.quantity, 10).toFixed(globalData.DEC_QTY),
          parseFloat(olotRow.quantity_received, 10).toFixed(globalData.DEC_QTY),
          parseFloat((olotRow.quantity - olotRow.quantity_received), 10).toFixed(globalData.DEC_QTY)
      ]).draw( false );
    }

    setLotsToInfoModal(index) {
       var oRow = oMovement.getRow(index);

       oLotsInfoTable.clear().draw();

       for (var [key, lotRow] of oRow.lotRows.entries()) {
          transferLots.addRowToInfoTable(key, lotRow);
       }
    }

    addRowToInfoTable(iIndex, oLotRow) {
        oLotsInfoTable.row.add([
            iIndex,
            oLotRow.iLotId,
            oLotRow.sLot,
            oLotRow.tExpDate,
            parseFloat(oLotRow.dQuantity, 10).toFixed(globalData.DEC_QTY)
        ]).draw( false );
    }

    setAddLots(oElement) {
      oLotsAddTable.clear().draw();

      for (var [key, oLotRow] of oElement.lotRows) {
        transferLots.addRowToTableAdd(key, oLotRow);
      }
    }

    addRowToTableAdd(iIndex, olotRow) {
      oLotsAddTable.row.add([
          iIndex,
          olotRow.iLotId,
          olotRow.sLot,
          olotRow.tExpDate,
          parseFloat(olotRow.dAuxQuantity, 10).toFixed(globalData.DEC_QTY),
          parseFloat(olotRow.dQuantitySupplied, 10).toFixed(globalData.DEC_QTY),
          parseFloat(olotRow.dAuxQuantity - olotRow.dQuantitySupplied, 10).toFixed(globalData.DEC_QTY),
          transferLots.getInputQuantity(iIndex, olotRow.dAuxQuantity - olotRow.dQuantitySupplied)
      ]).draw( false );
    }

    getInputQuantity(iIndex, dQty) {
        return '<input id="r' + iIndex + '" class="form-control input-sm" ' +
                  'placeholder="Cantidad..." style="text-align: right;" ' +
                  'type="number" step="0.01" min="0" ' +
                  ' value="' + parseFloat(dQty, 10).toFixed(globalData.DEC_QTY) + '">';
    }

    validateInput() {
      for (var index = 0; index < externalTransfers.oElement.lotRows.size; index++) {
          var sReceived = document.getElementById('r' + index).value;
          var dReceived = parseFloat(sReceived, 10);

          var oLotRow = externalTransfers.oElement.getLotRow(index);

          if (! externalTransfers.oElement.bIsBulk && guiTransfers.isNumberFloat(dReceived)) {
            swal("Error", "Este material/producto no admite cantidad con decimales.", "error");
            return false;
          }
          if (oLotRow.dAuxQuantity < dReceived) {
            swal("Error", "No puede asignar más unidades de las que contiene el movimiento.", "error");
            return false;
          }
          if (dReceived < 0 ) {
            swal("Error", "La cantidad recibida no puede ser menor a cero.", "error");
            return false;
          }
          if ((oLotRow.dAuxQuantity - oLotRow.dQuantitySupplied) <  dReceived) {
            swal("Error", "No puede asignar más cantidad de la pendiente.", "error");
            return false;
          }
      }

      return true;
    }

    updateMovementLotRows() {
      var dReceivedRow = 0;
      for (var index = 0; index < externalTransfers.oElement.lotRows.size; index++) {
          var sReceived = document.getElementById('r' + index).value;
          externalTransfers.oElement.getLotRow(index).dQuantity = parseFloat(sReceived, 10);
          dReceivedRow += parseFloat(sReceived, 10);
      }

      externalTransfers.oElement.dQuantity = dReceivedRow;
      guiTransfers.setQuantity(externalTransfers.oElement.dQuantity);
    }
}

var transferLots = new STRansLotsCore();

function setLots(index, idRow) {
    transferLots.iTableRowIndex = index;
    transferLots.setLotsToModal(index, idRow);

    $('#lots_transfers').modal('show');
}

function viewLots(index) {
    transferLots.setLotsToInfoModal(index);

    $('#lots_info').modal('show');
}

$('#accepLots').on('click', function(e) {
    if (transferLots.validateInput()) {
        transferLots.updateMovementLotRows();
        // linksCore.updateMovementRow(linkLotsCore.iMovement,
        //                             linkLotsCore.iMovementRow,
        //                             linkLotsCore.iTableRowIndex);
    }
    else {
        return false;
    }
});
