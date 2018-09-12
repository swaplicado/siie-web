class SOrdersCore {

  constructor() {
      this.oProductionOrder = null;
      this.lProductionOrders = null;
      this.bChangeStatus = false;
  }

  setOrders(lOrders) {
      ordersCore.lProductionOrders = lOrders;
  }

  makeConsumption() {
    var sRoute = './orders/' + ordersCore.oProductionOrder.id_order + '/consumptions';

    $.get(sRoute,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);

        oConsumeTable.clear().draw();
        if (serverData.length > 0) {
            for (var i = 0; i < serverData.length; i++) {
              ordersCore.addRow(serverData[i]);
            }
        }

        $('#consumModal').modal('show');
     });
  }

  consume() {
    showLoading(4000);

    var sRoute = './orders/' + ordersCore.oProductionOrder.id_order + '/consume';

    $.get(sRoute,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);

        if (Array.isArray(serverData) && serverData.length > 0) {
            var sError = '';

            for (var i = 0; i < serverData.length; i++) {
              sError += serverData[i] + '. ';
            }

            swal('Error', sError, 'error');
        }
        else {
            if (ordersCore.bChangeStatus) {
              ordersCore.changeStatus(globalData.scmms.NEXT_ST);
            }
            else {
              swal(
                  'Hecho',
                  'La operación de realizó correctamente',
                  'success',
                );
            }
        }
     });
  }

  processStatus(oOrder, operation) {
    showLoading(3000);
    ordersCore.oProductionOrder = oOrder;

    if (operation == globalData.scmms.NEXT_ST &&
          (ordersCore.oProductionOrder.status_id + 1) == globalData.scmms.PO_STATUS.ST_ENDED) {
      ordersCore.bChangeStatus = true;
      ordersCore.makeConsumption();
    }
    else {
      ordersCore.changeStatus(operation);
    }
  }

  changeStatus(operation) {
    var sOp = operation == globalData.scmms.NEXT_ST ? 'next' : 'previous';
    var sRoute = './orders/' + ordersCore.oProductionOrder.id_order + '/' + sOp;

    $.get(sRoute,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);

        if (serverData.length > 0) {
          var sError = '';
          for (var i = 0; i < serverData.length; i++) {
            sError += serverData[i] + '. ';
          }
          swal(
              'Error', sError, 'error',
            );
        }
        else {
          swal(
              'Realizado',
              'La orden de producción ha cambiado de estado',
              'success',
            );

            sleepFunction(6000);

          $(document).ajaxStop(function(){
              window.location.reload();
          });
        }
     });
  }

  addRow(oConsumeRow) {
      oConsumeTable.row.add([
          oConsumeRow.item_code,
          oConsumeRow.item,
          oConsumeRow.lot,
          oConsumeRow.dt_expiry,
          oConsumeRow.pallet_id == 1 ? 'SIN TARIMA' : oConsumeRow.pallet_id,
          oConsumeRow.loc_code,
          oConsumeRow.whs_code,
          oConsumeRow.branch_code,
          parseFloat(oConsumeRow.delivered, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oConsumeRow.returned, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oConsumeRow.consumed, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oConsumeRow.to_consume, 10).toFixed(globalData.DEC_QTY),
          oConsumeRow.unit_code
      ]).draw( false );
  }

  getPo(row) {
    console.log();
    var bFound = false;
    for (var i = 0; i < ordersCore.lProductionOrders.length; i++) {
      var oPo = ordersCore.lProductionOrders[i];
      if (row[0] == oPo.id_order) {
          return oPo;
      }
    }

    return null;
  }
}

var ordersCore = new SOrdersCore();

function next(oOrder) {
    ordersCore.processStatus(oOrder, globalData.scmms.NEXT_ST);
}

function previous(oOrder) {
    ordersCore.processStatus(oOrder, globalData.scmms.PREVIOUS_ST);
}

function consume() {
    ordersCore.consume();
}

function consumeByButton() {
   var row = oOrdersTable.row('.selected').data();

   if (row == undefined) {
     swal("Error", "Debe seleccionar una orden de producción.", "error");
     return false;
   }

   var oPo = ordersCore.getPo(row);
   if (oPo != null) {
      ordersCore.oProductionOrder = oPo;
      ordersCore.bChangeStatus = false;
      ordersCore.makeConsumption();
   }
}

/**
 * function sleep
 *
 * @param  {double} dTime time in milliseconds
 *
 */
async function sleepFunction(dTime) {
    await sleep(dTime);
}
