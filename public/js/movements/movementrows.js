/*
* Calls the method to add row when the button is clicked
* Row of movement
*/
$('#tButton').on('click', function(e) {
    addRow(e);
});

/*
* Calls the method to add row when the enter key is pressed
* Row of movement
*/
function addRowByEnter(e) {
  if (e.keyCode == 13) {
        addRow(e);
    }
}

/*
* Add a new row based in the code and quantity that the user writes in the
* fields, in the view of movs
* Row of movement
*/
function addRow(e) {

  if (! validateInput()) {
    return false;
  }

  var sItemCode = document.getElementById("item").value; // gets the code of code bar
  var dInputQuantity = parseFloat(document.getElementById("quantity").value); // gets the quantity
  var iWmsMvtType = parseInt(document.getElementById('mvt_whs_type_id').value);
  movement.iMvtType = iWmsMvtType;

  var iWhsId = 0;
  var whsStock = 0;
  if (globalData.bIsInputMov) {
    iWhsId = document.getElementById("whs_des").value;
  }
  else {
    iWhsId = document.getElementById("whs_src").value;
    whsStock = iWhsId;
  }

  //ajax
  $.get('./create/children?parent=' + sItemCode + '&whs=' + whsStock + '&idCls=' + globalData.iMvtClass,
   function(data) { // executes the method children of controller
      // data is the "data" that the controller returns
      $('.dataTables_empty').remove(); // remove the row with the class: dataTables_empty
      var bItemFound = false;
      console.log(data);

      $.each(data, function(index, oMovementRow) { // iterate each of the movements
        bItemFound = true;
        bFound = false;
        iRowId = 0;

        var iMovType = 0;
        if (oMovementRow.aux_lot_id > 0) {
          iMovType = globalData.IS_LOT;
          idLot = oMovementRow.aux_lot_id;
          console.log(oMovementRow);
        }
        else if (oMovementRow.pallet_id > 1) {
          iMovType = globalData.IS_PALLET;
        }
        else {
          iMovType = globalData.IS_ITEM;
        }

        var ojsMovRow = new SMovementRow(movement.rowIdentifier);

        ojsMovRow.iItemId = oMovementRow.item_id;
        ojsMovRow.iUnitId = oMovementRow.unit_id;
        ojsMovRow.iPalletId = oMovementRow.pallet_id;
        ojsMovRow.iLocationId = oMovementRow.location_id;
        ojsMovRow.oAuxItem = oMovementRow.item;
        ojsMovRow.oAuxUnit = oMovementRow.unit;
        ojsMovRow.aStock = oMovementRow.aux_stock;

        // If the definition of the platform has an assigned amount, this is set, if the user's input is not set
        ojsMovRow.dQuantity = oMovementRow.quantity > 0 ? parseFloat(oMovementRow.quantity) * dInputQuantity : dInputQuantity;
        ojsMovRow.dPrice = oMovementRow.amount_unit >= 0 ? parseFloat(oMovementRow.amount_unit) : 0;

        // checks if the pallet was selected before
        movement.rows.forEach(function(element) {
            if (element.iItemId == oMovementRow.item_id && element.iUnitId == oMovementRow.unit_id
                && element.iPalletId == oMovementRow.pallet_id) {
                bFound = true;
                iRowId = element.iIdRow;
                // break;
            }
        });

        var rowTrId = 0;
        // if pallet was selected previously
        if (bFound) {
          movement.getRow(iRowId).dQuantity += ojsMovRow.dQuantity;
          updateRowTr(iRowId, movement.getRow(iRowId).dQuantity);

          rowTrId = iRowId;
        }
        else {
          var idRow = movement.rowIdentifier; // gets the identifier of current row

          addRowTr(idRow, ojsMovRow, iWhsId, iMovType, iWmsMvtType);

          rowTrId = movement.rowIdentifier;
          movement.addRow(ojsMovRow);
          console.log(movement.rows);
        }

        //this code adds the lots to rows
        if (iMovType == globalData.IS_LOT) {
            // If the movement row is of lot, add a new lot
            addOrUpdateLotRow(rowTrId, idLot, ojsMovRow.dQuantity, ojsMovRow.dPrice);
        }
        else if (iMovType == globalData.IS_PALLET) {
          // if the movement row is of a pallet, add the lots with stock in pallet
          oMovementRow.aux_lots.forEach(function(lot) {
              addOrUpdateLotRow(rowTrId, parseInt(lot.lot_id), parseFloat(lot.quantity), parseFloat(lot.amount_unit));
          });
        }
      });

      if (! bItemFound) {
        alert("No se encontraron resultados");
      }
    });
}

