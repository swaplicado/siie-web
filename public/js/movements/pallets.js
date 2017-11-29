var oPalletRow = '';
var oReconfigurationMov = '';

/**
 * [Vue object to show pallet on view]
 * @type {Vue}
 */
vuePallet = new Vue({
  el: '#palletBody',
  data: {
    sPallet : {
      code: 'NA',
      item: 'NA',
      unit: 'NA',
      location: 'NA',
      pallet: 'NA',
      price: '0.0',
      quantity: '0.0'
    }
  }
})

/**
 * [viewStock set the values of stock to html table]
 */
function updatePallet(palletRow, movType) {
    oPalletRow = palletRow;

    Vue.set(vuePallet.sPallet, 'code', palletRow.oAuxItem.code);
    Vue.set(vuePallet.sPallet, 'item', palletRow.oAuxItem.name);
    Vue.set(vuePallet.sPallet, 'unit', palletRow.oAuxUnit.code);
    Vue.set(vuePallet.sPallet, 'location', palletRow.oAuxLocation.name);
    Vue.set(vuePallet.sPallet, 'pallet', palletRow.oAuxPallet.pallet);
    Vue.set(vuePallet.sPallet, 'price', palletRow.dPrice);
    Vue.set(vuePallet.sPallet, 'quantity', palletRow.dQuantity);

    console.log("lotes:");
    console.log(globalData.lLots);

    if (movType == globalData.PALLET_RECONFIG_IN) {
      var auxLots = [];
      globalData.lLots.forEach(function(lot) {
          palletRow.lotRows.forEach(function(lotR) {
              if (lot.id_lot == lotR.iLotId || lot.id_lot == 1) {
                  auxLots.push(lot);
              }
          });
      });

      globalData.lLots = auxLots;
    }
    document.getElementById('palletStk').disabled = false;
}

/*
* This method prepare the data to process in modal window
*/
$(document).on('click', 'button.palletLots', function () {
    attachRow(oPalletRow, true);
    document.getElementById('addBtn').disabled = true;
    document.getElementById('closeModal').disabled = true;
});

/**
 * [palletValidation description]
 * @param  {[SMovementRow]} row [description]
 * @return {[boolean]}     [description]
 */
function palletValidation(row, whsId, lotId, movType) {
   if (oPalletRow.iPalletId == row.iPalletId) {
     swal("Error", "No puede agregar la misma tarima.", "error");
     return false;
   }
   else if(oPalletRow.oAuxItem.is_lot && lotId != 0 && movType == globalData.PALLET_RECONFIG_IN) {
      var bExists = false;
      oPalletRow.lotRows.forEach(function(palletLot) {
            if (lotId == palletLot.iLotId) {
                bExists = true;
            }
       });

       if (! bExists) {
          swal("Aviso", "La tarima no contiene materiales/productos de este lote.", "warning");
          return false;
       }
   }

   if (row.iLocationId != 1 && row.oAuxLocation.warehouse.id_whs != whsId) {
     swal("Aviso", "La tarima destino debe estar en el mismo almacén.", "warning");
     return false;
   }
   if (row.oAuxItem.id_item != oPalletRow.oAuxItem.id_item) {
     swal("Aviso", "Las tarimas no pueden mezclar diferentes materiales/productos.", "warning");
     return false;
   }

   return true;
}

function discountMovements(oMovement) {
    if (oMovement.auxPalletRow.oAuxItem.is_lot) {
        oMovement.auxPalletRow.lotRows.forEach(function(palletLot) {
          palletLot.dQuantity = 0;
          oMovement.rows.forEach(function(row) {
            row.lotRows.forEach(function(lotRow) {
                if (lotRow.iLotId == palletLot.iLotId) {
                    palletLot.dQuantity += lotRow.dQuantity;
                }
            });
          });
        });

        oMovement.auxPalletRow.dQuantity = 0;
        oMovement.auxPalletRow.lotRows.forEach(function(palletLot) {
            oMovement.auxPalletRow.dQuantity += palletLot.dQuantity;
        });
    }
    else {
        oMovement.auxPalletRow.dQuantity = 0;
        oMovement.rows.forEach(function(row) {
            oMovement.auxPalletRow.dQuantity += row.dQuantity;
        });
    }
}
