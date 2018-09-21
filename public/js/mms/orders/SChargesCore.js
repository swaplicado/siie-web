class SChargesCore {

  showProductionOrder(oProductionOrder) {
      oGuiCharges.setBranch(oProductionOrder.branch_name);
      oGuiCharges.setPlan(guiKardex.stringfill(oProductionOrder.plan_folio, globalData.LEN_FOL, '0')
                                    + '-' + oProductionOrder.production_plan);
      oGuiCharges.setFloor(oProductionOrder.floor_name);
      oGuiCharges.setPODate(oProductionOrder.date);
      oGuiCharges.setPOType(oProductionOrder.type_name);
      oGuiCharges.setItem(oProductionOrder.item_code + '-' +oProductionOrder.item);
      oGuiCharges.setPOQuantity(oProductionOrder.charges);
      oGuiCharges.setUnit(oProductionOrder.unit_code);
      oGuiCharges.setFolio(oProductionOrder.folio);
      oGuiCharges.setIdentifier(oProductionOrder.identifier);
      oGuiCharges.setFatherFolio(oProductionOrder.father_folio);

      showLoading(4000);

      var sRoute = './orders/' + oProductionOrder.id_order + '/details';

      $.get(sRoute,
       function(data) {
          var serverData = JSON.parse(data);
          console.log(serverData);
          oChargesTable.clear().draw();

          if (serverData.length > 0) {
            for (var i = 0; i < serverData.length; i++) {
              oGuiCharges.addRowToChargesTable(serverData[i]);
            }
          }
       });

      $('#seePO').modal('show');
  }

}

var oCharges = new SChargesCore();

function showPO(oProductionOrder) {
    oCharges.showProductionOrder(oProductionOrder);
}
