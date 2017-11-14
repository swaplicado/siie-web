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

  var iWhsId = 0;
  if (globalData.bIsInputMov) {
    iWhsId = document.getElementById("whs_des").value;
  }
  else {
    iWhsId = document.getElementById("whs_src").value;
  }

  //ajax
  $.get('./create/children?parent=' + sItemCode, function(data) { // executes the method children of controller
      // data is the "data" that the controller returns
      $('.dataTables_empty').remove(); // remove the row with the class: dataTables_empty
      var bItemFound = false;

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
        else if (oMovementRow.pallet_id > 0) {
          iMovType = globalData.IS_PALLET;
          console.log(oMovementRow);
        }
        else {
          iMovType = globalData.IS_ITEM;
        }

        var ojsMovRow = new SMovementRow();

        ojsMovRow.iItemId = oMovementRow.item_id;
        ojsMovRow.iUnitId = oMovementRow.unit_id;
        ojsMovRow.bAuxIsLot = oMovementRow.item.is_lot;
        ojsMovRow.bAuxIsBulk = oMovementRow.item.is_bulk;
        ojsMovRow.sAuxItemCode = oMovementRow.item.code;

        // If the definition of the platform has an assigned amount, this is set, if the user's input is not set
        ojsMovRow.dQuantity = oMovementRow.quantity > 0 ? parseFloat(oMovementRow.quantity) * dInputQuantity: dInputQuantity;
        ojsMovRow.dPrice = oMovementRow.amount_unit >= 0 ? parseFloat(oMovementRow.amount_unit) : 0;

        // checks if the pallet was selected before
        movement.rows.forEach(function(element) {
            if (element.iItemId == oMovementRow.item_id && element.iUnitId == oMovementRow.unit_id
                && element.iPalletId == oMovementRow.pallet_id) {
                bFound = true;
                iRowId = element.identifier;
                // break;
            }
        });

        var rowTrId = 0;
        // if pallet was selected previously
        if (bFound) {
          movement.rows[iRowId].dQuantity += ojsMovRow.dQuantity;
          updateRowTr(iRowId, movement.rows[iRowId].dQuantity);

          rowTrId = iRowId;
        }
        else {
          var idRow = movement.rowIdentifier; // gets the identifier of current row

          addRowTr(idRow, ojsMovRow, oMovementRow, iWhsId, iMovType);

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
          oMovementRow.auxLots.forEach(function(lot) {
              addOrUpdateLotRow(rowTrId, lot.lot_id, lot.quantity, lot.amount_unit);
          });
        }
      });

      // if (! bItemFound) {
      //   alert("No se encontraron resultados");
      // }
    });
}

