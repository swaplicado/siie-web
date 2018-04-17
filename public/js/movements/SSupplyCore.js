/**
 * process of supply
 */
class SSupplyCore {
    get ADD() {
        return 1;
    }

    get CLEAN() {
        return 2;
    }

    docRowtoJsRow(oRow) {
      var oJsRow = new SMovementRow();

      oJsRow.iItemId = oRow.item_id;
      oJsRow.iUnitId = oRow.unit_id;
      oJsRow.bIsLot = oRow.is_lot;
      oJsRow.bIsBulk = oRow.is_bulk;
      oJsRow.dPrice = parseFloat(oRow.price_unit_cur, 10);

      oJsRow.sItem = oRow.concept;
      oJsRow.sItemCode = oRow.concept_key;
      oJsRow.sUnit = oRow.unit;

      oJsRow.bIsDeleted = oRow.is_deleted;
      oJsRow.iAuxDocRowId = oRow.id_document_row;

      return oJsRow;
    }

    setData(obRow, index) {
        var oRow = supplyCore.docRowtoJsRow(lDocRows[index]);

        oRow.iAuxIndex = index;

        elementToAdd = oRow;

        guiFunctions.setPrice(elementToAdd.dPrice);

        if (! oRow.bIsLot) {
          guiValidations.showPallet();
          guiValidations.hideLots();
          guiValidations.showAdd();
        }
        else {
          guiValidations.showLots();
          guiValidations.showPallet();
        }

        guiValidations.setItemLabel(elementToAdd.sItemCode + '-' + elementToAdd.sItem);
        guiValidations.setUnitLabel(elementToAdd.sUnit);
    }

    updateRow(oElement, iAction) {
      var temp = oDocsTable.row(oElement.iAuxIndex).data();

      if (iAction == supplyCore.ADD) {
          temp[9] = (parseFloat(temp[9].replace(',', ''), 10) + oElement.dQuantity).toFixed(globalData.DEC_QTY);
          temp[10] = (parseFloat(temp[10].replace(',', ''), 10) - oElement.dQuantity).toFixed(globalData.DEC_QTY);
      }
      else {
          temp[9] = (parseFloat(temp[9].replace(',', ''), 10) - oElement.dQuantity).toFixed(globalData.DEC_QTY);
          temp[10] = (parseFloat(temp[10].replace(',', ''), 10) + oElement.dQuantity).toFixed(globalData.DEC_QTY);
      }

      $('#doc_table').dataTable().fnUpdate(temp, oElement.iAuxIndex, undefined, false);

      progressBar.updateProgressbar();
    }

    cleanRow(obRow, index) {
        var oRow = oMovement.getRow(obRow[0]);
        oRow.iAuxIndex = index;
        oRow.iPalletId = 1;
        oRow.iLocationId = 0;
        oRow.dAuxQuantity = 0;
        oRow.bIsDeleted = false;

        oRow.sLocation = '--';
        oRow.sPallet = '--';
        oRow.lotRows = new Map();
        oRow.lAuxlotRows = null;

        supplyCore.updateRow(oRow, supplyCore.CLEAN);

        oRow.dQuantity = 0;
    }

}

var supplyCore = new SSupplyCore();

function setRowData() {
  var row = oDocsTable.row('.selected').data();
  var index = oDocsTable.row('.selected').index();

  if (row == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  supplyCore.setData(row, index);
}

function cleanRowData() {
  var row = oDocsTable.row('.selected').data();
  var index = oDocsTable.row('.selected').index();

  if (row == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  supplyCore.cleanRow(row, index);
}
