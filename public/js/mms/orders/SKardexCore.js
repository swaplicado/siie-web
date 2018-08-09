class SKardexCore {
  constructor() {

  }

  getKardex(iProductionOrder) {
    showLoading(4000);
    var sRoute = './orders/' + iProductionOrder + '/kardex';

    $.get(sRoute,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);
     });

     // $.ajax({
     //   type: "GET",
     //   url: './orders/kardex',
     //   headers: {
     //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     //   },
     //   success: function(data) {
     //      var serverData = JSON.parse(data);
     //      console.log(url);
     //      console.log(serverData);
     //   }
     // });
  }
}

var kardexCore = new SKardexCore();

function onKardexModal(iPO) {
    kardexCore.getKardex(iPO);
    $('#poKardexModal').modal('show');
}
