class SGuiResults {

    showModal() {
        $('#captResultsModal').modal('show');
    }

    setLot(str) {
        document.getElementById("lot").value = str.toUpperCase();
    }

    setExpiration(str) {
        document.getElementById("exp_date").value = str;
    }

    setItem(str) {
        document.getElementById("item").value = str.toUpperCase();
    }

    setFamily(str) {
        document.getElementById("family").value = str.toUpperCase();
    }

    fillModal(lCaptureRows, sLot, sExpDate, sItem, sFamily) {
        oGuiResults.setLot(sLot);
        oGuiResults.setExpiration(sExpDate);
        oGuiResults.setItem(sItem);
        oGuiResults.setFamily(sFamily);

        oCaptureTable.clear().draw();

        for (let index = 0; index < lCaptureRows.length; index++) {
            oGuiResults.addRow(lCaptureRows[index]);
        }
    }

    addRow(oCaptureRow) {
        oCaptureTable.row.add([
            oCaptureRow.sCodeName,
            oCaptureRow.sStandard,
            oCaptureRow.sType,
            parseFloat(oCaptureRow.dMin, 10).toFixed(globalData.DEC_QTY),
            parseFloat(oCaptureRow.dMax, 10).toFixed(globalData.DEC_QTY),
            '<input type="number" id="d_result" ' +
                            'name="d_result" ' +
                            'class="form-control input-sm" ' +
                            ' style="text-align: right;"' +
                            'value="'+ parseFloat(oCaptureRow.dResult, 10).toFixed(globalData.DEC_QTY) +'">'
        ]).draw( false );
    }
}

var oGuiResults = new SGuiResults();

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