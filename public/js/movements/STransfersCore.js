class STransfersCore {
  constructor() {

  }

  addRow(elementToAdd) {
      elementToAdd.iLocationDesId = oLocationDes.id_whs_location;
      elementToAdd.sLocationDes = oLocationDes.name;

      oMovement.addRow(elementToAdd);

      oTransfersMovsTable.row.add([
          elementToAdd.iIdRow,
          elementToAdd.sItemCode,
          elementToAdd.sItem,
          elementToAdd.sUnit,
          elementToAdd.sLocation,
          elementToAdd.sLocationDes,
          elementToAdd.sPallet,
          parseFloat(elementToAdd.dPrice, 10).toFixed(globalData.DEC_AMT),
          parseFloat(elementToAdd.dQuantity, 10).toFixed(globalData.DEC_QTY),
          elementToAdd.bIsLot ? rowsCore.getLotsButton(elementToAdd.iIdRow) : '-',
          rowsCore.getStockButton(elementToAdd.iIdRow)
      ]).draw( false );
  }
}

var transfersCore = new STransfersCore();
