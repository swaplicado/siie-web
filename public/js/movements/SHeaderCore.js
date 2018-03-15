/**
 * class dedicated to manage de header
 */
class SHeaderCore {

    constructor() {
      this.lItems = [];
      this.lLots = [];
      this.lPallets = [];
      this.lStock = [];
    }

    /**
     * set the values of input elements to movement object
     *
     * @param {SMovement} movement
     * @param {SGlobalData} globalData
     */
    setValuesToMovement(movement, globalData) {
        console.log(globalData);
        movement.iMvtType = document.getElementById('mvt_whs_type_id').value;
        movement.iMvtSubType = document.getElementById('mvt_com').value;
        movement.tDate = document.getElementById('dt_date').value;

        if (movement.iMvtType == globalData.MVT_TP_OUT_TRA) {
            movement.iWhsSrc = document.getElementById('whs_src').value;
            movement.iWhsDes = document.getElementById('whs_des').value;
        }
        else {
          if (globalData.bIsInputMov) {
             movement.iWhsDes = document.getElementById('whs_des').value;
          }
          else {
             movement.iWhsSrc = document.getElementById('whs_src').value;
          }
        }
    }

    /**
     * obtain the neccesary data to proccess the movement
     * stock
     * permitted items
     * lots
     * pallets
     * locations
     *
     * @param {SMovement} movement
     * @param {SGlobalData} globalData
     *
     * call to initializate method
     */
    getValuesFromServer(movement, globalData) {
      //ajax
      console.log(movement);
      var idMov = (globalData.iOperation == globalData.lOperationType.EDITION ? globalData.iMvtId : 0);

      showLoading(5000);
      $.get('./' + (globalData.oDocument != 0 ? 'supply' : 'create' ) +
                    '/data?whs_source=' + movement.iWhsSrc +
                    '&whs_des=' + movement.iWhsDes +
                    '&mvt_cls=' + globalData.iMvtClass +
                    '&mvt_type=' + globalData.iMvtType +
                    '&mvt_id=' + idMov,
       function(data) {
          var serverData = JSON.parse(data);

          initializePanel(serverData);
       });
    }

    /**
     *  receive the rows of movement from server and transform them
     *  to js rows to manage them on the client side
     *  update the stock in the present movement
     *
     * @param  {SMovementRow List} lRows
     *
     * add the js SMovementRow to movement object
     */
    transformServerToClientRows(lRows) {
      lRows.forEach(function(oRow) {
          var jsRow = rowsCore.rowtoJsRow(oRow);
          if (headerCore.validateAndUdpateStock(jsRow, globalData.lOperation.INPUT)) {
              oMovement.addRow(jsRow);
          }
      });
    }

    /**
     *  receive the rows of document from server and transform them
     *  to js rows to manage them on the client side
     *
     * @param  {SMovementRow List} lRows
     *
     * add the js SMovementRow to movement object
     */
    transformServerToClientDocRows(lRows) {
      lRows.forEach(function(oRow) {
          var jsRow = supplyCore.docRowtoJsRow(oRow);
            oMovement.addRow(jsRow);
      });
    }

    /**
     * parse the stock object to double to do operations on server side
     */
    initializeStock() {
      globalData.lFStock.forEach(function(stockRow) {
        stockRow.available_stock = parseFloat(stockRow.available_stock, 10);
        stockRow.dInput = 0;
        stockRow.dOutput = 0;
        stockRow.dCurrent = parseFloat(stockRow.available_stock, 10);
      });
    }

