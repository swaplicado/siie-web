class SLinkLotsCore {

  constructor() {
    this.oCurrentMovement = null;
    this.oCurrentMovementRow = null;
    this.iMovement = null;
    this.iMovementRow = null;
    this.iTableRowIndex = -1;

    this.lRowsWithStock = null;
    this.oCurMovRowWithSupply = null;
  }

  setLotsToModal(idMov, idRow) {
      linkLotsCore.oCurrentMovement = globalData.lMovements.get(idMov);
      linkLotsCore.oCurrentMovementRow = linkLotsCore.oCurrentMovement.getRow(idRow);

      for (var index = 0; index < linkLotsCore.lRowsWithStock.length; index++) {
          var oRow = linkLotsCore.lRowsWithStock[index];

          if (idRow == oRow.iIdRow && idMov == oRow.iAuxMovIndex) {
              linkLotsCore.oCurMovRowWithSupply = oRow;
              break;
          }
      }

      oLotsTable.clear().draw();

      linkLotsCore.oCurrentMovementRow.lotRows.forEach(function(lotRow) {
          linkLotsCore.oCurMovRowWithSupply.lAuxlotRows.forEach(function(lotRowS) {
              if (lotRowS[1].id == lotRow.id) {
                  lotRow.dQuantitySupplied = lotRowS[1].dQuantitySupplied;
              }
          });

          linkLotsCore.addRowToTable(lotRow, lotRow.id);
      });

      if (parseFloat(linkLotsCore.oCurMovRowWithSupply.dQuantity, 10) <=
              parseFloat(linkLotsCore.oCurMovRowWithSupply.dQtyIndSupplied, 10)) {
        guiLink.disableAcceptButton();
      }
      else {
        guiLink.enableAcceptButton();
      }

      $('#modal_link_lots').modal('show');
  }

  addRowToTable(oLot, iIndex) {
    oLotsTable.row.add([
        iIndex,
        oLot.iLotId,
        oLot.sLot,
        oLot.tExpDate,
        parseFloat(oLot.dQuantity, 10).toFixed(globalData.DEC_QTY),
        parseFloat(oLot.dQuantitySupplied, 10).toFixed(globalData.DEC_QTY),
        parseFloat(oLot.dQuantity - oLot.dQuantitySupplied, 10).toFixed(globalData.DEC_QTY),
        linkLotsCore.getInputQuantity(iIndex, oLot.dAuxQuantity)
    ]).draw( false );
  }

  getInputQuantity(iIndex, dQty) {
      return '<input id="r' + iIndex + '" class="form-control input-sm" ' +
                'placeholder="Cantidad..." style="text-align: right;" ' +
                'type="number" step="0.01" min="0" ' +
                ' value="' + parseFloat(dQty, 10).toFixed(globalData.DEC_QTY) + '">';
  }

  validateInput() {
    for (var index = 0; index < linkLotsCore.oCurrentMovementRow.lotRows.size; index++) {
        var sAssigned = document.getElementById('r' + index).value;
        var dAssigned = parseFloat(sAssigned, 10);

        var oLotRow = linkLotsCore.oCurrentMovementRow.getLotRow(index);

        if (! linkLotsCore.oCurrentMovementRow.bIsBulk && guiLink.isNumberFloat(sAssigned)) {
          swal("Error", "Este material/producto no admite cantidad con decimales.", "error");
          return false;
        }
        if (oLotRow.dQuantity < dAssigned) {
          swal("Error", "No puede asignar más unidades de las que contiene el movimiento.", "error");
          return false;
        }
        if (dAssigned < 0 ) {
          swal("Error", "La cantidad asignada no puede ser menor a cero.", "error");
          return false;
        }
        if ((oLotRow.dQuantity - oLotRow.dQuantitySupplied) <  dAssigned) {
          swal("Error", "No puede asignar más cantidad de la pendiente.", "error");
          return false;
        }
    }

    return true;
  }

  updateMovementLotRows() {
    var dAssignedRow = 0;
    for (var index = 0; index < linkLotsCore.oCurrentMovementRow.lotRows.size; index++) {
        var dAssigned = document.getElementById('r' + index).value;
        linkLotsCore.oCurrentMovementRow.getLotRow(index).dAuxQuantity = parseFloat(dAssigned, 10);
        dAssignedRow += parseFloat(dAssigned, 10);
    }

    globalData.lMovements.get(linkLotsCore.iMovement)
                          .getRow(linkLotsCore.iMovementRow).dAuxQuantity = dAssignedRow;
  }
}

var linkLotsCore = new SLinkLotsCore();

function setLots(idMov, idRow, iTableRowIndex) {
    linkLotsCore.iMovement = idMov;
    linkLotsCore.iMovementRow = idRow;

    linkLotsCore.iTableRowIndex = iTableRowIndex;
    linkLotsCore.setLotsToModal(idMov, idRow);
}

$('#accepLots').on('click', function(e) {
    if (linkLotsCore.validateInput()) {
        linkLotsCore.updateMovementLotRows();
        linksCore.updateMovementRow(linkLotsCore.iMovement,
                                    linkLotsCore.iMovementRow,
                                    linkLotsCore.iTableRowIndex);
    }
    else {
        return false;
    }
});
