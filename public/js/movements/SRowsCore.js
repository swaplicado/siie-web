var lLotsToCreate = new Array();

class SRowsCore {

  cleanAddPanel() {
    elementToAdd = null;
    iElementType = globalData.lElementsType.ITEMS;

    guiFunctions.setSearchCode('');
    guiValidations.setItemLabel('--');
    guiValidations.setUnitLabel('-');

    guiFunctions.setQuantity(0);
    guiValidations.enableQuantity();
    guiFunctions.setPrice(0);

    guiFunctions.changeClassToSecondary('btn_pallet');
    palletCore.cleanPallet();
    guiValidations.hidePallet();

    guiFunctions.changeClassToSecondary('btn_lots');
    lLotsToAdd = new Map();
    oLotsTable.clear().draw();

    document.getElementById('lot_edition').style.display = 'inline';
    document.getElementById('delete_lot').style.display = 'inline';
    document.getElementById('accLots').disabled = false;

    guiValidations.hideLots();

    $('#item').focus();
  }

  addRow(elementToAdd) {
      if (globalData.iMvtType == globalData.MVT_TP_OUT_TRA) {
        transfersCore.addRow(elementToAdd);
      }
      else {
        oMovement.addRow(elementToAdd);

        oMovsTable.row.add( [
            elementToAdd.iIdRow,
            elementToAdd.sItemCode,
            elementToAdd.sItem,
            elementToAdd.sUnit,
            elementToAdd.sLocation,
            elementToAdd.sPallet,
            parseFloat(elementToAdd.dPrice, 10).toFixed(globalData.DEC_AMT),
            parseFloat(elementToAdd.dQuantity, 10).toFixed(globalData.DEC_QTY),
            elementToAdd.bIsLot ? rowsCore.getLotsButton(elementToAdd.iIdRow) : '-',
            rowsCore.getStockButton(elementToAdd.iIdRow)
        ] ).draw( false );
      }

      guiFunctions.updateAmtQtyLabels();

      guiValidations.hideModify();
      rowsCore.cleanAddPanel();
  }

  deleteMovRow(tRow, index) {
    if (globalData.iMvtType == globalData.MVT_TP_OUT_TRA) {
      oTransfersMovsTable.row('.selected').remove().draw( false );
    }
    else {
      oMovsTable.row('.selected').remove().draw( false );
    }

    var oRow = oMovement.getRow(tRow[0]);
    oRow.iAuxIndex = index;

    if (! oRow.bAuxToStock) {
        oMovement.removeRow(tRow[0]);
    }
    else if (headerCore.validateAndUdpateStock(oRow, globalData.lOperation.OUTPUT)) {
        oMovement.removeRow(tRow[0]);
    }

    if (globalData.iMvtType == globalData.MVT_TP_IN_PUR || globalData.iMvtType == globalData.MVT_TP_OUT_SAL) {
        supplyCore.updateRow(oRow, supplyCore.CLEAN);
    }

    guiFunctions.updateAmtQtyLabels();
    if (globalData.isPalletReconfiguration) {
        guiReconfig.updatePallet(oMovement);
    }
    cleanPanel();
  }

  getLotsButton(id) {
    return  "<button type='button' onClick='viewLots(" + id + ")' " +
                    "class='butstk btn btn-primary btn-md' " +
                    "title='Ver lotes'>" +
                "<i class='glyphicon glyphicon-info-sign'></i>" +
            "</button>"
  }

  getStockButton(id) {
    return  "<button type='button' onClick='viewStock(" + id + ")'" +
                "class='butstk btn btn-success btn-md'" +
                "data-toggle='modal' data-target='#stock_modal' " +
                (globalData.bIsInputMov ? 'disabled ' : '') +
                "title='Ver existencias'>" +
                "<i class='glyphicon glyphicon-info-sign'></i>" +
                "</button>"
  }

  validateClientRow() {
      if (elementToAdd == null) {
        swal("Error", "Debe seleccionar un elemento para agregar.", "error");
        return false;
      }

      if (globalData.bIsInputMov && !globalData.isPalletReconfiguration) {
         if (guiFunctions.getPrice() <= 0) {
           swal("Error", "El precio debe ser mayor a cero.", "error");
           return false;
         }
      }

      if (elementToAdd.bIsLot && lLotsToAdd.size == 0) {
           swal("Error", "Debe asignar lotes.", "error");
           return false;
      }

      if (elementToAdd.bIsLot && ! lotsCore.validateLots()) {
          return false;
      }

      if (globalData.isPalletReconfiguration) {
          if (! globalData.isPalletDivision) {
            // if (reconfigCore.iAuxPalletLocationDes != 0
            //     && reconfigCore.iAuxPalletLocationDes != oLocation.id_whs_location) {
            //   swal("Error", "La tarima origen no está en la ubicación: " + oLocation.name + ".", "error");
            //   return false;
            // }
          }
      }

      return true;
  }

