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
}