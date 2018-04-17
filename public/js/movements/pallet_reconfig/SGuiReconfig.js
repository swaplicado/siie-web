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

    addRowToPalletTable(index, sLot, sExpDate, dQuantity) {
      oPalletTable.row.add( [
          index,
          sLot,
          sExpDate,
          parseFloat(dQuantity, 10).toFixed(globalData.DEC_QTY),
          parseFloat(0, 10).toFixed(globalData.DEC_QTY),
          parseFloat(dQuantity, 10).toFixed(globalData.DEC_QTY)
          // parseFloat(elementToAdd.dQuantity, 10).toFixed(globalData.DEC_QTY),
      ] ).draw( false );
    }

    updatePallet(oMovement) {
       if (reconfigCore.oPalletRow.bIsLot) {
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
         var dQuantity = 0;
         for (var [key, row] of oMovement.rows) {
             dQuantity += parseFloat(row.dQuantity, 10);
         }

         guiReconfig.updateTableRow(0, dQuantity, reconfigCore.oPalletRow.dQuantity - dQuantity);
       }
    }

    updateTableRow(iIndex, dQuantity, dQuantityRem) {
      var temp = oPalletTable.row(iIndex).data();

      temp[4] = parseFloat(dQuantity, 10).toFixed(globalData.DEC_QTY);
      temp[5] = parseFloat(dQuantityRem, 10).toFixed(globalData.DEC_QTY);

      $('#pallet_table').dataTable().fnUpdate(temp, iIndex, undefined, false);
    }
}

var guiReconfig = new SGuiReconfig();