  validateServerRow() {
    elementToAdd.lAuxlotRows = Array.from(lLotsToAdd);
    elementToAdd.lAuxlotsToCreate = Array.from(lLotsToCreate);

    showLoading(5000);
    var data = { value : JSON.stringify(elementToAdd) };
    $.ajax({
      type: "POST",
      url: './' + (globalData.sRoute) + '/validaterow',
      data: data,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
         var serverData = JSON.parse(data);
         console.log(serverData);


         if (serverData.lErrors.length > 0) {
            guiValidations.hideAdd();

            var sErrors = '';
            serverData.lErrors.forEach(function(element) {
                sErrors += element + '\n';
            });

            if (elementToAdd.bIsLot) {
              guiFunctions.changeClassToSecondary('btn_lots');
            }

            swal("Error", sErrors, "error");
            return false;
         }
         else {
            if (elementToAdd.bIsLot) {
              lLotsToCreate = serverData.lNewLots;

              rowsCore.completeRow(serverData.lLotRows);
            }
            else {
              elementToAdd.dPrice = guiFunctions.getPrice();
              elementToAdd.dQuantity = guiFunctions.getQuantity();
            }

            elementToAdd.iLocationId = oLocation.id_whs_location;
            elementToAdd.sLocation = oLocation.code.toUpperCase();

            if (headerCore.validateAndUdpateStock(elementToAdd, globalData.lOperation.INPUT)) {
                if (globalData.iMvtType == globalData.MVT_TP_IN_PUR || globalData.iMvtType == globalData.MVT_TP_OUT_SAL) {
                  supplyCore.updateRow(elementToAdd, supplyCore.ADD);
                }
                rowsCore.addRow(elementToAdd);
                guiValidations.showDelete();

                if (globalData.isPalletReconfiguration) {
                    guiReconfig.updatePallet(oMovement);
                }
            }
         }
      }
    });
  }

  completeRow(lLotRows) {
      elementToAdd.dPrice = guiFunctions.getPrice();
      elementToAdd.lAuxlotRows = null;
      elementToAdd.lAuxlotsToCreate = null;

      var lArray = $.map(lLotRows, function(value, index) {
          value.dPrice = elementToAdd.dPrice;
          elementToAdd.addLotRow(value);
          return [value];
      });

      var dQuantity = parseFloat(0, 10);
      lArray.forEach(function(element) {
          dQuantity += parseFloat(element.dQuantity, 10);
      });

      elementToAdd.dQuantity = dQuantity;
  }

  rowtoJsRow(oRow) {
     var oJsRow = new SMovementRow();

     oJsRow.iIdMovRow = oRow.id_mvt_row;
     oJsRow.iItemId = oRow.item_id;
     oJsRow.iUnitId = oRow.unit_id;
     oJsRow.bIsLot = oRow.item.is_lot;
     oJsRow.bIsBulk = oRow.item.is_bulk;
     oJsRow.iPalletId = oRow.pallet_id;
     oJsRow.iLocationId = oRow.location_id;
     oJsRow.dQuantity = oRow.quantity;
     oJsRow.dPrice = oRow.amount_unit;

     oJsRow.iDocOrderRowId = oRow.doc_order_id;
     oJsRow.iDocInvoiceRowId = oRow.doc_invoice_id;
     oJsRow.iDocDebitNoteRowId = oRow.doc_debit_note_id;
     oJsRow.iDocCreditNoteRowId = oRow.doc_credit_note_id;
     oJsRow.bIsDeleted = oRow.is_deleted;

     oJsRow.sItem = oRow.item.name;
     oJsRow.sItemCode = oRow.item.code;
     oJsRow.sUnit = oRow.unit.code;

     oJsRow.sLocation = oRow.location.code;
     oJsRow.sPallet = oRow.pallet.pallet;

     if (oJsRow.bIsLot) {
       oRow.lot_rows.forEach(function(lotRow) {
          var jsLotRow = lotsCore.lotRowtoJsLotRow(lotRow);
          oJsRow.addLotRow(jsLotRow);
       });
     }

     return oJsRow;
  }

}

rowsCore = new SRowsCore();

function addElement() {
    if (rowsCore.validateClientRow()) {
        rowsCore.validateServerRow();
    }
    else {
      return false;
    }
}

function deleteElement() {
  var row = undefined;
  var index = undefined;

  if (globalData.iMvtType == globalData.MVT_TP_OUT_TRA) {
    row = oTransfersMovsTable.row('.selected').data();
    index = oTransfersMovsTable.row('.selected').index();
  }
  else {
    row = oMovsTable.row('.selected').data();
    index = oMovsTable.row('.selected').index();
  }

  if (row == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  rowsCore.deleteMovRow(row, index);
}

function cleanPanel() {
  rowsCore.cleanAddPanel();
}
