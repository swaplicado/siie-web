/**
 * process of supply
 */
class SSupplyCore {
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

    return oJsRow;
  }

    setData(obRow, index) {
        var oRow = oMovement.getRow(obRow[0]);
        oRow.iAuxIndex = index;

        elementToAdd = oRow;

        guiFunctions.setPrice(elementToAdd.dPrice);

        if (! oRow.bIsLot) {
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

    updateRow(oElement) {
      var temp = oDocsTable.row(oElement.iAuxIndex).data();

      temp[6] = (parseFloat(emp[6], 10) - oElement.dQuantity).toFixed(globalData.DEC_QTY);
      temp[7] = oElement.dQuantity.toFixed(globalData.DEC_QTY);
      temp[9] = oElement.sLocation;
      temp[10] = oElement.sPallet;
      if (oElement.bIsLot) {
          temp[11] = "<button type='button' onClick='viewLots({{ $i }})'" +
                            "class='butstk btn btn-primary btn-md'" +
                            " title='Ver lotes'>" +
                        "<i class='glyphicon glyphicon-info-sign'></i>" +
                      "</button>";
      }
      
      $('#doc_table').dataTable().fnUpdate(temp, oElement.iAuxIndex, undefined, false);

      rowsCore.cleanAddPanel();
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
