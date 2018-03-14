class SStockCore {

  addStockToTable(oStock) {
    oStockTable.row.add( [
        oStock.location,
        oStock.pallet,
        oStock.lot,
        parseFloat((oStock.available_stock
                  + oStock.dInput
                  - oStock.dOutput), 10).toFixed(globalData.DEC_QTY),
        oStock.unit
    ]).draw( false );
  }

  addStockToTableComplete(oStock) {
    oCompleteStockTable.row.add( [
        oStock.item_code,
        oStock.item,
        oStock.location,
        oStock.pallet,
        oStock.lot,
        parseFloat((oStock.available_stock
                  + oStock.dInput
                  - oStock.dOutput), 10).toFixed(globalData.DEC_QTY),
        oStock.unit
    ]).draw( false );
  }
}

stockCore = new SStockCore();

function viewStock(idRow) {
    oStockTable.clear().draw();

    var oRow = oMovement.getRow(idRow);

    var idItem = oRow.iItemId;
    var idUnit = oRow.iUnitId;

    globalData.lFStock.forEach( function(stock) {
      if (stock.item_id == idItem && stock.unit_id == idUnit) {
          stockCore.addStockToTable(stock);
      }
    });
}

function stockComplete() {
    oCompleteStockTable.clear().draw();

    globalData.lFStock.forEach( function(stock) {
        stockCore.addStockToTableComplete(stock);
    });

}
