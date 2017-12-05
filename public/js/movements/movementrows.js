/**
 * 0 = isn't pallet reconfiguration
 * 1 = the pallet was set
 * 2 = the first not is pallet
 * 3 = normal process after pallet set
 *
 * @type {Number} iPalletReconfig
 */
var iPalletReconfig = 0;
var palletMovement = new SMovement();

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
  var iWhsSrc = 0;
  var oWarehouseSrc = null;
  var iWhsDes = 0;
  var oWarehouseDes = null
  if (globalData.bIsInputMov) {
    iWhsId = document.getElementById("whs_des").value;
  }
  else {
    iWhsId = document.getElementById("whs_src").value;
    whsStock = iWhsId;
  }

  if (document.getElementById("whs_src") !== undefined && document.getElementById("whs_src") != null) {
      iWhsSrc = document.getElementById("whs_src").value;
  }
  if (document.getElementById("whs_des") !== undefined && document.getElementById("whs_des") != null) {
      iWhsDes = document.getElementById("whs_des").value;
  }

  document.getElementById('item').value = '';

  globalData.lWarehouses.forEach(function(whs) {
    if (iWhsSrc != 0 && whs.id_whs == iWhsSrc) {
        oWarehouseSrc = whs;
    }
    if (iWhsDes != 0 && whs.id_whs == iWhsDes) {
        oWarehouseDes = whs;
    }
  });

  //ajax
  $.get('./create/children?parent=' + sItemCode + '&whs=' + whsStock + '&idCls=' + globalData.iMvtClass,
   function(data) { // executes the method children of controller
      // data is the "data" that the controller returns
      $('.dataTables_empty').remove(); // remove the row with the class: dataTables_empty
      var bItemFound = false;
      var bIsItemFromWhs = true;
      var bisItemContainerValid = true;

      $.each(data, function(index, oMovementRow) { // iterate each of the movements
        bItemFound = true;
        bFound = false;
        iRowId = 0;

        if (oWarehouseDes != null && !validateItemWarehouseType(oWarehouseDes, oMovementRow.item)) {
            bIsItemFromWhs = false;
            return true;
        }

        if (oWarehouseDes != null && !validateContainer(oWarehouseDes, oMovementRow.item, globalData.lItemContainers)) {
            bisItemContainerValid = false;
            return true;
        }

        var iMovType = 0;
        if (oMovementRow.aux_lot_id > 0) {
          iMovType = globalData.IS_LOT;
          idLot = oMovementRow.aux_lot_id;
        }
        else if (oMovementRow.pallet_id > 1) {
          iMovType = globalData.IS_PALLET;
        }
        else {
          iMovType = globalData.IS_ITEM;
        }

        // Pallets reconfiguration
        if (globalData.isPalletReconfiguration &&
                (iPalletReconfig == 0 || iPalletReconfig == 2)) {
            if (iMovType == globalData.IS_PALLET) {
                iPalletReconfig = 1;
            }
            else {
                iPalletReconfig = 2;
                return false;
            }
        }

        var ojsMovRow = new SMovementRow(movement.rowIdentifier);

        ojsMovRow.iItemId = oMovementRow.item_id;
        ojsMovRow.iUnitId = oMovementRow.unit_id;
        ojsMovRow.iPalletId = oMovementRow.pallet_id;
        ojsMovRow.iLocationId = oMovementRow.location_id;
        ojsMovRow.oAuxItem = oMovementRow.item;
        ojsMovRow.oAuxUnit = oMovementRow.unit;
        ojsMovRow.oAuxPallet = oMovementRow.pallet;
        ojsMovRow.oAuxLocation = oMovementRow.location;
        ojsMovRow.aStock = oMovementRow.aux_stock;

        // If the definition of the platform has an assigned amount, this is set, if the user's input is not set
        ojsMovRow.dQuantity = oMovementRow.quantity > 0 ? parseFloat(oMovementRow.quantity) * dInputQuantity : dInputQuantity;
        ojsMovRow.dPrice = oMovementRow.amount_unit >= 0 ? parseFloat(oMovementRow.amount_unit) : 0;

        //pallets reconfiguration
        if (iPalletReconfig == 3) {
           if (!palletValidation(ojsMovRow, iWhsId, iMovType == globalData.IS_LOT ? oMovementRow.aux_lot_id : 0, iWmsMvtType)) {
                return false;
           }
           if (iMovType == globalData.IS_PALLET && iWmsMvtType == globalData.PALLET_RECONFIG_IN) {
              ojsMovRow.dQuantity = 0;
           }
        }

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
          rowTrId = iRowId;
          if (iWmsMvtType != globalData.PALLET_RECONFIG_IN || iMovType != globalData.IS_PALLET) {
            updateRowTr(iRowId, movement.getRow(iRowId).dQuantity);
          }
        }
        else {
          var idRow = movement.rowIdentifier; // gets the identifier of current row
          rowTrId = movement.rowIdentifier;
          movement.addRow(ojsMovRow);

          if (!globalData.isPalletReconfiguration || iPalletReconfig == 3) {
            addRowTr(idRow, ojsMovRow, iWhsId, iMovType, iWmsMvtType);
          }
        }

        //this code adds the lots to rows
        if (iMovType == globalData.IS_LOT) {
            if (iPalletReconfig == 1 || iMovType == globalData.IS_LOT) {
              // If the movement row is of lot, add a new lot
              addOrUpdateLotRow(rowTrId, idLot, ojsMovRow.dQuantity, ojsMovRow.dPrice);
            }
        }
        else if (iMovType == globalData.IS_PALLET) {
          // if the movement row is of a pallet, add the lots with stock in pallet
          if (iPalletReconfig == 0 || iPalletReconfig == 1 || iMovType == globalData.IS_LOT || (iWmsMvtType == globalData.PALLET_RECONFIG_OUT && iPalletReconfig == 3)) {
              oMovementRow.aux_lots.forEach(function(lot) {
                  addOrUpdateLotRow(rowTrId, parseInt(lot.lot_id), parseFloat(lot.quantity), parseFloat(lot.amount_unit));
              });
          }
        }

        if (iPalletReconfig == 1) {
            palletRow = movement.rows.pop()
            iPalletReconfig = 3;
            document.getElementById('quantity').disabled = false;
            updatePallet(palletRow);
            oReconfigurationMov = new SMovement()
        }
      });

      if (! bItemFound) {
        // alert("No se encontraron resultados");
        swal( "???", "No se encontraron resultados.", "warning");
      }
      if (! bisItemContainerValid) {
        // alert("No se encontraron resultados");
        swal( "Error", "El almacén destino no está configurado para recibir este material/producto.", "error");
      }
      if (globalData.isPalletReconfiguration &&
              iPalletReconfig == 2) {
        // alert("Debe de elegir primero una tarima");
        swal("Error", "Debe de elegir primero una tarima.", "error");
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

    var values = [ //this is the array of values for the row
                identifier,
                jsRow.oAuxItem.code,
                jsRow.oAuxItem.name,
                jsRow.oAuxUnit.code,
                "",
                "  Lotes",
                "",
                parseFloat(jsRow.dPrice).toFixed(globalData.DEC_AMT),
                parseFloat(iWmsMvtType == globalData.PALLET_RECONFIG_IN && iMovType == globalData.IS_PALLET ? 1 : jsRow.dQuantity).toFixed(globalData.DEC_QTY),
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
    oTdPALLETS.innerHTML = "<select " + (iWmsMvtType == globalData.MVT_TP_OUT_TRA || globalData.isPalletReconfiguration ? "disabled='true'" : "")  +
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
    oTdQUANTITY.innerHTML = "<input align='right' onKeyUp='updateQtyKey(" + identifier + ")' class='form-control' type='number' " +
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

    tblBody.appendChild(oTr);
}

/*
* Update the values of row when the item already exists
*/
function updateRowTr(idRow, quantity) {
  document.getElementById(idRow).children[7].children[0].value = parseFloat(quantity).toFixed(globalData.DEC_QTY);
  movement.getRow(idRow).dQuantity = parseFloat(quantity);
}

function updateQtyKey(id) {
    updateRowTr(id, document.getElementById(id).children[7].children[0].value)
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

function validateItemWarehouseType(oWarehouse, item) {
  isValid = false;
  var CLASS_MATERIAL = 1;
  var CLASS_PRODUCT = 2;
  var CLASS_SPENDING = 3;

  var  WHS_TYPE_NA = 1;
  var  WHS_TYPE_MATERIAL = 2;
  var  WHS_TYPE_PRODUCTION = 3;
  var  WHS_TYPE_PRODUCT = 4;

  /**
   *  Item classes
   *  1	MATERIAL
   *  2	PRODUCT
   *  3	SPENDING
   */
  /**
   *  Whs types
   *  1 N/A
   *  2 MATERIAL
   *  3 PRODUCTION
   *  4 PRODUCT
   */


    switch (oWarehouse.whs_type.id_whs_type) {
      case WHS_TYPE_NA:
              isValid = true;
              return true;
        break;
      case WHS_TYPE_MATERIAL:
              if (item.gender.item_class.id_item_class == CLASS_MATERIAL) {
                isValid = true;
                return true;
              }
        break;
      case WHS_TYPE_PRODUCTION:
              if (item.gender.item_class.id_item_class == CLASS_PRODUCT || item.gender.item_class.id_item_class == CLASS_MATERIAL) {
                isValid = true;
                return true;
              }
        break;
      case WHS_TYPE_PRODUCT:
              if (item.gender.item_class.id_item_class == CLASS_PRODUCT) {
                isValid = true;
                return true;
              }
        break;
      default:

    }


  if (!isValid) {
      // alert("No puede ingresar este material/producto en este almacén");
      swal("Error", "No puede ingresar este material/producto en este almacén.", "error");
  }

  return isValid;
}
