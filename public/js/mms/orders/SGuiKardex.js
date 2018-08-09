class SGuiKardex {
  constructor() {

  }

  setProductionOrder(sText) {
    document.getElementById('po').value = sText;
  }

  setPOType(sText) {
    document.getElementById('po_type').value = sText;
  }

  setPODate(sText) {
    document.getElementById('po_date').value = sText;
  }

  setItem(sText) {
    document.getElementById('item').value = sText;
  }

  setInputs(dNumber) {
    document.getElementById('po_qty').value = parseFloat(dNumber, 10).toFixed(globalData.DEC_QTY);
  }

  setUnit(sText) {
    document.getElementById('unit').value = sText;
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
