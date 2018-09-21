class SGuiCharges {
  constructor() {

  }

  setBranch(sText) {
    document.getElementById('branch').innerText = sText;
  }

  setPlan(sText) {
    document.getElementById('plan').innerText = sText;
  }

  setFloor(sText) {
    document.getElementById('floor').innerText = sText;
  }

  setPODate(sText) {
    document.getElementById('dt_order').innerText = sText;
  }

  setPOType(sText) {
    document.getElementById('po_type').innerText = sText;
  }

  setItem(sText) {
    document.getElementById('product').innerText = sText;
  }

  setPOQuantity(dNumber) {
    document.getElementById('qty').innerText = parseFloat(dNumber, 10).toFixed(globalData.DEC_QTY);
  }

  setUnit(sText) {
    document.getElementById('unit').innerText = sText;
  }

  setFolio(sText) {
    document.getElementById('folio').innerText = guiKardex.stringfill(sText, globalData.LEN_FOL, '0');
  }

  setIdentifier(sText) {
    document.getElementById('identifier').innerText = sText;
  }

  setFatherFolio(sText) {
    document.getElementById('father').innerText = guiKardex.stringfill(sText, globalData.LEN_FOL, '0');
  }

  addRowToChargesTable(oRow) {
    var dCharged = 0;
    var dConsumed = 0;
    var dReturned = 0;
    var sLots = '';

    if (oRow.oConsumtion.length > 0) {
        for (var i = 0; i < oRow.oConsumtion.length; i++) {
          dCharged += parseFloat(oRow.oConsumtion[i].delivered, 10);
          dConsumed += parseFloat(oRow.oConsumtion[i].consumed, 10);
          dReturned += parseFloat(oRow.oConsumtion[i].returned, 10);

          if (parseFloat(oRow.oConsumtion[i].delivered, 10) > 0
                || parseFloat(oRow.oConsumtion[i].consumed, 10) > 0) {
            sLots += oRow.oConsumtion[i].lot + '; ';
          }
        }
    }

    oChargesTable.row.add([
        oRow.item_code,
        oRow.item,
        parseFloat(oRow.dRequired, 10).toFixed(globalData.DEC_QTY),
        parseFloat(dCharged - dReturned, 10).toFixed(globalData.DEC_QTY),
        parseFloat(dConsumed, 10).toFixed(globalData.DEC_QTY),
        oRow.unit_code,
        sLots
    ]).draw( false );
  }

}

var oGuiCharges = new SGuiCharges();