/*
* Add a new Row on HTML table
*/
function addRowTr(identifier, jsRow, iWhsId, iMovType, iWmsMvtType) {
      // Pallets select
      var bPallSet = false;
      var iDefaultPallet = 1;
      var optionsPall = "";

      globalData.lPallets.forEach(function(element) {
        if ((element.item_id == jsRow.iItemId && element.unit_id == jsRow.iUnitId) ||
                element.id_pallet == 1) {
            if (!bPallSet) {
              iDefaultPallet = element.id_pallet;
            }
            bPallSet = true;
            optionsPall += "<option value=" + element.id_pallet +
                              " " + (iMovType == globalData.IS_PALLET && jsRow.iPalletId == element.id_pallet ? "selected" : "") + ">" +
                                  element.pallet +
                            "</option>";
        }
      });
      jsRow.iPalletId = iMovType == globalData.IS_PALLET ? jsRow.iPalletId : iDefaultPallet;

      //Locations select
      var bLocSet = false;
      var iDefaultLocation = 1;
      var locationOptions = "";

      globalData.lLocations.forEach(function(element) {
          var isDefault = false;
          if (element.whs_id == iWhsId) {
              if (element.is_default) {
                iDefaultLocation = element.id_whs_location;
                isDefault = true;
              }
              locationOptions += "<option " +
                                      "value=" + element.id_whs_location +
                                      (isDefault ? " selected" : "") + ">" +
                                      element.code + "-" + element.name +
                                  "</option>";
          }
      });

      if (globalData.bIsInputMov || ( !globalData.bIsInputMov && jsRow.iLocationId <= 0)) {
        jsRow.iLocationId = iDefaultLocation;
      }

    const FIRST = 0;
    const ITEM_CODE = 1;
    const ITEM_NAME = 2;
    const UNIT_CODE = 3;
    const LOCATION = 4;
    const LOTS = 5;
    const PALLETS = 6;
    const PRICE = 7;
    const QUANTITY = 8;
    const DEL = 9;
    const STOCK = 14;
    // const ITEM_ID = 10;
    // const UNIT_ID = 11;
    // const LOC_ID = 12;
    // const PALL_ID = 13;

    var values = [ //this is the array of values for the row
                identifier,
                jsRow.oAuxItem.code,
                jsRow.oAuxItem.name,
                jsRow.oAuxUnit.code,
                "",
                "  Lotes",
                "",
                parseFloat(jsRow.dPrice).toFixed(globalData.DEC_AMT),
                parseFloat(jsRow.dQuantity).toFixed(globalData.DEC_QTY),
                "",
                parseInt(jsRow.iItemId),
                parseInt(jsRow.iUnitId),
                jsRow.iLocationId,
                jsRow.iPalletId
              ];

    var tblBody = document.getElementById("lbody"); // gets the body of table by id
    var oTr = document.createElement("tr");

    oTr.setAttribute("id", identifier);

    var oTdFIRST = document.createElement("td");
    oTdFIRST.setAttribute("style", "display:none;");
    oTdFIRST.appendChild(document.createTextNode(values[FIRST]));

    var oTdITEM_CODE = document.createElement("td");
    oTdITEM_CODE.appendChild(document.createTextNode(values[ITEM_CODE]));

    var oTdITEM_NAME = document.createElement("td");
    oTdITEM_NAME.appendChild(document.createTextNode(values[ITEM_NAME]));

    var oTdUNIT_CODE = document.createElement("td");
    oTdUNIT_CODE.appendChild(document.createTextNode(values[UNIT_CODE]));

    var oTdLOCATION = document.createElement("td");
    oTdLOCATION.setAttribute("align", "center");
    oTdLOCATION.innerHTML = "<select " + (!globalData.LOCATION_ENABLED ? "disabled='true'" : "") +
                            " onChange='setLoc(this.value, this)' class='form-control'>" +
                                        locationOptions +
                            "</select>";

    var oTdPALLETS = document.createElement("td");
    oTdPALLETS.setAttribute("align", "center");
    oTdPALLETS.innerHTML = "<select " + (iWmsMvtType == globalData.MVT_TP_OUT_TRA ? "disabled='true'" : "")  +
                              " onChange='setPall(this.value, this)' class='selPallet form-control'>" +
                                          optionsPall +
                            "</select>";

    var oTdPRICE = document.createElement("td");
    oTdPRICE.setAttribute("align", "right");
    oTdPRICE.innerHTML = "<input align='right' class='form-control' type='number' " +
                                (iMovType == globalData.IS_PALLET || iMovType == globalData.IS_LOT || (iMovType == globalData.IS_ITEM && jsRow.oAuxItem.is_lot) ? "readonly='readonly'" : "") +
                                " placeholder='1.00' step='0.01' min='0' maxlength='15' size='5' value='" +
                                values[PRICE] + "'>";

    var oTdQUANTITY = document.createElement("td");
    oTdQUANTITY.setAttribute("align", "right");
    oTdQUANTITY.innerHTML = "<input align='right' class='form-control summ clsqty' type='number' " +
                                (iMovType == globalData.IS_PALLET || iMovType == globalData.IS_LOT || (iMovType == globalData.IS_ITEM && jsRow.oAuxItem.is_lot) ? "readonly='readonly'" : "") +
                                " placeholder='1.00' step='0.01' min='0' maxlength='15' size='2' value='" +
                                values[QUANTITY] + "'>";

    var oTdLOTS = document.createElement("td");
    oTdLOTS.innerHTML = "<button type='button' class='buttlots btn btn-info btn-md'" +
                  "data-toggle='modal' data-target='#myModal' title='Agregar lote' " + (!jsRow.oAuxItem.is_lot ? "disabled" : "") + ">" +
    "<i class='glyphicon glyphicon-list-alt'></i>" +
    "</button>";

    var oTdSTOCK = document.createElement("td");
    oTdSTOCK.innerHTML = "<button type='button' onClick='viewStock(this)' class='butstk btn btn-success btn-md' data-toggle='modal' data-target='#myStock' title='Ver existencias'>" +
    "<i class='glyphicon glyphicon-info-sign'></i>" +
    "</button>";

    var oTdDEL = document.createElement("td");
    oTdDEL.innerHTML = "<button type='button' class='removebutton btn btn-danger btn-md' title='Quitar renglón'>" +
    "<li class='glyphicon glyphicon-remove'></li>"
    "</button>";
    //
    // var oTdITEM_ID = document.createElement("td");
    // oTdITEM_ID.appendChild(document.createTextNode(values[ITEM_ID]));
    // oTdITEM_ID.setAttribute("style", "display:none;");
    // oTdITEM_ID.setAttribute("class", "id_item");
    //
    // var oTdUNIT_ID = document.createElement("td");
    // oTdUNIT_ID.appendChild(document.createTextNode(values[UNIT_ID]));
    // oTdUNIT_ID.setAttribute("style", "display:none;");
    // oTdUNIT_ID.setAttribute("class", "id_unit");
    //
    // var oTdLOC_ID = document.createElement("td");
    // oTdLOC_ID.appendChild(document.createTextNode(values[LOC_ID]));
    // oTdLOC_ID.setAttribute("style", "display:none;");
    // oTdLOC_ID.setAttribute("class", "id_loc");
    //
    // var oTdPALL_ID = document.createElement("td");
    // oTdPALL_ID.appendChild(document.createTextNode(values[PALL_ID]));
    // oTdPALL_ID.setAttribute("style", "display:none;");
    // oTdPALL_ID.setAttribute("class", "id_pall");

    oTr.appendChild(oTdFIRST);
    oTr.appendChild(oTdITEM_CODE);
    oTr.appendChild(oTdITEM_NAME);
    oTr.appendChild(oTdUNIT_CODE);
    oTr.appendChild(oTdLOCATION);
    oTr.appendChild(oTdPALLETS);
    oTr.appendChild(oTdPRICE);
    oTr.appendChild(oTdQUANTITY);
    oTr.appendChild(oTdLOTS);
    oTr.appendChild(oTdSTOCK);
    oTr.appendChild(oTdDEL);
    // oTr.appendChild(oTdITEM_ID);
    // oTr.appendChild(oTdUNIT_ID);
    // oTr.appendChild(oTdLOC_ID);
    // oTr.appendChild(oTdPALL_ID);

    tblBody.appendChild(oTr);
}

