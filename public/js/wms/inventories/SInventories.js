class SInventories {
  constructor() {

  }

  addRow(oStockRow) {
      oStockTable.row.add( [
          oStockRow.item_code,
          oStockRow.item,
          oStockRow.location,
          oStockRow.pallet,
          oStockRow.lot,
          oStockRow.dt_expiry,
          parseFloat(oStockRow.stock - oStockRow.segregated, 10).toFixed(globalData.DEC_AMT),
          parseFloat(oStockRow.segregated, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oStockRow.stock, 10).toFixed(globalData.DEC_QTY),
          oStockRow.unit
      ]).draw( false );
  }

  readWarehouseStock(iWarehouse) {
    oMovement.iWhsSrc = iWarehouse;
    showLoading(4000);

    $.get('./emptywarehouse/stock?iWhs=' + iWarehouse,
     function(data) {
        var serverData = JSON.parse(data);

        oStockTable.clear().draw();
        for (var i = 0; i < serverData.length; i++) {
           oInventories.addRow(serverData[i]);
        }

        oInventories.fillMovement(serverData);

     });
  }

  fillMovement(serverData) {
    oMovement.rows.clear();
    
    oMovement.iMvtType = globalData.scwms.MVT_TP_OUT_ADJ;
    oMovement.iMvtSubType = globalData.scwms.MVT_ADJ_TP_OTH;
    oMovement.tDate = '2018-05-01';

    var iItem = 1;
    var iUnit = 1;
    for (var i = 0; i < serverData.length; i++) {
      var oStockRow = serverData[i];

      var oRow = oInventories.createRow(oStockRow);
      if (oStockRow.is_lot) {
         var oLotRow = oInventories.createLotRow(oStockRow);

         oRow.addLotRow(oLotRow);
      }

      oMovement.addRow(oRow);
    }
  }

  createRow(oStockRow) {
      var oRow = new SMovementRow();
      oRow.iItemId = oStockRow.item_id;
      oRow.iUnitId = oStockRow.unit_id;
      oRow.bIsLot = oStockRow.is_lot;
      oRow.bIsBulk = oStockRow.is_bulk;
      oRow.iPalletId = oStockRow.pallet_id;
      oRow.iLocationId = oStockRow.location_id;
      oRow.dQuantity = oStockRow.stock;
      oRow.dPrice = 0;

      oRow.iDocOrderRowId = 1;
      oRow.iDocInvoiceRowId = 1;
      oRow.iDocDebitNoteRowId = 1;
      oRow.iDocCreditNoteRowId = 1;
      oRow.iAuxDocRowId = 0;
      oRow.bIsDeleted = false;

      oRow.sItem = oStockRow.item;
      oRow.sItemCode = oStockRow.item_code;
      oRow.sUnit = oStockRow.unit;

      return oRow;
  }

  createLotRow(oStockRow) {
    var oLotRow = new SLotRow();

    oLotRow.iLotId = oStockRow.lot_id;
    oLotRow.sLot = oStockRow.lot;
    oLotRow.tExpDate = oStockRow.dt_expiry;
    oLotRow.dQuantity = oStockRow.stock;
    oLotRow.dPrice = 0;
    oLotRow.bIsDeleted = false;

    return oLotRow;
  }

  isValid() {
    if (oMovement.rows.size == 0 ) {
      swal("Error", "Debe leer un almacén con existencias.", "error");
      return false;
    }

    return true;
  }
}

var oInventories = new SInventories();

function readWhsStk() {
    var iWarehouse = document.getElementById('warehouse').value;
    oInventories.readWarehouseStock(iWarehouse);
}


function showLoading(dTime) {
  swal({
      title: 'Espere',
      text: 'Cargando...',
      timer: dTime,
      onOpen: () => {
        swal.showLoading()
      }
    }).then((result) => {
      if (result.dismiss === 'timer') {
      }
    });
}

function unfreeze() {
  var fre = document.getElementById("idFreeze"); // freeze button
  var sBut = document.getElementById("saveButton"); // save button
  var sButRead = document.getElementById("read_btn"); // read button
  var oDate = document.getElementById("dt_date"); // read button

  if (fre.firstChild.data == "Congelar") {
    if (oInventories.isValid()) {
        $('#warehouse').attr("disabled", true).trigger("chosen:updated");
        sBut.disabled = false;
        sButRead.disabled = true;
        oDate.readOnly = true;
        setMovementToForm();

        fre.innerHTML = "Descongelar";
    }
  }
  else {
    $('#warehouse').attr("disabled", false).trigger("chosen:updated");
    sBut.disabled = true;
    sButRead.disabled = false;
    oDate.readOnly = false;
    fre.innerHTML = "Congelar";
  }
}
