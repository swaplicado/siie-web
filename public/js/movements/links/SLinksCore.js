class SLinksCore {

  constructor() {
    this.iDocumentRowId = 0;
  }

  /**
   * [serverToClientMovements description]
   * @param  {array} lServerMovs array of server movements
   *
   * @return {Map} Map of SMovement
   */
  serverToClientMovements(lServerMovs) {
      var lMovements = new Map();

      var iIndex = 0;
      lServerMovs.forEach(function(sMovement) {
          var cMovement = new SMovement();

          cMovement.iAuxIndex = iIndex;
          cMovement.sBranch = sMovement.branch.name;
          cMovement.sWarehouse = sMovement.warehouse.name;

          sMovement.rows.forEach(function(sMovRow) {
              var cMovRow = new SMovementRow();

              cMovRow.iIdMovRow = sMovRow.id_mvt_row;
              cMovRow.iItemId = sMovRow.item_id;
              cMovRow.iUnitId = sMovRow.unit_id;
              cMovRow.bIsLot = sMovRow.item.is_lot;
              cMovRow.bIsBulk = sMovRow.item.is_bulk;
              cMovRow.dPrice = sMovRow.amount_unit;
              cMovRow.dQuantity = sMovRow.quantity;
              cMovRow.bIsDeleted = sMovRow.is_deleted;
              cMovRow.iPalletId = sMovRow.pallet_id;
              cMovRow.iDocOrderRowId = sMovRow.doc_order_row_id;
              cMovRow.iDocInvoiceRowId = sMovRow.doc_invoice_row_id;
              cMovRow.iDocDebitNoteRowId = sMovRow.doc_debit_note_row_id;
              cMovRow.iDocCreditNoteRowId = sMovRow.doc_credit_note_row_id;

              if (sMovRow.lot_rows.length > 0) {
                  sMovRow.lot_rows.forEach(function(sLotRow) {
                      var cLotRow = new SLotRow();

                      cLotRow.iIdLotRow = sLotRow.id_mvt_row_lot;
                      cLotRow.dQuantity = sLotRow.quantity;
                      cLotRow.bIsDeleted = sLotRow.is_deleted;
                      cLotRow.iLotId = sLotRow.lot_id;
                      cLotRow.sLot = sLotRow.lot.lot;
                      cLotRow.tExpDate = sLotRow.lot.dt_expiry;

                      cMovRow.addLotRow(cLotRow);
                  });
              }

              cMovement.addRow(cMovRow);
          });

          lMovements.set(iIndex, cMovement);
          iIndex++;
      });

      return lMovements;
  }

  docRowtoJsRow(oRow) {
    var oJsRow = new SMovementRow();

    oJsRow.iItemId = oRow.item_id;
    oJsRow.iUnitId = oRow.unit_id;
    oJsRow.bIsLot = oRow.is_lot;
    oJsRow.bIsBulk = oRow.is_bulk;
    oJsRow.dPrice = parseFloat(oRow.price_unit_cur, 10);
    oJsRow.dQuantity = oRow.pending;

    oJsRow.sItem = oRow.concept;
    oJsRow.sItemCode = oRow.concept_key;
    oJsRow.sUnit = oRow.unit;

    oJsRow.bIsDeleted = oRow.is_deleted;
    oJsRow.iAuxDocRowId = oRow.id_document_row;

    return oJsRow;
  }

  setData(index) {
      var oRow = linksCore.docRowtoJsRow(globalData.lDocRows[index]);

      linksCore.iDocumentRowId = oRow.iAuxDocRowId;

      guiLink.setItem(oRow.sItemCode + " " + oRow.sItem);
      guiLink.setQuantity(oRow.dQuantity);
      guiLink.setUnit(oRow.sUnit);

      linksCore.updateTable(oRow);
  }

  updateTable(oDocRow) {
    oMovscTable.clear().draw();

    var lRows = new Array();
    var iTableRowIndex = 0;
    for (var [key, oMovement] of globalData.lMovements) {
        for (var [key, oMovementRow] of oMovement.rows) {
            if (oDocRow.iItemId == oMovementRow.iItemId && oDocRow.iUnitId == oMovementRow.iUnitId) {
                oMovementRow.iDocRowIndSupp = oDocRow.iAuxDocRowId;
                oMovementRow.sBranch = oMovement.sBranch
                oMovementRow.sWarehouse = oMovement.sWarehouse
                oMovementRow.iAuxMovIndex = oMovement.iAuxIndex
                oMovementRow.iTableRowIndex = iTableRowIndex;
                oMovementRow.lAuxlotRows = Array.from(oMovementRow.lotRows);

                lRows.push(oMovementRow);

                iTableRowIndex++;
            }
        }
    }

    linksCore.callToServer(lRows);
  }

  callToServer(aTableRows) {

    showLoading(5000);
    var data = { value : JSON.stringify(aTableRows) };
    $.ajax({
      type: "POST",
      url: './' + 'link' + '/indirectsupplied',
      data: data,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
          var serverData = JSON.parse(data);
          console.log(serverData);

          for (var index = 0; index < serverData.lRowsSupplied.length; index++) {
              var oRow = serverData.lRowsSupplied[index];
              linksCore.addRowToTable(oRow, oRow.sBranch, oRow.sWarehouse, oRow.iAuxMovIndex, oRow.iTableRowIndex);
          }
          linkLotsCore.lRowsWithStock = serverData.lRowsSupplied;
       }
     });
  }

  addRowToTable(oMovementRow, sBranch, sWarehouse, iMovIndex, iTableRowIndex) {
      oMovscTable.row.add( [
          iMovIndex,
          oMovementRow.iIdRow,
          sBranch,
          sWarehouse,
          oMovementRow.sLocation,
          oMovementRow.sPallet,
          parseFloat(oMovementRow.dPrice, 10).toFixed(globalData.DEC_AMT),
          parseFloat(oMovementRow.dQuantity, 10).toFixed(globalData.DEC_QTY),
          parseFloat(oMovementRow.dQtyIndSupplied, 10).toFixed(globalData.DEC_QTY),
          parseFloat((oMovementRow.dQuantity - oMovementRow.dQtyIndSupplied), 10).toFixed(globalData.DEC_QTY),
          parseFloat(0, 10).toFixed(globalData.DEC_QTY),
          oMovementRow.bIsLot ?
                            linksCore.getLotsButton(iMovIndex, oMovementRow.iIdRow, iTableRowIndex) :
                            '--',
      ])
      .draw( false );
  }

  getLotsButton(mov, id, iTableRowIndex, bDisabled) {
    return  "<button type='button' onClick='setLots(" + mov + ", " + id + ", " + iTableRowIndex + ")' " +
                    "class='btn btn-primary btn-sm' " +
                    "title='Ver lotes'>" +
                "<i class='glyphicon glyphicon-info-sign'></i>" +
            "</button>"
  }

  updateMovementRow(iMov, iMovRow, iTableRowIndex) {
    var rowTemp = oMovscTable.row(iTableRowIndex).data();

    globalData.lMovements.get(iMov).
        getRow(iMovRow).iAuxDocRowId = linksCore.iDocumentRowId;

    rowTemp[10] = globalData.lMovements.get(iMov).
                        getRow(iMovRow).dAuxQuantity.
                                toFixed(globalData.DEC_QTY);

    $('#movs_table').dataTable().fnUpdate(rowTemp, iTableRowIndex, undefined, false);
  }

  setMovementsToForm() {
    for (var [key, oMov] of globalData.lMovements) {
       for (var [key, oRow] of oMov.rows) {
          oRow.lAuxlotRows = Array.from(oRow.lotRows);
       }
       oMov.lAuxRows = Array.from(oMov.rows);
    }

    spackage.iDocumentSourceId = globalData.oDocumentSrc == null ? 0 : globalData.oDocumentSrc.id_document;
    spackage.iDocumentDestinyId = globalData.oDocumentDes.id_document;
    spackage.lMovements = Array.from(globalData.lMovements);

    localStorage.setItem('spackage', JSON.stringify(spackage));
    document.getElementById('spackage_object').value = JSON.stringify(spackage);
  }

  linkAllOrNothing(bIsAll) {
    globalData.lMovements.forEach(function (oMov) {
       oMov.rows.forEach(function (oMovRow) {
         for (var index = 0; index < linkLotsCore.lRowsWithStock.length; index++) {
             var oRow = linkLotsCore.lRowsWithStock[index];

             if (oMovRow.iIdRow == oRow.iIdRow && oMovRow.iAuxMovIndex == oRow.iAuxMovIndex) {
                 var dToLink = bIsAll ? parseFloat(oMovRow.dQuantity, 10) - parseFloat(oRow.dQtyIndSupplied, 10) : 0;
                 oMovRow.dAuxQuantity = dToLink;

                 if (oMovRow.bIsLot) {
                   oMovRow.lotRows.forEach(function(lotRow) {
                       oRow.lAuxlotRows.forEach(function(lotRowS) {
                           if (lotRowS[1].id == lotRow.id) {
                               var dToLinkLot = bIsAll ? parseFloat(lotRow.dQuantity, 10) - parseFloat(lotRowS[1].dQuantitySupplied, 10) : 0;
                               lotRow.dAuxQuantity = dToLinkLot;
                           }
                       });
                   });
                 }

                 linksCore.updateMovementRow(oMovRow.iAuxMovIndex,
                                             oMovRow.iIdRow,
                                             oMovRow.iTableRowIndex);

                 break;
             }
         }
       });
    });
  }
}

var linksCore = new SLinksCore();

function setDataToLink() {
  var index = oDocsTable.row('.selected').index();

  if (index == undefined) {
    swal("Error", "Debe seleccionar un elemento.", "error");
    return false;
  }

  guiLink.showActions();
  linksCore.setData(index);
}

function assignAll() {
    linksCore.linkAllOrNothing(true);
}
function assignNothing() {
    linksCore.linkAllOrNothing(false);
}

function freezeMovement() {
    guiLink.unfreeze();
}
