class SExternalTransfers {
  constructor() {

  }

  goToServer() {
    showLoading(2000);
    $.get('./' + (globalData.sRoute) +
                  '/data?whs_source=' + movement.iWhsSrc +
                  '&id_whs=' + movement.iWhsDes +
                  '&id_mvt=' + globalData.iMvtClass +
                  '&mvt_type=' + globalData.iMvtType +
                  '&mvt_id=' + idMov,
     function(data) {
        var serverData = JSON.parse(data);

        initializePanel(serverData);
     });
  }
}

var externalTransfers = new SExternalTransfers();

function headerContinue() {
    guiTransfers.disableHeader();
    guiTransfers.hideContinue();
    externalTransfers.goToServer();
}
