class SGuiProduction {
  constructor() {

  }

  setFormula(sText) {
    document.getElementById('formula').innerText = sText;
  }

  setPlan(sText) {
    document.getElementById('plan').innerText = sText;
  }

  setIdentifier(sText) {
    document.getElementById('identifier').innerText = sText;
  }

  setOrderType(sText) {
    document.getElementById('order_type').innerText = sText;
  }

  setOrderDate(sText) {
    document.getElementById('order_date').innerText = sText;
  }

  setOrderItem(sText) {
    document.getElementById('order_item').innerText = sText;
  }

  setCharges(dQty) {
    document.getElementById('charges').value = parseFloat(dQty, 10).
                                                  toFixed(globalData.DEC_QTY);
  }

  setDelivered(dQty) {
    document.getElementById('delivered').value = parseFloat(dQty, 10).
                                                  toFixed(globalData.DEC_QTY);
  }

  setPOUnit(sText) {
    document.getElementById('po_unit').innerText = sText;
  }

  setProductionOrder(oProductionOrder) {
     guiProduction.setFormula(oProductionOrder.formula.identifier);
     guiProduction.setPlan(guiProduction.stringfill(oProductionOrder.plan.folio, 5, '0'));
     guiProduction.setIdentifier(guiProduction.stringfill(oProductionOrder.folio, 5, '0') + '-' + oProductionOrder.identifier);
     guiProduction.setOrderType(oProductionOrder.type.name);
     guiProduction.setOrderDate(oProductionOrder.date);
     guiProduction.setOrderItem(oProductionOrder.item.code + '-' +
                                    oProductionOrder.item.name + '-' +
                                    oProductionOrder.unit.code);
     guiProduction.setCharges(oProductionOrder.charges);
     guiProduction.setDelivered(oProductionOrder.dDelivered);
     guiProduction.setPOUnit(oProductionOrder.unit.code);

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

var guiProduction = new SGuiProduction();
