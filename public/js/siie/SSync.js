var app = new Vue({
    el: '#app',
    data: {
      message: 'Hello Vue!',
      nFormulas: 0,
      nPOs: 0
    },
    methods: {
        syncMms: function() {
            showLoading();

            axios.get('../siie/import/mms')
                .then(res => {
                    console.log("respuesta");
                    console.log(res);
                    let oData = res.data;
                    this.nFormulas = oData.formulas;
                    this.nPOs = oData.prod_orders;

                    location.reload();
                })
                .catch(err => {
                console.log(err);
            })
        }
    },
})

function showLoading() {
    swal({
        title: 'Espere',
        text: 'Sincronizando...',
        timer: 500000,
        onOpen: () => {
            swal.showLoading()
        }
    }).then((result) => {
        if (result.dismiss === 'timer') {
        }
    });
}