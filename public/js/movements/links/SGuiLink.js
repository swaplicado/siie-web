class SGuiLink {

  setQuantity(dQty) {
    document.getElementById('quantity').value = parseFloat(dQty, 10).
    toFixed(globalData.DEC_QTY);
  }

  getQuantity() {
    return parseFloat((document.getElementById('quantity').value), 10);
  }

  setItem(sText) {
    document.getElementById('item').value = sText.toUpperCase();
  }

  setUnit(sText) {
    document.getElementById('unit').value = sText;
  }

  disableAcceptButton() {
    document.getElementById('accepLots').disabled = true;
  }

  enableAcceptButton() {
    document.getElementById('accepLots').disabled = false;
  }

  hideActions() {
    document.getElementById('div_actions').style.display = 'none';
  }

  showActions() {
    document.getElementById('div_actions').style.display = 'inline';
  }

  /*
  * When freeze is pressed, the field of item, quantity and the button
  * of add are disabled, but the data of the movement is send to server too
  */
  unfreeze() {
    var fre = document.getElementById("idFreezeMov"); // freeze button
    var sBut = document.getElementById("saveMovButton"); // save button

    if (fre.value == "Congelar") {
      if (true) {
          sBut.disabled = false;
          linksCore.setMovementsToForm();

          fre.value = "Descongelar";
      }
    }
    else {
      sBut.disabled = true;
      fre.value = "Congelar";
    }
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

var guiLink = new SGuiLink();

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
