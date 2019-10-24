class SModal {
    showModal() {
        $('#previousPrintModal').modal('show');
    }

    setLot(iLot) {
        document.getElementById("id_lot").value = iLot;
    }

    setCertDate(sDate) {
        document.getElementById("cert_date").value = sDate;
    }
}

var oModal = new SModal();

function onPrint(iLot, sDate) {
    oModal.setLot(iLot);
    oModal.setCertDate(sDate);
    oModal.showModal();
}