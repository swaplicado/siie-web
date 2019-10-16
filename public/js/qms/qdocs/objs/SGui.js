class SGui {
    showLoading(dTime) {
        swal({
            title: 'Espere',
            timer: dTime,
            onOpen: () => {
                swal.showLoading()
            }
        }).then((result) => {
            if (result.dismiss === 'timer') {
            }
        });
    }

    showOk() {
        swal({
            type: 'success',
            title: 'Realizado',
            showConfirmButton: false,
            timer: 1500
        });
    }

    showError(sMessage) {
        swal({
            type: 'error',
            title: sMessage,
            showConfirmButton: false,
            timer: 1500
        });
    }
}