    /**
     * validate if the received row can be aggregated to movement based
     * in the stock
     *
     * @param  {SMovementRow} oMovRow   [description]
     * @param  {Integer} iOperType can be:
     *                                     globalData.lOperation.INPUT
     *                                     globalData.lOperation.OUTPUT
     * @return {boolean} if the row can not be added to movement show a message
     *                       and return false
     *                   if there is no problem, return true
     */
    validateAndUdpateStock(oMovRow, iOperType) {
      var bFound = false;
      var bValid = false;
      var BreakException = {};

      if (globalData.bIsInputMov) {
        return true;
      }

      try{
        globalData.lFStock.forEach(function(stockRow) {
            if (oMovRow.iItemId == stockRow.item_id &&
                      oMovRow.iUnitId == stockRow.unit_id) {
                if (oMovRow.iLocationId == stockRow.location_id &&
                          oMovRow.iPalletId == stockRow.pallet_id) {
                    if (oMovRow.bIsLot) {
                        oMovRow.lotRows.forEach(function(oLotRow) {
                           if (stockRow.lot_id == oLotRow.iLotId) {
                                bFound = true;
                               if (globalData.lOperation.INPUT == iOperType) {
                                 if ((stockRow.available_stock
                                     + stockRow.dInput
                                     - stockRow.dOutput) >= parseFloat(oMovRow.dQuantity, 10)) {
                                          stockRow.dOutput += parseFloat(oMovRow.dQuantity, 10);
                                          bValid = true;
                                   }
                                   else {
                                     swal(guiFunctions.getStockAlert(oMovRow.sItemCode, oMovRow.sItem,
                                                                        oMovRow.sUnit, oMovRow.dQuantity, stockRow));
                                   }
                                   throw BreakException;
                               }
                               else {
                                   stockRow.dInput += parseFloat(oMovRow.dQuantity, 10);
                                   bValid = true;
                                   // throw BreakException;
                               }
                           }
                        });
                    }
                    else {
                      bFound = true;
                      if (globalData.lOperation.INPUT == iOperType) {
                          if ((stockRow.available_stock
                              + stockRow.dInput
                              - stockRow.dOutput) >= oMovRow.dQuantity) {
                                  stockRow.dOutput += parseFloat(oMovRow.dQuantity, 10);
                                  bValid = true;
                          }
                          else {
                              swal(guiFunctions.getStockAlert(oMovRow.sItemCode, oMovRow.sItem,
                                                           oMovRow.sUnit, oMovRow.dQuantity, stockRow));
                              throw BreakException;
                          }
                          // throw BreakException;
                      }
                      else {
                          stockRow.dInput += parseFloat(oMovRow.dQuantity, 10);
                          bValid = true;
                          throw BreakException;
                      }
                    }
                }
            }
        });
      }
      catch (e) {
        if (e !== BreakException) {
         throw e;
        }
      }

      if (! globalData.bIsInputMov && !bFound) {
        swal("Error", "El material/producto no tiene existencias en el almacén.", "error");
        return false;
      }

      return bValid;
    }
}

var headerCore = new SHeaderCore();

/**
 * initializate the pannel to start a new operation
 *
 * @param  {SData} serverData object from server
 */
function initializePanel(serverData) {
    globalData.lFItems = serverData.lItems;
    globalData.lFLots = serverData.lLots;
    globalData.lFPallets = serverData.lPallets;
    globalData.lFStock = serverData.lStock;
    globalData.lFSrcLocations = serverData.lSrcLocations;
    globalData.lFDesLocations = serverData.lDesLocations;

    headerCore.initializeStock();

    if (globalData.bIsInputMov) {
        if (serverData.iFolioDes == 0) {
          swal("Error", "No hay folio asignado para el movimiento.", "error");
          goToBack();
        }
    }
    else {
        if (serverData.iFolioSrc == 0) {
          swal("Error", "No hay folio asignado para el movimiento.", "error");
          goToBack();
        }
    }

    guiValidations.validatePrice();
    iElementType = globalData.lElementsType.ITEMS;
    updateTable(globalData.lElementsType.ITEMS);
    updateLocationsTable();
    guiValidations.hideLots();
    guiValidations.hidePallet();
    guiValidations.showPanel();

    if (oMovement.rows.size > 0 && globalData.iMvtType != globalData.MVT_TP_IN_PUR) {
        guiValidations.showDelete();
    }
    else {
        guiValidations.hideDelete();
    }

    if (globalData.iMvtType == globalData.MVT_TP_IN_PUR) {
        guiTransSupp.showSetDataButton();
    }

    if (globalData.bIsInputMov) {
      itemSelection.setDefaultLocation(globalData.lFDesLocations);
    }
    else {
      itemSelection.setDefaultLocation(globalData.lFSrcLocations);
    }

    $('#item').focus();
}

/**
 * the table of items, pallets and lots is the same,
 * this method change the data of these table to show
 * differents kinds of elements
 *
 * @param  {integer} iElementType can be:
 *                                        globalData.lElementsType.ITEMS
 *                                        globalData.lElementsType.LOTS
 *                                        globalData.lElementsType.PALLETS
 */
