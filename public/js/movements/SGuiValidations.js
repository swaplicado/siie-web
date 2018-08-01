/**
 * this method validate the data of header
 * when the button continue is pressed
 *
 */
function validateHeader() {
    // get the values of input fields and set them to the movement object
    headerCore.setValuesToMovement(oMovement, globalData);

    // validate the data of movement header
    if (guiValidations.validateHeaderData()) {
      // if the movement does not have lines, the header can be modified
      if (oMovement.rows.size == 0) {
          guiValidations.showModify();
      }

      guiValidations.disableHeader();
      headerCore.getValuesFromServer(oMovement, globalData);
      guiValidations.hideContinue();
    }
}

/**
 * this method enable the movement header
 * and hide the panel for adition of new rows
 *
 */
function modifyHeader() {
    guiValidations.showContinue();
    guiValidations.hidePanel();
    guiValidations.enableHeader();
    guiValidations.hideModify();
    guiValidations.hideInfo();

    rowsCore.cleanAddPanel();
    if (globalData.isPalletReconfiguration) {
       reconfigCore.cleanAllPallet();
    }
}

/**
 * This class cointains methods of validation or related
 * with the Gui of user
 */
class SGuiValidations {

    /**
     * validate the data of movement header and show the message of error
     *
     * @return {boolean} true if the validations passed, else false
     */
    validateHeaderData() {
       if (oMovement.iMvtType == 0) {
          swal("Error", "Debe elegir un tipo de movimiento.", "error");
          return false;
       }
       if (oMovement.iMvtSubType == 0) {
          swal("Error", "Debe elegir un subtipo de movimiento.", "error");
          return false;
       }
       if (oMovement.tDate == null || oMovement.tDate == '') {
          swal("Error", "Debe seleccionar una fecha.", "error");
          return false;
       }

       if (oMovement.iMvtType == globalData.MVT_TP_OUT_TRA || !globalData.bIsInputMov) {
         if (oMovement.iWhsSrc == 0) {
           swal("Error", "Debe elegir un almacén origen.", "error");
           return false;
         }
       }

       if ((oMovement.iMvtType == globalData.MVT_TP_OUT_TRA || globalData.bIsInputMov)
            && !globalData.bIsExternalTransfer) {
          if (oMovement.iWhsDes == 0) {
            swal("Error", "Debe elegir un almacén destino.", "error");
            return false;
          }
       }

       if (globalData.bIsExternalTransfer) {
           if (oMovement.iBranchDes == 0) {
             swal("Error", "Debe elegir una sucursal destino.", "error");
             return false;
           }
       }

       if (globalData.iAssignType > 0) {
          switch (globalData.iAssignType) {
            case globalData.scmms.ASSIGN_TYPE.PP:
                  if (oMovement.iPODes == 0) {
                    swal("Error", "Debe elegir una orden de producción destino.", "error");
                    return false;
                  }

            case globalData.scmms.ASSIGN_TYPE.MP:
                  if (oMovement.iPOSrc == 0) {
                    swal("Error", "Debe elegir una orden de producción.", "error");
                    return false;
                  }
              break;

            default:

          }
       }

       return true;
    }

    /**
     * show the button of header modification
     */
    showModify() {
      document.getElementById('div_modify').style.display = 'inline';
    }

    /**
     * hide the button of header modification
     */
    hideModify() {
      document.getElementById('div_modify').style.display = 'none';
    }

    /**
     * disable the header of movement
     */
    disableHeader() {
        document.getElementById('dt_date').readOnly = false;
        $('#mvt_com').attr("disabled", true).trigger("chosen:updated");
        $('#whs_src').attr("disabled", true).trigger("chosen:updated");
        $('#whs_des').attr("disabled", true).trigger("chosen:updated");
        $('#branch_des').attr("disabled", true).trigger("chosen:updated");
    }

    /**
     * enable the input elements of header
     */
    enableHeader() {
        document.getElementById('dt_date').readOnly = false;
        $('#mvt_com').attr("disabled", false).trigger("chosen:updated");
        $('#whs_src').attr("disabled", false).trigger("chosen:updated");
        $('#whs_des').attr("disabled", false).trigger("chosen:updated");
        $('#branch_des').attr("disabled", false).trigger("chosen:updated");
    }

    /**
     * show the panel of addition of rows
     */
    showPanel() {
      document.getElementById('div_rows').style.display = "block";
    }

    /**
     * hide the panel of addition of movement rows
     */
    hidePanel() {
      document.getElementById('div_rows').style.display = "none";
    }

    /**
     * hide the continue button of header
     */
    hideContinue() {
      document.getElementById('div_continue').style.display = "none";
    }
    /**
     * show the continue button of header
     */
    showContinue() {
      document.getElementById('div_continue').style.display = "inline";
    }

    /**
     * hide the continue button of header
     */
    hideLocationDes() {
      document.getElementById('locss').style.display = "none";
    }

    /**
     * show the continue button of header
     */
    showLocationDes() {
      document.getElementById('locss').style.display = "inline";
    }

    /**
     * hide the label of destiny location
     */
    hideLocationDesLabel() {
      document.getElementById('loc_des_lab').style.display = "none";
    }

    /**
     * show the label of destiny location
     */
    showLocationDesLabel() {
      document.getElementById('loc_des_lab').style.display = "inline";
    }

    showSearchButton() {
      document.getElementById('div_search_button').style.display = "block";
    }

