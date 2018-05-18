var lLotsToCreate = null;

class SGuiTransfers {
  constructor() {

  }

  setElementToPanel(oMovementRow) {
    guiTransfers.setItemLabel(oMovementRow.sItemCode + '-' + oMovementRow.sItem);
    guiTransfers.setUnitLabel(oMovementRow.sUnit);
    guiTransfers.setPalletLabel(oMovementRow.sPallet);
  }

  disableHeader() {
    document.getElementById('dt_date').readOnly = true;
    $('#whs_id').attr("disabled", true).trigger("chosen:updated");
  }

  enableHeader() {
    document.getElementById('dt_date').readOnly = false;
    $('#whs_id').attr("disabled", false).trigger("chosen:updated");
  }

  hideContinue() {
    document.getElementById('div_continue').style.display = 'none';
  }

  showContinue() {
    document.getElementById('div_continue').style.display = 'block';
  }

  showSearchPanel() {
    document.getElementById('div_search').style.display = 'block';
  }

  hideSearchPanel() {
    document.getElementById('div_search').style.display = 'none';
  }

  showTablePanel() {
    document.getElementById('div_table').style.display = 'block';
  }

  showButtonReceive() {
    document.getElementById('btn_receive').style.display = 'block';
  }

  hideButtonReceive() {
    document.getElementById('btn_receive').style.display = 'none';
  }

  showDelete() {
    document.getElementById('div_delete').style.display = 'block';
  }

  hideDelete() {
    document.getElementById('div_delete').style.display = 'none';
  }

  setQtyReadOnly(bReadOnly) {
    document.getElementById('quantity').readOnly = bReadOnly;
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
   * set the value to quantity input formatted with the decimals configured
   * in the company's configuration
   *
   * @param {double} dQty value to be set in input
   */
  setQuantity(dQty) {
    document.getElementById('quantity').value = parseFloat(dQty, 10).
    toFixed(globalData.DEC_QTY);
  }

  /**
   * get the value of input quantity parsed to float
   *
   * @return {double} quantity
   */
  getQuantity() {
    return parseFloat(document.getElementById('quantity').value, 10);
  }


  /**
   * set the value to price input formatted with the decimals configured
   * in the company's configuration
   *
   * @param {double} dPrice value to be set in input
   */
  setPrice(dPrice) {
    document.getElementById('price').value = parseFloat(dPrice, 10).
    toFixed(globalData.DEC_AMT);
  }

  /**
   * get the value of input price parsed to float
   *
   * @return {double} price
   */
  getPrice() {
    return parseFloat(document.getElementById('price').value, 10);
  }

  /**
   * set the text to input of searching code
   *
   * @param {string} sText text to be set in input
   */
  setSearchCode(sText) {
      if (document.getElementById('element') != null) {
          document.getElementById('element').value = sText;
      }
  }

  /**
   * get the value of input of item searched
   *
   * @return {string} text searched
   */
  getSearchCode() {
    return document.getElementById('element').value;
  }

  /**
   * set the text to pallet selected on principal view
   *
   * @param {string} sText
   */
  setPalletLabel(sText) {
    document.getElementById('label_pallet').innerText = sText;
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

  cleanEntryPanel() {
    externalTransfers.oElement = null;
    externalTransfers.oMovRow = null;
    externalTransfers.iAuxIndex = -1;

    guiTransfers.setItemLabel('--');
    guiTransfers.setQuantity(0);
    guiTransfers.setUnitLabel('--');
    guiTransfers.setPrice(0);
    guiTransfers.setPalletLabel('--');

    oLotsAddTable.clear().draw();
  }

  addElementToTable(elementToAdd) {
    oTransfersMovsTable.row.add([
        elementToAdd.iIdRow,
        elementToAdd.sItemCode,
        elementToAdd.sItem,
        elementToAdd.sUnit,
        elementToAdd.sLocation,
        elementToAdd.sLocationDes,
        elementToAdd.sPallet,
        parseFloat(elementToAdd.dPrice, 10).toFixed(globalData.DEC_AMT),
        parseFloat(elementToAdd.dQuantity, 10).toFixed(globalData.DEC_QTY),
        elementToAdd.bIsLot ? guiTransfers.getLotsButton(elementToAdd.iIdRow) : '-',
        '-'
    ]).draw( false );
  }

  getLotsButton(id) {
    return  "<button type='button' onClick='viewLots(" + id + ")' " +
                    "class='butstk btn btn-primary btn-md' " +
                    "title='Ver lotes'>" +
                "<i class='glyphicon glyphicon-info-sign'></i>" +
            "</button>"
  }

}

var guiTransfers = new SGuiTransfers();

/*
* When freeze is pressed, the field of item, quantity and the button
* of add are disabled, but the data of the movement is send to server too
*/
function unfreezeTrans() {
  var fre = document.getElementById("idFreeze"); // freeze button
  var sBut = document.getElementById("saveButton"); // save button

  if (fre.firstChild.data == "Congelar") {
    lLotsToCreate = new Array();
    if (validation.validateMovement(oMovement)) {
        sBut.disabled = false;
        setMovementToForm();

        guiTransfers.hideSearchPanel();
        guiTransfers.hideButtonReceive();
        guiTransfers.hideDelete();

        fre.innerHTML = "Descongelar";
    }
  }
  else {
    sBut.disabled = true;

    guiTransfers.showSearchPanel();
    guiTransfers.showButtonReceive();
    guiTransfers.showDelete();

    fre.innerHTML = "Congelar";
  }
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
