class SGuiReconfig {

    showPalletDiv() {
      document.getElementById('div_pallet').style.display = 'block';
    }

    hidePalletDiv() {
      document.getElementById('div_pallet').style.display = 'none';
    }

    setPalletLabel(sText) {
      document.getElementById('label_pallet').innerText = sText;
    }

    setPalletLocationLabel(sText) {
      document.getElementById('label_pallet_location').innerText = sText;
    }

    setPalletItemCodeLabel(sText) {
      document.getElementById('label_pallet_item_code').innerText = sText;
    }

    setPalletItemLabel(sText) {
      document.getElementById('label_pallet_item').innerText = sText;
    }

    setPalletItemUnitLabel(sText) {
      document.getElementById('label_pallet_unit').innerText = sText;
    }

    cleanPallet() {
      guiReconfig.setPalletLabel('');
      guiReconfig.setPalletLocationLabel('');
      guiReconfig.setPalletItemCodeLabel('');
      guiReconfig.setPalletItemLabel('');
      guiReconfig.setPalletItemUnitLabel('');

      oPalletTable.clear().draw();

      guiReconfig.hidePalletDiv();
    }

    updatePallet(oMovement) {
       if (reconfigCore.oPalletRow.bIsLot) {
         if (globalData.isPalletDivision) {
           var dQuantity;
           for (var [key, rowLot] of reconfigCore.oPalletRow.lotRows) {
             dQuantity = 0;
             for (var [key, movRow] of oMovement.rows) {
               for (var [key, movLotRow] of movRow.lotRows) {
                  if (movLotRow.iLotId == rowLot.iLotId) {
                      dQuantity += parseFloat(movLotRow.dQuantity, 10);
                  }
               }
             }

             guiReconfig.updateTableRow(rowLot.iTableIndex, dQuantity, rowLot.dQuantity - dQuantity);
           }
         }
         else {
           var mLots = new Map();
           for (var [key, movRow] of oMovement.rows) {
             for (var [keyL, movLotRow] of movRow.lotRows) {
                if (mLots.has(movLotRow.iLotId)) {
                   mLots.set(movLotRow.iLotId, mLots.get(movLotRow.iLotId) + parseFloat(movLotRow.dQuantity, 10));
                }
                else {
                   mLots.set(movLotRow.iLotId, parseFloat(movLotRow.dQuantity, 10));
                }
              }
            }

           var bFound = false;
           mLots.forEach(function(dLotQuantity, idLot, mLots) {
             bFound = false;
             for (var [keyR, rowLot] of reconfigCore.oPalletRow.lotRows) {
                if (rowLot.iLotId == idLot) {
                    bFound = true;
                    guiReconfig.updateTableRow(rowLot.iTableIndex,
                                                dLotQuantity,
                                                rowLot.dQuantity + dLotQuantity);
                    break;
                }
             }

             if (! bFound) {
               var iIndex = !oPalletTable.rows().count() ? 0 : oPalletTable.rows().count() + 1;
               var oLotObj = null;
               globalData.lFLots.forEach(function (row) {
                  if (row.id_lot == idLot) {
                      oLotObj = row;
                  }
               });
               guiReconfig.addRowToPalletTable(iIndex,
                                               oLotObj.lot,
                                               oLotObj.dt_expiry,
                                               0,
                                               dLotQuantity,
                                               dLotQuantity);
             }
           });
         }
       }
       else {
         var dQuantity = 0;
         for (var [key, row] of oMovement.rows) {
             dQuantity += parseFloat(row.dQuantity, 10);
         }

         if (globalData.isPalletDivision) {
            guiReconfig.updateTableRow(0, dQuantity, reconfigCore.oPalletRow.dQuantity - dQuantity);
         }
         else {
            guiReconfig.updateTableRow(0, dQuantity, reconfigCore.oPalletRow.dQuantity + dQuantity);
         }
       }
    }

    updateTableRow(iIndex, dQuantity, dQuantityRem) {
      var temp = oPalletTable.row(iIndex).data();

      temp[4] = parseFloat(dQuantity, 10).toFixed(globalData.DEC_QTY);
      temp[5] = parseFloat(dQuantityRem, 10).toFixed(globalData.DEC_QTY);

      $('#pallet_table').dataTable().fnUpdate(temp, iIndex, undefined, false);
    }

    addRowToPalletTable(index, sLot, sExpDate, dQuantity1, dQuantity2, dQuantity3) {
      oPalletTable.row.add( [
        index,
        sLot,
        sExpDate,
        parseFloat(dQuantity1, 10).toFixed(globalData.DEC_QTY),
        parseFloat(dQuantity2, 10).toFixed(globalData.DEC_QTY),
        parseFloat(dQuantity3, 10).toFixed(globalData.DEC_QTY)
        // parseFloat(elementToAdd.dQuantity, 10).toFixed(globalData.DEC_QTY),
      ] ).draw( false );
    }
}

var guiReconfig = new SGuiReconfig();
