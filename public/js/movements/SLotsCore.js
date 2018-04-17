var lotToAdd = null;
var lLotsToAdd = new Map();
var index = 0;

class SLotsCore {

  searchLots() {
     var sCode = guiFunctions.getSearchLot();
     lotsCore.search(sCode);
  }

  search(sCode) {
    $.get('./' + (globalData.sRoute) +
                  '/search?code=' + sCode,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);
        lotsCore.processLot(serverData);
     });
  }

  processLot(serverData) {
     if (serverData.oElement == null) {
        swal("Error", "No se encontraron resultados.", "error");
        guiFunctions.setSearchLot('');
        return false;
     }

     if (serverData.iElementType != globalData.lElementsType.LOTS) {
        swal("Error", "Sólo pueden escanearse lotes.", "error");
        guiFunctions.setSearchLot('');
        return false;
     }

     if (serverData.oElement.item_id != elementToAdd.iItemId ||
          serverData.oElement.unit_id != elementToAdd.iUnitId) {
        swal("Error", "El lote escaneado no corresponde con el " +
                        "material/producto elegido.", "error");
        return false;
     }

     lotToAdd = new SLotRow();
     lotToAdd.iLotId = serverData.oElement.id_lot;
     lotToAdd.sLot = serverData.oElement.lot;
     lotToAdd.tExpDate = serverData.oElement.dt_expiry;

     guiFunctions.setTextLot(serverData.oElement.lot);
     guiFunctions.setExpDateLot(serverData.oElement.dt_expiry);
     guiFunctions.setQuantityLot(1);

     guiFunctions.setSearchLot('');
  }

  setFoundLot() {
    if (elementToAdd.iElementType == globalData.lElementsType.LOTS) {
       lotToAdd = new SLotRow();
       lotToAdd.iLotId = elementToAdd.iLotId;
       lotToAdd.sLot = elementToAdd.sLot;
       lotToAdd.tExpDate = elementToAdd.tExpDate;

       guiFunctions.setTextLot(elementToAdd.sLot);
       guiFunctions.setExpDateLot(elementToAdd.tExpDate);
       guiFunctions.setQuantityLot(1);

       elementToAdd.iLotId = 0;
       elementToAdd.sLot = '';
       elementToAdd.tExpDate = '';
       elementToAdd.iElementType = globalData.lElementsType.ITEMS;
    }
  }

  addLot() {
    if (! lotsCore.isValidLot()) {
      guiFunctions.setSearchLot('');
      return false;
    }

    if (lotToAdd == null)  {
        lotToAdd = new SLotRow(elementToAdd.lotIdentifier);
        lotToAdd.iLotId = 0;
        lotToAdd.sLot = guiFunctions.getTextLot();
        lotToAdd.tExpDate = guiFunctions.getExpDateLot();
    }

    lotToAdd.sLot = guiFunctions.getTextLot();
    lotToAdd.tExpDate = guiFunctions.getExpDateLot();
    lotToAdd.dQuantity = guiFunctions.getQuantityLot();

    // lotToAdd.bCreate = guiFunctions.getCreateLot();
    lLotsToAdd.set(index, lotToAdd);

    elementToAdd.dAuxQuantity += lotToAdd.dQuantity;

    guiFunctions.setAccumQuantityLabel(elementToAdd.dAuxQuantity);
    lotsCore.addRowToTable(lotToAdd, index);

    index++;

    lotsCore.cleanLotPanel();
  }

  addRowToTable(oLot, iIndex) {
    oLotsTable.row.add( [
        iIndex,
        oLot.iLotId,
        oLot.sLot,
        oLot.tExpDate,
        parseFloat(oLot.dQuantity, 10).toFixed(globalData.DEC_QTY)
        // (oLot.bCreate ? 'SÍ' : 'NO')
    ]).draw( false );
  }

  deleteLotRow(oRow) {
    oLotsTable.row('.selected').remove().draw( false );
    lLotsToAdd.delete(oRow[0]);

    elementToAdd.dAuxQuantity -= oRow[4];
    guiFunctions.setAccumQuantityLabel(elementToAdd.dAuxQuantity);
  }

  isValidLot() {
    if (guiFunctions.getTextLot() == null || guiFunctions.getTextLot() == '') {
      swal("Error", "Debe ingresar un lote.", "error");
      return false;
    }

    if (guiFunctions.getExpDateLot() == null || guiFunctions.getExpDateLot() == '') {
      swal("Error", "Debe ingresar una fecha de Vencimiento del lote.", "error");
      return false;
    }

    if (! elementToAdd.bIsBulk && guiValidations.isNumberFloat(guiFunctions.getQuantityLot())) {
      swal("Error", "Este material/producto no admite cantidad con decimales.", "error");
      return false;
    }

    if (guiFunctions.getQuantityLot() > guiFunctions.getQuantity()) {
      swal("Error", "La cantidad del lote no puede ser mayor a la del renglón.", "error");
      return false;
    }

    if (guiFunctions.getQuantityLot() <= 0) {
      swal("Error", "La cantidad del lote debe ser mayor a cero.", "error");
      return false;
    }

    var auxQty = elementToAdd.dAuxQuantity + guiFunctions.getQuantityLot();
    if (auxQty > guiFunctions.getQuantity()) {
      swal("Error", "La cantidad a agregar superará a cantidad del renglón.", "error");
      return false;
    }

    return true;
  }

  validateInputSearchLot() {
    if (guiFunctions.getSearchLot() == null || guiFunctions.getSearchLot() == '') {
        swal("Error", "Debe ingresar un código de barras a buscar.", "error");
        return false;
    }

    return true;
  }

  cleanLotPanel() {
    guiFunctions.setSearchLot('');
    guiFunctions.setTextLot('');
    guiFunctions.setExpDateLot(null);
    guiFunctions.setQuantityLot(0);
    // guiFunctions.setCreateLot(false);

    lotToAdd = null;
  }

  validateLots() {
      var dSum = parseFloat(0, 10);

      for (var [key, value] of lLotsToAdd) {
          dSum += parseFloat(value.dQuantity, 10);
      }

      if (dSum != guiFunctions.getQuantity()) {
          swal("Error", "La suma de cantidades de los lotes debe ser igual " +
          "a la cantidad del renglón.", "error");
          return false;
      }

      return true;
  }

  lotRowtoJsLotRow(lotRow) {
    var lotRowJs = new SLotRow();

    lotRowJs.iIdLotRow = lotRow.id_mvt_row_lot;
    lotRowJs.iLotId = lotRow.lot_id;
    lotRowJs.sLot = lotRow.lot.lot;
    lotRowJs.tExpDate = lotRow.lot.dt_expiry;
    lotRowJs.dQuantity = lotRow.quantity;
    lotRowJs.dPrice = lotRow.amount_unit;
    lotRowJs.bIsDeleted = lotRow.is_deleted;

    return lotRowJs;
  }
}

