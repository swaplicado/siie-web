function showLoading() {
    swal({
        title: 'Espere',
        text: 'Sincronizando...',
        timer: 500000,
        onOpen: () => {
            swal.showLoading()
        }
    }).then((result) => {
        if (result.dismiss === 'timer') {}
    });
}