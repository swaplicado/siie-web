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
        movement.iMvtType = document.getElementById('mvt_whs_type_id').value;
        movement.iMvtSubType = document.getElementById('mvt_com').value;
        movement.tDate = document.getElementById('dt_date').value;
        if (! Number.isInteger(globalData.oDocument)) {
          movement.iDocumentId = globalData.oDocument.id_document;
        }

        if ((movement.iMvtType == globalData.scwms.MVT_TP_OUT_TRA && !globalData.bIsExternalTransfer)
              || utilFunctions.isProductionTransfer(movement.iMvtType)) {
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

        if (globalData.bIsExternalTransfer) {
            movement.iBranchDes = document.getElementById('branch_des').value;
            movement.iWhsDes = 2;
        }

        movement.iPOSrc = document.getElementById('src_po').value;
        movement.iPODes = document.getElementById('des_po').value;
        movement.iAuxAssigType = globalData.iAssignType;
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
      var idMov = (globalData.iOperation == globalData.lOperationType.EDITION ? globalData.iMvtId : 0);

      showLoading(5000);
      $.get('./' + (globalData.sRoute) +
                    '/data?whs_source=' + movement.iWhsSrc +
                    '&whs_des=' + movement.iWhsDes +
                    '&mvt_cls=' + globalData.iMvtClass +
                    '&mvt_type=' + globalData.iMvtType +
                    '&mvt_sub_type=' + movement.iMvtSubType +
                    '&src_po=' + movement.iPOSrc +
                    '&des_po=' + movement.iPODes +
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

      if (globalData.bIsInputMov
            || oMovRow.bIsDeleted
              || utilFunctions.isProductionDelivery(globalData.iMvtType)) {
        return true;
      }

      try {
        var lStockResp = new Array();
        globalData.lFStock.forEach(function(stockRow) {
            var oStk = {dInput: stockRow.dInput, dOutput: stockRow.dOutput};
            lStockResp.push(oStk);
        });

        globalData.lFStock.forEach(function(stockRow) {
            if (oMovRow.iItemId == stockRow.item_id &&
                      oMovRow.iUnitId == stockRow.unit_id) {
                if (oMovRow.iLocationId == stockRow.location_id) {
                  if (oMovRow.iPalletId == stockRow.pallet_id) {
                    if (oMovRow.bIsLot) {
                        oMovRow.lotRows.forEach(function(oLotRow) {
                           if (stockRow.lot_id == oLotRow.iLotId) {
                                bFound = true;
                               if (globalData.lOperation.INPUT == iOperType) {
                                 if ((stockRow.available_stock
                                     + stockRow.dInput
                                     - stockRow.dOutput) >= parseFloat(oLotRow.dQuantity, 10))
                                   {
                                          stockRow.dOutput += parseFloat(oLotRow.dQuantity, 10);
                                          bValid = true;
                                   }
                                   else {
                                     swal(guiFunctions.getStockAlert(oMovRow.sItemCode, oMovRow.sItem,
                                                                        oMovRow.sUnit, oLotRow.dQuantity, stockRow));
                                      bValid = false;
                                      throw BreakException;
                                   }

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
                              - stockRow.dOutput) >= oMovRow.dQuantity)
                          {
                                  stockRow.dOutput += parseFloat(oMovRow.dQuantity, 10);
                                  bValid = true;
                          }
                          else {
                              swal(guiFunctions.getStockAlert(oMovRow.sItemCode, oMovRow.sItem,
                                                           oMovRow.sUnit, oMovRow.dQuantity, stockRow));
                              bValid = false;
                              throw BreakException;
                          }
                      }
                      else {
                          stockRow.dInput += parseFloat(oMovRow.dQuantity, 10);
                          bValid = true;
                          throw BreakException;
                      }
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

      if (! bValid) {
          oMovRow.lotRows = new Map();
          var i = 0;
          globalData.lFStock.forEach(function(stockRow) {
             stockRow.dInput = lStockResp[i].dInput;
             stockRow.dOutput = lStockResp[i].dOutput;
             i++;
          });
      }

      if (! globalData.bIsInputMov && !bFound) {
        swal("Error", "El material/producto " +  oMovRow.sItemCode +
                       "-" + oMovRow.sItem + " no tiene existencias en " +
                      "la tarima, lote, ubicación y almacén seleccionados.", "error");
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
        if (serverData.iFolioDes == 0 && globalData.iMvtType != globalData.PHYSICAL_INVENTORY) {
          alert("No hay folio asignado para el movimiento.");
          // swal("Error", "No hay folio asignado para el movimiento.", "error");
          goToBack();
        }
    }
    else {
        if (serverData.iFolioSrc == 0) {
          alert("No hay folio asignado para el movimiento.");
          // swal("Error", "No hay folio asignado para el movimiento.", "error");
          goToBack();
        }
    }

    guiValidations.validatePrice();
    guiFunctions.setQuantity(1);
    if (globalData.isPalletReconfiguration) {
      iElementType = globalData.lElementsType.PALLETS;
      searchCore.initializateItems(iElementType);
    }
    else {
      iElementType = globalData.lElementsType.ITEMS;
      searchCore.initializateItems(iElementType);
    }

    locationsJs.updateLocationsTable();

    guiValidations.hideLots();
    oLotsTable.clear().draw();

    guiValidations.hidePallet();
    guiValidations.showPanel();
    guiValidations.showFreeze();

    if (oMovement.rows.size > 0) {
        guiValidations.showDelete();
    }
    else {
        guiValidations.hideDelete();
    }

    guiFunctions.updateAmtQtyLabels();
    guiValidations.showInfo();

    if (globalData.iMvtType == globalData.MVT_TP_IN_PUR ||
        globalData.iMvtType == globalData.MVT_TP_IN_SAL ||
          globalData.iMvtType == globalData.MVT_TP_OUT_SAL) {
        guiTransSupp.showSetDataButton();
    }

    if (globalData.bIsInputMov) {
        locationsJs.setDefaultLocation(globalData.lFDesLocations);
    }
    else {
        locationsJs.setDefaultLocation(globalData.lFSrcLocations);
    }

    if (globalData.iMvtType == globalData.scwms.MVT_TP_OUT_TRA
        || utilFunctions.isProductionTransfer(oMovement.iMvtType)) {
        locationsJs.setDefaultLocationDes(globalData.lFDesLocations);

        if (globalData.bIsExternalTransfer) {
            guiValidations.hideLocationDes();
        }
        else {
            guiValidations.showLocationDes();
        }
        guiValidations.showLocationDesLabel();
    }

    if (utilFunctions.isProductionMovement(oMovement.iMvtType)) {
        guiProduction.setProductionOrder(serverData.oProductionOrder);
        guiValidations.showPOBtn();
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

  if (iElementType == globalData.lElementsType.LOTS) {
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

  if (oElementsTable != null) {
    oElementsTable.destroy();
  }

  oElementsTable = $('#items_table').DataTable({
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
      "scrollY": "50vh",
      "scrollX": true,
      "scrollCollapse": true,
      "colReorder": true,
      "paging": false,
      "data": oData,
      "columns": aColumns
  });

  if (iElementType == globalData.lElementsType.LOTS) {
    oElementsTable.column( 0 ).visible( false );
    oElementsTable.column( 1 ).visible( false );
    oElementsTable.column( 2 ).visible( false );

    document.getElementById('modal_title').innerText = 'Lotes';
  }
  else {
    oElementsTable.column( 0 ).visible( false );
    oElementsTable.column( 1 ).visible( false );
    oElementsTable.column( 2 ).visible( false );
    oElementsTable.column( 3 ).visible( false );

    document.getElementById('modal_title').innerText = 'Tarimas';
  }
  if (globalData.bIsInputMov) {
    oElementsTable.column( 8 ).visible( false );
  }

  oElementsTable.columns.adjust().draw();
  oElementsTable.rows().invalidate().draw();
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
