class SAssign {
  constructor() {
     this.oSrcPO = null;
  }

  goToServer(iPOSrc, iPODes) {
    $.get('./' + (globalData.sRoute) +
                  '/productiondata?po_src=' + iPOSrc +
                  '&po_des=' + iPODes +
                  '&assig_type=' + globalData.iAssignType,
     function(data) {
        var serverData = JSON.parse(data);

        assignCore.oSrcPO = serverData.oSrcPO;
        assignCore.updateDesProdOrders(serverData.lDesPO);
        assignCore.updateLabels();
     });
  }

  updateLabels() {
    document.getElementById('src_item').innerText = assignCore.oSrcPO.item.name;

    if (globalData.iAssignType == globalData.scmms.ASSIGN_TYPE.PP) {
      var poDes = document.getElementById('des_po').value;
    }
  }

  updateDesProdOrders(lDesPO) {
      $('#des_po').empty();
      $.each(lDesPO, function(index, oDesPO) {
        var option = $("<option value=" + oDesPO.id_order + "></option>")
                    .attr(oDesPO, index)
                    .text(oDesPO.folio + '-' + oDesPO.identifier);

        $('#des_po').append(option);
      });

      var poDes = document.getElementById('des_po').value;

      $.each(lDesPO, function(index, oDesPO) {
         if (oDesPO.id_order == poDes) {
            document.getElementById('des_item').innerText = oDesPO.item.name;
            // break;
         }
      });
  }
}

var assignCore = new SAssign();

function updatePOs() {
    var poSrc = document.getElementById('src_po').value;
    var poDes = document.getElementById('des_po').value;

    assignCore.goToServer(poSrc, poDes);
}

function openModal() {
  updatePOs();

  $('#po_modal').modal('show');
}
