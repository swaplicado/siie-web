class SGuiKardex {
  constructor() {

  }

  setHeader(oServerData, iType) {
    oGuiKardex.setItem(oServerData.oItem.name);
    oGuiKardex.setUnit(oServerData.oUnit.code);
    oGuiKardex.setInputs(oServerData.dInputs);
    oGuiKardex.setOutputs(oServerData.dOutputs);
    oGuiKardex.setStock(oServerData.dStock);

    switch ((iType + "")) {
      case globalData.scwms.ELEMENTS_TYPE.PALLETS:
        oGuiKardex.setElementLabel('Tarima');
        oGuiKardex.setElement(oServerData.oElement.id_pallet == 1 ?
                                              'SIN TARIMA' :
                                              oServerData.oElement.id_pallet);
        oGuiKardex.setExpiration('--/--/----');

        break;

      case globalData.scwms.ELEMENTS_TYPE.LOTS:
        oGuiKardex.setElementLabel('Lote');
        oGuiKardex.setElement(oServerData.oElement.lot);
        oGuiKardex.setExpiration(oServerData.oElement.dt_expiry);

        break;

      case globalData.scwms.ELEMENTS_TYPE.ITEMS:
        oGuiKardex.setElementLabel('');
        oGuiKardex.setElement('----');
        oGuiKardex.setExpiration('--/--/----');

        break;
      default:

    }
  }

  setItem(sText) {
    document.getElementById('item').value = sText;
  }

  setUnit(sText) {
    document.getElementById('unit').value = sText;
  }

  setElementLabel(sText) {
    document.getElementById('type_label').innerText = sText;
  }

  setElement(sText) {
    document.getElementById('element_type').value = sText;
  }

  setExpiration(sText) {
    document.getElementById('expiration').value = sText;
  }

  setCutoffDate(sText) {
    document.getElementById('cutoff_date').value = sText;
  }

  setYear(sText) {
    document.getElementById('year').value = sText;
  }

  setInputs(dNumber) {
    document.getElementById('inputs').value = parseFloat(dNumber, 10).toFixed(globalData.DEC_QTY);
  }

  setOutputs(dNumber) {
    document.getElementById('outputs').value = parseFloat(-dNumber, 10).toFixed(globalData.DEC_QTY);
  }

  setStock(dNumber) {
    document.getElementById('stock').value = parseFloat(dNumber, 10).toFixed(globalData.DEC_QTY);
  }

  addRow(oMovRow, iType) {
      var label1 = '';
      var label2 = '';

      switch (iType + '') {
        case globalData.scwms.ELEMENTS_TYPE.ITEMS:
          label1 = '----';
          label2 = '--/--/----';
          break;

        case globalData.scwms.ELEMENTS_TYPE.PALLETS:
          label1 = oMovRow.pallet;
          label2 = '--/--/----';
          break;

        case globalData.scwms.ELEMENTS_TYPE.LOTS:
          label1 = oMovRow.lot;
          label2 = oMovRow.dt_expiry;
          break;

        default:
          label1 = '----';
          label2 = '--/--/----';
      }

      oKardexTable.row.add( [
          oMovRow.index,
          oMovRow.dt_date,
          oMovRow.folio,
          oMovRow.mvt_name,
          label1,
          label2,
          oMovRow.branch_code,
          oMovRow.whs_code,
          oMovRow.loc_code,
          parseFloat(oMovRow.inputs, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oMovRow.outputs, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oMovRow.stock, 10).toFixed(globalData.DEC_QTY),
          oMovRow.unit_code
      ]).draw( false );
  }

  showModal() {
     $('#theKardex').modal('show');
  }
}

var oGuiKardex = new SGuiKardex();