/*
* Update the values of row when the item already exists
*/
function updateRowTr(idRow, quantity) {
  document.getElementById(idRow).children[7].children[0].value = parseFloat(quantity).toFixed(globalData.DEC_QTY);
  movement.getRow(idRow).dQuantity = parseFloat(quantity);
}

/*
* This method removes a tr of table when the button of delete is pressed
* the button should have the removebutton class
*/
$(document).on('click', 'button.removebutton', function () {
    movement.removeRow($(this).closest('tr').attr('id'));
    $(this).closest('tr').remove();
    return false;
});

/**
 * [setLoc Set the value to property location of row when the combo is changed]
 * @param {[type]} value [id of location]
 * @param {[type]} obj   [the JS object]
 */
function setLoc(value, obj) {
  // $(obj).closest('tr').children('td.id_loc').html(value);
  movement.updateLocation($(obj).closest('tr').attr('id'), value);
}

/**
 * [setPall description]
 * @param {[type]} value [description]
 * @param {[type]} obj   [description]
 */
function setPall(value, obj) {
  // $(obj).closest('tr').children('td.id_pall').html(value);
  movement.updatePallet($(obj).closest('tr').attr('id'), value);
}

/**
 * [Vue object to show stock in whs movements view]
 * @type {Vue}
 */
vm = new Vue({
  el: '#app',
  data: {
    stock : {
      unit: 'NA',
      released: 0,
      segregated: 0,
      available: 0
    }
  }
})

/**
 * [viewStock set the values of stock to html table]
 */
function viewStock(obj) {
    idRow = $(obj).closest('tr').attr('id');
    var sUnit = '';
    var dReleased = 0;
    var dSegregated = 0;
    var dAvailable = 0;

    if (movement.getRow(idRow)) {
        sUnit = movement.getRow(idRow).oAuxUnit.code;
        dReleased = parseFloat(movement.getRow(idRow).aStock[2]).toFixed(globalData.DEC_QTY);
        dSegregated = parseFloat(movement.getRow(idRow).aStock[1]).toFixed(globalData.DEC_QTY);
        dAvailable = parseFloat(movement.getRow(idRow).aStock[0]).toFixed(globalData.DEC_QTY);
    }
    else {
        sUnit = '';
        dReleased = 0;
        dSegregated = 0;
        dAvailable = 0;
    }

    Vue.set(vm.stock, 'unit', sUnit);
    Vue.set(vm.stock, 'released', dReleased);
    Vue.set(vm.stock, 'segregated', dSegregated);
    Vue.set(vm.stock, 'available', dAvailable);
}