/*
* Add a new Row on HTML table
*/
function addRowTr(identifier, jsRow, movRow, iWhsId, iMovType) {
      // Pallets select
      var bPallSet = false;
      var iDefaultPallet = 1;
      var optionsPall = "";

      globalData.lPallets.forEach(function(element) {
        if ((element.item_id == movRow.item_id && element.unit_id == movRow.unit_id) ||
                element.id_pallet == 1) {
            if (!bPallSet) {
              iDefaultPallet = element.id_pallet;
            }
            bPallSet = true;
            optionsPall += "<option value=" + element.id_pallet +
                              " " + (iMovType == globalData.IS_PALLET && movRow.pallet_id == element.id_pallet ? "selected" : "") + ">" +
                                  element.pallet +
                            "</option>";
        }
      });
      jsRow.iPalletId = iMovType == globalData.IS_PALLET ? movRow.pallet_id : iDefaultPallet;

      //Locations
      var bLocSet = false;
      var iDefaultLocation = 0;
      var locationOptions = "";

      globalData.lLocations.forEach(function(element) {
          if (element.whs_id == iWhsId || element.id_whs_location == 1) {
              if (!bLocSet) {
                iDefaultLocation = element.id_whs_location;
              }
              bLocSet = true;
              locationOptions += "<option " +
                                      "value=" + element.id_whs_location + ">" +
                                      element.code + " - " + element.name +
                                  "</option>";
          }
      });

      if (globalData.bIsInputMov || ( !globalData.bIsInputMov && movRow.location_id <= 0)) {
        jsRow.iLocationId = iDefaultLocation;
      }
      else {
        jsRow.iLocationId = movRow.location_id;
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
    const ITEM_ID = 10;
    const UNIT_ID = 11;
    const LOC_ID = 12;
    const PALL_ID = 13;

    var values = [ //this is the array of values for the row
                identifier,
                movRow.item.code,
                movRow.item.name,
                movRow.unit.code,
                "",
                "  Lotes",
                "",
                parseFloat(jsRow.dPrice).toFixed(2),
                parseFloat(jsRow.dQuantity).toFixed(5),
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
    oTdLOCATION.innerHTML = "<select onChange='setLoc(this.value, this)' class='form-control'>" + locationOptions + "</select>";

    var oTdLOTS = document.createElement("td");
    oTdLOTS.innerHTML = "<button type='button' class='buttlots btn btn-info btn-xs' data-toggle='modal' data-target='#myModal' title='Agregar lote'>" +
    "<i class='glyphicon glyphicon-list-alt'></i>" +
    "</button>";

    var oTdPALLETS = document.createElement("td");
    oTdPALLETS.setAttribute("align", "center");
    oTdPALLETS.innerHTML = "<select onChange='setPall(this.value, this)' class='form-control'>" + optionsPall + "</select>";

    var oTdPRICE = document.createElement("td");
    oTdPRICE.appendChild(document.createTextNode(values[PRICE]));
    oTdPRICE.setAttribute("align", "right");
    oTdPRICE.setAttribute("contenteditable", "true");

    var oTdQUANTITY = document.createElement("td");
    oTdQUANTITY.appendChild(document.createTextNode(values[QUANTITY]));
    oTdQUANTITY.setAttribute("class", "summ clsqty");
    oTdQUANTITY.setAttribute("align", "right");
    oTdQUANTITY.setAttribute("contenteditable", "true");

    var oTdDEL = document.createElement("td");
    oTdDEL.innerHTML = "<button type='button' class='removebutton btn btn-danger btn-xs' title='Quitar renglón'>" +
    "<li class='glyphicon glyphicon-remove'></li>"
    "</button>";

    var oTdITEM_ID = document.createElement("td");
    oTdITEM_ID.appendChild(document.createTextNode(values[ITEM_ID]));
    oTdITEM_ID.setAttribute("style", "display:none;");
    oTdITEM_ID.setAttribute("class", "id_item");

    var oTdUNIT_ID = document.createElement("td");
    oTdUNIT_ID.appendChild(document.createTextNode(values[UNIT_ID]));
    oTdUNIT_ID.setAttribute("style", "display:none;");
    oTdUNIT_ID.setAttribute("class", "id_unit");

    var oTdLOC_ID = document.createElement("td");
    oTdLOC_ID.appendChild(document.createTextNode(values[LOC_ID]));
    oTdLOC_ID.setAttribute("style", "display:none;");
    oTdLOC_ID.setAttribute("class", "id_loc");

    var oTdPALL_ID = document.createElement("td");
    oTdPALL_ID.appendChild(document.createTextNode(values[PALL_ID]));
    oTdPALL_ID.setAttribute("style", "display:none;");
    oTdPALL_ID.setAttribute("class", "id_pall");

    oTr.appendChild(oTdFIRST);
    oTr.appendChild(oTdITEM_CODE);
    oTr.appendChild(oTdITEM_NAME);
    oTr.appendChild(oTdUNIT_CODE);
    oTr.appendChild(oTdLOCATION);
    oTr.appendChild(oTdLOTS);
    oTr.appendChild(oTdPALLETS);
    oTr.appendChild(oTdPRICE);
    oTr.appendChild(oTdQUANTITY);
    oTr.appendChild(oTdDEL);
    oTr.appendChild(oTdITEM_ID);
    oTr.appendChild(oTdUNIT_ID);
    oTr.appendChild(oTdLOC_ID);
    oTr.appendChild(oTdPALL_ID);

    tblBody.appendChild(oTr);
}

/*
* Update the values of row when the item already exists
*/
function updateRowTr(idRow, quantity) {
  document.getElementById(idRow).children[8].innerHTML = quantity.toFixed(8);
}

/*
* This method removes a tr of table when the button of delete is pressed
* the button should have the removebutton class
*/
$(document).on('click', 'button.removebutton', function () {
    // datas.lotRows.splice($(this).closest('tr').attr('id'), 1);
    // datas.lotTables.splice($(this).closest('tr').attr('id'), 1);
    movement.removeRow($(this).closest('tr').attr('id'));
    $(this).closest('tr').remove();
    return false;
});

function setLoc(value, obj) {
  $(obj).closest('tr').children('td.id_loc').html(value);
  movement.rows[$(obj).closest('tr').attr('id')].iLocationId = value;
}

function setPall(value, obj) {
  $(obj).closest('tr').children('td.id_pall').html(value);
  movement.rows[$(obj).closest('tr').attr('id')].iPalletId = value;
}