    hideSearchButton() {
      document.getElementById('div_search_button').style.display = "none";
    }

    /**
     * hide the lots button
     */
    hideLots() {
      document.getElementById('div_lots').style.display = "none";
    }

    /**
     * show the lots button
     */
    showLots() {
      document.getElementById('div_lots').style.display = "inline";
    }

    /**
     * hide the button of pallets
     */
    hidePallet() {
      document.getElementById('div_pallets').style.display = "none";
    }

    /**
     * show the pallets button
     */
    showPallet() {
      document.getElementById('div_pallets').style.display = "inline";
    }

    /**
     * hide the add button (to add new movement row)
     */
    hideAdd() {
      document.getElementById('div_add').style.display = "none";
    }

    /**
     * show the add button (to add new movement row)
     */
    showAdd() {
      document.getElementById('div_add').style.display = "inline";
    }

    /**
     * hide the add button (to add new movement row)
     */
    hideInfo() {
      document.getElementById('info_div').style.display = "none";
    }

    /**
     * show the add button (to add new movement row)
     */
    showInfo() {
      document.getElementById('info_div').style.display = "inline";
    }

    /**
     * hide the button to delete movement rows
     */
    hideDelete() {
      if (document.getElementById('div_delete') != null) {
        document.getElementById('div_delete').style.display = "none";
      }
    }

    /**
     * show the button to delete movement rows
     */
    showDelete() {
      if (document.getElementById('div_delete') != null) {
        document.getElementById('div_delete').style.display = "inline";
      }
    }

    showFreeze() {
        document.getElementById('idFreeze').style.display = "inline";
    }

    hideFreeze() {
        document.getElementById('idFreeze').style.display = "none";
    }

    /**
     * enable or disable the input of price
     * enable: input movement
     * disable: output movement
     */
    validatePrice() {
      if (! globalData.bIsInputMov) {
         guiFunctions.setPrice(0);
         document.getElementById('price').readOnly = true;
      }
      else {
        guiFunctions.setPrice(0);
        document.getElementById('price').readOnly = false;
      }
    }

    /**
     * set the text to location label on the principal view
     *
     * @param {string} sText
     */
    setLocationLabel(sText) {
      document.getElementById('label_loc').innerText = sText;
    }

    /**
     * set the text to destiny location label on the principal view
     *
     * @param {string} sText
     */
    setLocationDesLabel(sText) {
      document.getElementById('label_loc_des').innerText = sText;
    }

    /**
     * set a text to input of locations search
     *
     * @param {string} sText
     */
    setSearchLocationText(sText) {
      document.getElementById('location').value = sText;
    }

    /**
     * set a text to input of destiny locations search
     *
     * @param {string} sText
     */
    setSearchLocationDesText(sText) {
      document.getElementById('location_des').value = sText;
    }

    /**
     * set the text to item selected on principal view
     *
     * @param {string} sText
     */
    setItemLabel(sText) {
      document.getElementById('label_sel').innerText = sText;
    }

    /**
     * set the text to unit label on principal view
     *
     * @param {string} sText
     */
    setUnitLabel(sText) {
      document.getElementById('label_unit').innerText = sText;
    }

    /**
     * validate the input for add a new row
     *
     * @return {boolean} true if the validation passed successfully
     */
    validateInputRow() {
      if (oLocation == null) {
          swal("Error", "Debe seleccionar una ubicación.", "error");
          return false;
      }

      if (globalData.iMvtType == globalData.MVT_TP_OUT_TRA && oLocationDes == null) {
        swal("Error", "Cuando realiza un traspeso debe seleccionar una ubicación destino.", "error");
        return false;
      }

      if (guiFunctions.getSearchCode() == '' &&
            elementToAdd == null) {
          swal("Error", "Debe seleccionar un elemento para agregar o introducir " +
                          "un código de material/producto.", "error");
          return false;
      }

      return true;
    }

    /**
     * validate the quantity and price of row before to be added to the movement
     * if there is a error shows it to the user and returns false
     *
     * @return {boolean}
     */
    validateQtyPrice() {
      if (guiFunctions.getQuantity() <= 0) {
          swal("Error", "La cantidad debe ser mayor a cero.", "error");
          return false;
      }

      if (globalData.bIsInputMov
            && !globalData.isPalletReconfiguration
              && guiFunctions.getPrice() <= 0) {
          swal("Error", "El precio debe ser mayor a cero.", "error");
          return false;
      }
    }

    disableQuantity() {
       document.getElementById('quantity').disabled = true;
    }

    enableQuantity() {
       document.getElementById('quantity').disabled = false;
    }


    /**
     * determine if the value correspond to float number (with decimals)
     * 1.45 return true
     * 1 return false
     * 1.0 return false
     * 'hi' return false
     *
     * @param  {text}  value
     * @return {Boolean}
     */
    isNumberFloat(value) {
      return isFloat(parseFloat(value));
    }

}

var guiValidations = new SGuiValidations();

/**
 * function sleep
 *
 * @param  {double} dTime time in milliseconds
 *
 */
async function sleepFunction(dTime) {
    await sleep(dTime);
}

/**
 * go to previous page after sleep
 */
async function goToBack() {
  // await sleep(1000);
  window.history.back();
}

/**
 * pause the application the time received (ms)
 * @param  {integer} ms milliseconds
 * @return {Promise}
 */
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
