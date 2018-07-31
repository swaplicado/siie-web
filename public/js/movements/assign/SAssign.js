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

        this.oSrcPO = serverData.oSrcPO;
        updateLabels();
     });
  }

  updateLabels() {
    assignCore.oSrcPO

  }
}

var assignCore = new SAssign();

function updateDesPOs() {
    var poSrc = document.getElementById('src_po').value;
    var poDes = document.getElementById('des_po').value;

    assignCore.goToServer(poSrc, poDes);
}
