class SGuiFunctions {

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
      try {
        document.getElementById('item').value = sText;
      }
      catch (e) {
        console.log(e);
      }
    }

    /**
     * get the value of input of item searched
     *
     * @return {string} text searched
     */
    getSearchCode() {
      return document.getElementById('item').value;
    }

    /**
     * set the value to lots quantity input formatted with the decimals configured
     * in the company's configuration
     * this field is the quantity of row set by the user
     *
     * @param {double} dQty value to be set in input
     */
    setLotsQuantityLabel(dQty) {
      document.getElementById('lots_quantity').innerText = parseFloat(dQty, 10).
                                                    toFixed(globalData.DEC_QTY);
    }

    /**
     * set the value to lots accumulated quantity input formatted with the decimals configured
     * in the company's configuration
     * this field is the sum of quantities of  of row set by the user
     *
     * @param {double} dQty value to be set in input
     */
    setAccumQuantityLabel(dQty) {
      document.getElementById('accum_quantity').innerText = parseFloat(dQty, 10).
                                                    toFixed(globalData.DEC_QTY);
    }

    /**
     * set the text to input of searching lot
     *
     * @param {string} sText text to be set in input
     */
    setSearchLot(sText) {
      document.getElementById('search_lot').value = sText;
    }

    /**
     * get the value of input of lot searched
     *
     * @return {string} text searched
     */
    getSearchLot() {
      return document.getElementById('search_lot').value;
    }

    /**
     * set the text to input of lot
     *
     * @param {string} sText text to be set in input
     */
    setTextLot(sText) {
      document.getElementById('lot').value = sText;
    }

    /**
     * get the value of input of lot on row to add
     *
     * @return {string} entered text
     */
    getTextLot() {
      return document.getElementById('lot').value;
    }

    /**
     * set the string date to date input
     *
     * @param {string} sDate date in format text to be set to the input
     */
    setExpDateLot(sDate) {
      document.getElementById('exp_date').value = sDate;
    }

    /**
     * get the value of input of lot expiry date on row to add
     *
     * @return {string} entered
     */
    getExpDateLot() {
      return document.getElementById('exp_date').value;
    }

    /**
     * set the value to quantity of lot input formatted with the decimals configured
     * in the company's configuration
     *
     * @param {double} dQty value to be set in input
     */
    setQuantityLot(dQty) {
      document.getElementById('quantity_lot').value = parseFloat(dQty, 10).
                                                    toFixed(globalData.DEC_QTY);
    }

    /**
     * get the value of input of lot quantity on row to add
     *
     * @return {double} lot quantity
     */
    getQuantityLot() {
      return parseFloat(document.getElementById('quantity_lot').value, 10);
    }

    /**
     * set the value to input element
     * @param {[type]} bChecked [description]
     */
    setCreateLot(bChecked) {
      document.getElementById('is_lot_new').checked = bChecked;
    }

    /**
     * get the boolean value of input checkbox
     * (only appear when the configuration is enabled)
     *
     * @return {boolean} create
     */
    getCreateLot() {
      return document.getElementById('is_lot_new').checked;
    }

    setSearchPallet(sText) {
      document.getElementById('search_pallet').value = sText;
    }

    /**
     * get the value of input of pallet to be searched
     *
     * @return {string} text searched
     */
    getSearchPallet() {
      return document.getElementById('search_pallet').value;
    }

    /**
     * set the name of item of pallet to label
     *
     * @param {string} sText name of pallet's item to be set on label
     */
    setPalletItemLabel(sText) {
      document.getElementById('item_pallet').innerText = sText;
    }

    /**
     * set the code of unit assigned to current item
     *
     * @param {string} sText
     */
    setPalletUnitLabel(sText) {
      document.getElementById('unit_pallet').innerText = sText;
    }

    /**
     * set the name of pallet assigned to row
     *
     * @param {string} sText
     */
    setPalletNameLabel(sText) {
      document.getElementById('name_pallet').innerText = sText;
    }

    /**
     *  change the class secondary to success of element
     *  with the id received
     *
     * @param  {string} sIdElement id of element
     */
    changeClassToSuccess(sIdElement) {
      var oElement = document.getElementById(sIdElement);
      removeClass(oElement, "btn-secondary");
      addClass(oElement, "btn-success");
    }

    /**
     * change the class success to secondary of element
     *
     * @param  {string} sIdElement id of element to be changed
     */
    changeClassToSecondary(sIdElement) {
      var oElement = document.getElementById(sIdElement);
      removeClass(oElement, "btn-success");
      addClass(oElement, "btn-secondary");
    }

    getStockAlert(sItemCode, sItem, sUnit, dQuantity, oStockRow) {
        return {
                title: 'Error',
                type: 'error',
                showConfirmButton: true,
                html:
                '<p>No hay existencias disponibles del material/producto:</p> ' +
                '<p>' + sItemCode + ' ' + sItem + ' ' + sUnit + ' :</p> ' +
                '<div class="row"> ' +
                   '<div class="columnn left"> ' +
                      '<p>Existencias:</p> ' +
                      '<p>Segregado:</p> ' +
                      '<p>Disponible:</p> ' +
                      '<p>Entradas movimiento:</p> ' +
                      '<p>Salidas movimiento:</p> ' +
                      '<b>Disponible neto:</b> ' +
                      '<br>' +
                      '<br>' +
                      '<b style="color: red;">Solicitado:</b> ' +
                   '</div> ' +
                   '<div class="columnn right"> ' +
                      '<p>  ' + parseFloat(oStockRow.stock, 10).toFixed(globalData.DEC_QTY) + '</p> ' +
                      '<p>- ' + parseFloat(oStockRow.segregated, 10).toFixed(globalData.DEC_QTY) + '</p> ' +
                      '<p>= ' + parseFloat(oStockRow.available_stock, 10).toFixed(globalData.DEC_QTY) + '</p> ' +
                      '<p>+ ' + parseFloat(oStockRow.dInput, 10).toFixed(globalData.DEC_QTY) + '</p> ' +
                      '<p>- ' + parseFloat(oStockRow.dOutput, 10).toFixed(globalData.DEC_QTY) + '</p> ' +
                      '<b>= ' + parseFloat((oStockRow.available_stock
                                 + oStockRow.dInput
                                 - oStockRow.dOutput), 10).toFixed(globalData.DEC_QTY) + '</b> ' +
                      '<br>' +
                      '<br>' +
                      '<b style="color: red;">' + parseFloat(dQuantity, 10).toFixed(globalData.DEC_QTY) + '</b> ' +
                   '</div> ' +
               '</div> ' +
        ''};
    }
}

guiFunctions = new SGuiFunctions();