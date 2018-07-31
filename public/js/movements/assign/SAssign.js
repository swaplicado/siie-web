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
        assignCore.updateLabels();
     });
  }

  updateLabels() {
    document.getElementById('src_item').innerText = assignCore.oSrcPO.item.name;

    if (globalData.iAssignType == globalData.scmms.ASSIGN_TYPE.PP) {
      document.getElementById('des_item').innerText = assignCore.oDesPO.item.name;
    }
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
