var lLotsToCreate = new Array();

class SRowsCore {

  cleanAddPanel() {
    elementToAdd = null;
    iElementType = globalData.lElementsType.ITEMS;

    guiFunctions.setSearchCode('');
    guiValidations.setItemLabel('--');
    guiValidations.setUnitLabel('-');

    guiFunctions.setQuantity(1);
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
    guiValidations.setLotUnitLabel('-');

    $('#item').focus();
  }

  addRow(elementToAdd) {
      if (globalData.iMvtType == globalData.scwms.MVT_TP_OUT_TRA
          || utilFunctions.isProductionTransfer(globalData.iMvtType)) {
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
            elementToAdd.iPalletId == 1 ? 'SIN TARIMA' : elementToAdd.sPallet,
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
    var oRow = oMovement.getRow(tRow[0]);
    // oRow.iAuxIndex = index;
    let bCont = true;
    
    if (! oRow.bAuxToStock) {
      oMovement.removeRow(tRow[0]);
    }
    else if (headerCore.validateAndUdpateStock(oRow, globalData.lOperation.OUTPUT, false)) {
      oMovement.removeRow(tRow[0]);
    }
    else {
      bCont = false;
    }

    if (globalData.iMvtType == globalData.scwms.MVT_TP_OUT_TRA
        || utilFunctions.isProductionTransfer(globalData.iMvtType) && bCont) {
      oTransfersMovsTable.row('.selected').remove().draw( false );
    }
    else if(bCont) {
      oMovsTable.row('.selected').remove().draw( false );
    }

    if ((globalData.iMvtType == globalData.MVT_TP_IN_PUR
        || globalData.iMvtType == globalData.MVT_TP_IN_SAL
          || globalData.iMvtType == globalData.MVT_TP_OUT_SAL) && bCont) {
        supplyCore.updateRow(oRow, supplyCore.CLEAN);
    }

    if (bCont) {
      guiFunctions.updateAmtQtyLabels();
      if (globalData.isPalletReconfiguration) {
          guiReconfig.updatePallet(oMovement);
      }
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
      url: './' + (globalData.sRoute) + '/validaterow?iMvtType=' + globalData.iMvtType
                                      + '&iMovement=' + oMovement.iIdMovement
                                      + '&iPartner=' + globalData.oDocument.partner_id
                                      + '&iAddress=' + globalData.oDocument.address_id,
      data: data,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
         var serverData = JSON.parse(data);

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

              if (globalData.isPalletReconfiguration
                  || (!globalData.bIsInputMov && !utilFunctions.isProductionDelivery(globalData.iMvtType))) {
                 if (serverData.lNewLots.length > 0) {
                   swal("Error", "No pueden crearse lotes en esta operación", "error");
                   return false;
                 }

                 lLotsToCreate = new Array();
              }
              else {
                lLotsToCreate = serverData.lNewLots;
                for (const oNLot of lLotsToCreate) {
                  if (!!oNLot.warning && oNLot.warning.length > 0) {
                    let message = "";
                    let counter = 0;
                    for (const warn of oNLot.warning) {
                      switch (warn) {
                        case "NOT_FOUND":
                          message =
                            "El lote capturado no existe en el sistema SIIE, ¿desea continuar?";
                          break;
                        case "NO_ITEM":
                          message =
                            "El ítem de este lote no se encuntra en el sistema, ¿desea continuar?";
                          break;
                        case "NO_UNIT":
                          message =
                            "La unidad de este lote no se encuntra en el sistema, ¿desea continuar?";
                          break;
                        case "DIFF_ITEM":
                          message =
                            "El ítem no corresponde con el item del lote en SIIE,\n" +
                            oNLot.message[counter] +
                            " " +
                            "¿desea continuar?";
                          break;
                        case "DIFF_UNIT":
                          message =
                            "La unidad no corresponde con la unidad del lote en SIIE,\n" +
                            oNLot.message[counter] +
                            " " +
                            "¿desea continuar?";
                          break;

                        default:
                          break;
                      }

                      if (!confirm(message)) {
                        guiValidations.hideAdd();
                        guiFunctions.changeClassToSecondary("btn_lots");
                        return false;
                      }

                      counter++;
                    }
                  }
                }
              }

              rowsCore.completeRow(serverData.lLotRows);

              globalData.oLastLot = serverData.oLastLot;
              if (!globalData.bIsInputMov && !oRotation.validateRotation(elementToAdd)) {
                return false;
              }
            }
            else {
              elementToAdd.dPrice = guiFunctions.getPrice();
              elementToAdd.dQuantity = guiFunctions.getQuantity();
            }

            elementToAdd.iLocationId = oLocation.id_whs_location;
            elementToAdd.sLocation = oLocation.code.toUpperCase();

            var lElems = new Array();
            if (elementToAdd.lPallets != null && elementToAdd.lPallets.length > 0) {
              for (var i = 0; i < elementToAdd.lPallets.length; i++) {
                var elementCopy = rowsCore.clone(elementToAdd);

                elementCopy.iPalletId = elementToAdd.lPallets[i];
                elementCopy.sPallet = elementToAdd.lPallets[i];

                lElems.push(elementCopy);
              }
            }

            lElems.push(elementToAdd);

            for (var index = 0; index < lElems.length; index++) {
              if (headerCore.validateAndUdpateStock(lElems[index], globalData.lOperation.INPUT, false)) {
                  if (globalData.iMvtType == globalData.MVT_TP_IN_PUR
                        || globalData.iMvtType == globalData.MVT_TP_IN_SAL
                          || globalData.iMvtType == globalData.MVT_TP_OUT_SAL) {
                    supplyCore.updateRow(lElems[index], supplyCore.ADD);
                  }
                  rowsCore.addRow(lElems[index]);
                  guiValidations.showDelete();

                  if (globalData.isPalletReconfiguration) {
                      guiReconfig.updatePallet(oMovement);
                  }
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
     oJsRow.bWithoutRotation = oRow.item.without_rotation;
     oJsRow.iPalletId = oRow.pallet_id;
     oJsRow.iLocationId = oRow.location_id;
     oJsRow.dQuantity = oRow.quantity;
     oJsRow.dPrice = oRow.amount_unit;

     oJsRow.iDocOrderRowId = oRow.doc_order_row_id;
     oJsRow.iDocInvoiceRowId = oRow.doc_invoice_row_id;
     oJsRow.iDocDebitNoteRowId = oRow.doc_debit_note_row_id;
     oJsRow.iDocCreditNoteRowId = oRow.doc_credit_note_row_id;
     oJsRow.bIsDeleted = oRow.is_deleted;

     oJsRow.sItem = oRow.item.name;
     oJsRow.sItemCode = oRow.item.code;
     oJsRow.sUnit = oRow.unit.code;

     oJsRow.sLocation = oRow.location.code;
     oJsRow.sPallet = oRow.pallet_id == '1' ? 'SIN TARIMA' : oRow.pallet_id;

     if (oJsRow.bIsLot) {
       oRow.lot_rows.forEach(function(lotRow) {
          var jsLotRow = lotsCore.lotRowtoJsLotRow(lotRow);
          oJsRow.addLotRow(jsLotRow);
       });
     }

     return oJsRow;
  }

  clone(obj) {
      if (null == obj || "object" != typeof obj) return obj;
      var copy = new SMovementRow;
      for (var attr in obj) {
          if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
      }
      return copy;
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

  if (globalData.iMvtType == globalData.scwms.MVT_TP_OUT_TRA
      || utilFunctions.isProductionTransfer(globalData.iMvtType)) {
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