var lotsCore = new SLotsCore();

function searchLot(e) {
    if (e.keyCode == 13) {
      if (lotsCore.validateInputSearchLot()) {
          lotsCore.searchLots();
      }
    }
}

function searchLotByButton() {
    if (lotsCore.validateInputSearchLot()) {
        lotsCore.searchLots();
    }
}

function showLotsModal() {
    var dQuantity = guiFunctions.getQuantity();

    if (elementToAdd == null) {
       swal("Error", "Debe seleccionar o buscar un material/producto.", "error");
       return false;
    }

    if (dQuantity <= 0) {
      swal("Error", "La cantidad debe ser mayor que cero.", "error");
      return false;
   }

    if (!elementToAdd.bIsBulk && guiValidations.isNumberFloat(dQuantity)) {
      swal("Error", "La cantidad debe ser entera.", "error");
      return false;
    }

    guiFunctions.setLotsQuantityLabel(dQuantity);
    guiFunctions.setAccumQuantityLabel(elementToAdd.dAuxQuantity);

    lotsCore.setFoundLot();

    // oLotsTable.column( 5 ).visible( true );

    $('#lotss_modal').modal('show');
    $('#search_lot').focus();
}

function addLotRow() {
    lotsCore.addLot();
}

function deleteLot() {
  var row = oLotsTable.row('.selected').data();

  if (row == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  lotsCore.deleteLotRow(row);
}

function cleanEntry() {
    lotsCore.cleanLotPanel();
}

function viewLots(iRow) {
  document.getElementById('lot_edition').style.display = 'none';
  document.getElementById('delete_lot').style.display = 'none';
  // document.getElementById('lot_accep_div').style.display = 'none';
  document.getElementById('accLots').disabled = true;

  // oLotsTable.column( 5 ).visible( false );

  oLotsTable.clear().draw();

  var oRow = oMovement.getRow(iRow);

  guiFunctions.setLotsQuantityLabel(oRow.dQuantity);
  guiFunctions.setAccumQuantityLabel(oRow.dQuantity);

  for (var [key, lotRow] of oRow.lotRows) {
    lotsCore.addRowToTable(lotRow, key);
  }

  $('#lotss_modal').modal('show');
}

$('#accLots').on('click', function(e) {
    if (lLotsToAdd.size == 0) {
        swal("Error", "Debe asignar lotes para continuar.", "error");
        return false;
    }

    if (lotsCore.validateLots()) {
      guiFunctions.changeClassToSuccess('btn_lots');
      guiValidations.showAdd();
    }
});

var bLots = false;
var bLItems = false;
function showLots() {
    updateTable(globalData.lElementsType.LOTS);
    itemSelection.setElementType(globalData.lElementsType.LOTS);

    $('#lotss_modal').modal('hide');
    $('#item_search').modal('show');

    bLots = true;
    bLItems = true;
}

$('#item_search').on('hidden.bs.modal', function () {
  if (bLots) {
    $('#lotss_modal').modal('show');
  }
  bLItems = false;
})

$('#lotss_modal').on('hidden.bs.modal', function () {
   bLots = bLItems;
})
