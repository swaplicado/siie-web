class SGuiKardex {
  constructor() {

  }

  setProductionOrder(sText) {
    document.getElementById('po').innerText = sText;
  }

  setPOType(sText) {
    document.getElementById('po_type').innerText = sText;
  }

  setPODate(sText) {
    document.getElementById('po_date').innerText = sText;
  }

  setItem(sText) {
    document.getElementById('item').innerText = sText;
  }

  setPOQuantity(dNumber) {
    document.getElementById('po_qty').innerText = parseFloat(dNumber, 10).toFixed(globalData.DEC_QTY);
  }

  setUnit(sText) {
    document.getElementById('unit').innerText = sText;
  }

  setKardex(oServerData) {
    guiKardex.setProductionOrder(guiKardex.stringfill(oServerData.oProductionOrder.folio, 5, '0')
                                  + '-' + oServerData.oProductionOrder.identifier);
    guiKardex.setPOType(oServerData.oProductionOrder.type.name);
    guiKardex.setPODate(oServerData.oProductionOrder.date);
    guiKardex.setItem(oServerData.oProductionOrder.item.code
                                  + '-' + oServerData.oProductionOrder.item.name);
    guiKardex.setUnit(oServerData.oProductionOrder.unit.code);
    guiKardex.setPOQuantity(oServerData.oProductionOrder.charges
                              * oServerData.oProductionOrder.formula.quantity);

    oKardexTable.clear().draw();

    if (oServerData.lKardexRows.length > 0) {
      for (var i = 0; i < oServerData.lKardexRows.length; i++) {
        guiKardex.addKardexRow(oServerData.lKardexRows[i], i + 1);
      }
    }
  }

  addKardexRow(oKardexRow, index) {
    oKardexTable.row.add([
        index,
        oKardexRow.dt_date,
        oKardexRow.folio,
        oKardexRow.mvt_code,
        oKardexRow.mvt_name,
        oKardexRow.item,
        oKardexRow.pallet == 1 ? 'SIN TARIMA' : oKardexRow.pallet,
        oKardexRow.lot,
        oKardexRow.dt_expiry,
        oKardexRow.branch_code,
        oKardexRow.whs_code,
        oKardexRow.loc_code,
        parseFloat(oKardexRow.inputs, 10).toFixed(globalData.DEC_QTY),
        parseFloat(oKardexRow.outputs, 10).toFixed(globalData.DEC_QTY),
        oKardexRow.unit_code,
        parseFloat(0, 10).toFixed(globalData.DEC_AMT),
        parseFloat(0, 10).toFixed(globalData.DEC_AMT),
        parseFloat(0, 10).toFixed(globalData.DEC_AMT)
    ]).draw( false );
  }

  stringfill(sValue, width, cFill) {
      // var numberOutput = Math.abs(number); /* Valor absoluto del número */
      var numberOutput = sValue; /* Valor absoluto del número */
      var length = numberOutput.toString().length; /* Largo del número */
      // var zero = "0"; /* String de cero */
      var zero = cFill; /* String de cero */

      if (width <= length) {
          return sValue;
      } else {
          return ((zero.repeat(width - length)) + numberOutput.toString());
      }
  }
}

var guiKardex = new SGuiKardex();

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
