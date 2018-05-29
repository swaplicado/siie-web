class SKardex {
  constructor() {

  }

  getMovements(iType, iId) {
    showLoading(4000);

     $.ajax({
       type: "GET",
       url: './stock/movements?iType=' + iType
                                       + '&iId=' + iId
                                       + '&iWhsOption=' + globalData.iWhsOption,
       headers: {
         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       },
       success: function(data) {
          var serverData = JSON.parse(data);

          oGuiKardex.setHeader(serverData, iType);
          oKardex.fillTable(serverData.lMovements, iType);
          oGuiKardex.showModal();
       }
     });
  }

  fillTable(serverData, iType) {
    oKardexTable.clear().draw();

    for (var i = 0; i < serverData.length; i++) {
      var oRow = serverData[i];
      oGuiKardex.addRow(oRow, iType);
    }
  }
}

var oKardex = new SKardex();

function getKardex(iType, iId) {
    if (iType == globalData.scwms.ELEMENTS_TYPE.PALLETS && iId == 1) {
      swal("Error", "Elija un renglón con tarima.", "error");
      return false;
    }
    oKardex.getMovements(iType, iId);
}

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
