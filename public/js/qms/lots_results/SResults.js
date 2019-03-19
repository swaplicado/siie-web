class SResults {

    constructor() {
        this.lCaptureRows = new Array();
        this.oLot;
        this.oItem;
    }

    getData(iLot) {

        $.ajax({
            type: "GET",
            url: './results/index/getanalysis',
            data: { idLot : iLot },
            dataType : 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function( dataserv, textStatus, jqXHR ) {
            //    var serverData = JSON.parse(dataserv);
               var serverData = dataserv;
     
               oResults.loadModal(serverData);
               oGuiResults.showModal();
            },
            error : function( jqXHR, textStatus, errorThrown ) {
                alert('Disculpe, hubo un problema');
            },
          });
    }

    loadModal(serverData) {
        oResults.lCaptureRows = new Array();
        oResults.oLot = serverData.oLot;
        oResults.oItem = serverData.oItem;

        for (let index = 0; index < serverData.lAnalysis.length; index++) {
            const element = serverData.lAnalysis[index];
            let oRow = new SCaptureRow();
            oRow.sType = element._typecode;
            oRow.iAnalysis = element.id_analysis;
            oRow.sCodeName = element.code + "-" + element.name;
            oRow.sStandard = element.standard;
            oRow.dMin = element.min_value;
            oRow.dMax = element.max_value;
            oRow.dResult = 0;
            
            oResults.lCaptureRows.push(oRow);
        }

        oGuiResults.fillModal(oResults.lCaptureRows, oResults.oLot.lot, oResults.oLot.dt_expiry, oResults.oItem.name, oResults.oItem.gender.group.family.name);
    }
}

var oResults = new SResults();

function getModal(iLot) {
    showLoading(2000);

    oResults.getData(iLot);
}