function updateTable(iElementType) {
  var aColumns = [];
  var oData = [];

  if (iElementType == globalData.lElementsType.ITEMS) {
    aColumns = [
          {
              "title": "idItem",
              "data": "id_item"
          }, {
              "title": "idUnit",
              "data": "id_unit"
          }, {
              "title": "-",
              "data": "id_item"
          }, {
              "title": "-",
              "data": "id_item"
          }, {
              "title": "-",
              "data": "id_item"
          }, {
              "title": "Clave",
              "data": "item_code"
          }, {
              "title": "Mat/prod",
              "data": "item_name"
          }, {
              "title": "Un.",
              "data": "unit_code"
          }, {
              "title": "Existencia.",
              "data": "available_stock",
              "className": "text-right"
          }
      ];

      oData = globalData.lFItems;
  }
  else if (iElementType == globalData.lElementsType.LOTS) {
    aColumns = [
          {
              "title": "idItem",
              "data": "id_item"
          }, {
              "title": "idUnit",
              "data": "id_unit"
          }, {
              "title": "idLot",
              "data": "id_lot"
          }, {
              "title": "Lote",
              "data": "lot"
          }, {
              "title": "F. Vencimiento",
              "data": "dt_expiry"
          }, {
              "title": "Clave",
              "data": "item_code"
          }, {
              "title": "Mat/prod",
              "data": "item_name"
          }, {
              "title": "Un.",
              "data": "unit_code"
          }, {
              "title": "Existencia.",
              "data": "available_stock",
              "className": "text-right"
          }
      ];

      var lLots = new Array();

      globalData.lFLots.forEach(function(lot) {
          if (lot.id_item == elementToAdd.iItemId &&
                lot.id_unit == elementToAdd.iUnitId) {
              lLots.push(lot);
          }
      });

      oData = lLots;
  }
  else {
    aColumns = [
          {
              "title": "idItem",
              "data": "id_item"
          }, {
              "title": "idUnit",
              "data": "id_unit"
          }, {
              "title": "idPallet",
              "data": "id_pallet"
          }, {
              "title": "idPallet",
              "data": "id_pallet"
          }, {
              "title": "Tarima",
              "data": "pallet"
          }, {
              "title": "Clave",
              "data": "item_code"
          }, {
              "title": "Mat/prod",
              "data": "item_name"
          }, {
              "title": "Un.",
              "data": "unit_code"
          }, {
              "title": "Existencia.",
              "data": "available_stock",
              "className": "text-right"
          }
      ];

    var lPallets = new Array();

    globalData.lFPallets.forEach(function(pallet) {
        if (pallet.id_item == elementToAdd.iItemId &&
              pallet.id_unit == elementToAdd.iUnitId) {
            lPallets.push(pallet);
        }
    });

    oData = lPallets;
  }

  if (oItemsTable != null) {
    oItemsTable.destroy();
  }

  oItemsTable = $('#items_table').DataTable({
      "language": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      },
      "scrollY":        "50vh",
      "scrollCollapse": true,
      "paging":         false,
      "data": oData,
      "columns": aColumns
  });

  if (iElementType == globalData.lElementsType.ITEMS) {
    oItemsTable.column( 0 ).visible( false );
    oItemsTable.column( 1 ).visible( false );
    oItemsTable.column( 2 ).visible( false );
    oItemsTable.column( 3 ).visible( false );
    oItemsTable.column( 4 ).visible( false );

    document.getElementById('modal_title').innerText = 'Material/Producto';
  }
  else if (iElementType == globalData.lElementsType.LOTS) {
    oItemsTable.column( 0 ).visible( false );
    oItemsTable.column( 1 ).visible( false );
    oItemsTable.column( 2 ).visible( false );

    document.getElementById('modal_title').innerText = 'Lotes';
  }
  else {
    oItemsTable.column( 0 ).visible( false );
    oItemsTable.column( 1 ).visible( false );
    oItemsTable.column( 2 ).visible( false );
    oItemsTable.column( 3 ).visible( false );

    document.getElementById('modal_title').innerText = 'Tarimas';
  }
  if (globalData.bIsInputMov) {
    oItemsTable.column( 8 ).visible( false );
  }

  oItemsTable.columns.adjust().draw();
  oItemsTable.rows().invalidate().draw();
}

function updateLocationsTable() {
  if (oLocationsTable != null) {
    oLocationsTable.destroy();
  }

  var aColumns = [];
  var oData = [];

  aColumns = [
        {
            "title": "idLocation",
            "data": "id_whs_location"
        }, {
            "title": "Código",
            "data": "code"
        }, {
            "title": "Ubicación",
            "data": "name"
        }, {
            "title": "Default",
            "data": "is_default"
        }
    ];

    if (globalData.bIsInputMov) {
        oData = globalData.lFDesLocations;
    }
    else {
        oData = globalData.lFSrcLocations;
    }

    oLocationsTable = $('#locations_table').DataTable({
        "language": {
          "sProcessing":     "Procesando...",
          "sLengthMenu":     "Mostrar _MENU_ registros",
          "sZeroRecords":    "No se encontraron resultados",
          "sEmptyTable":     "Ningún dato disponible en esta tabla",
          "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
          "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
          "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
          "sInfoPostFix":    "",
          "sSearch":         "Buscar:",
          "sUrl":            "",
          "sInfoThousands":  ",",
          "sLoadingRecords": "Cargando...",
          "oPaginate": {
              "sFirst":    "Primero",
              "sLast":     "Último",
              "sNext":     "Siguiente",
              "sPrevious": "Anterior"
          },
          "oAria": {
              "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
              "sSortDescending": ": Activar para ordenar la columna de manera descendente"
          }
        },
        "scrollY":        "50vh",
        "scrollCollapse": true,
        "paging":         false,
        "data": oData,
        "columns": aColumns
    });

    oLocationsTable.column( 0 ).visible( false );

    oLocationsTable.columns.adjust().draw();
    oLocationsTable.rows().invalidate().draw();
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
        console.log('I was closed by the timer');
      }
    });
}