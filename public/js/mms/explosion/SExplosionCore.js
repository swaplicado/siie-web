class SExplosionCore {
  constructor() {
     this.aFile = null;
  }

  /**
   * hide the div of production orders
   */
  hideOrders() {
    document.getElementById('div_order').style.display = 'none';
  }

  /**
   * show the div of production orders
   */
  showOrders() {
    document.getElementById('div_order').style.display = 'inline';
  }

  /**
   * hide the div of production planes
   */
  hidePlanes() {
    document.getElementById('div_plan').style.display = 'none';
  }

  /**
   * show the div of production planes
   */
  showPlanes() {
    document.getElementById('div_plan').style.display = 'inline';
  }

  /**
   * hide the div of file
   */
  hideFile() {
    document.getElementById('div_file').style.display = 'none';
  }

  /**
   * show the div of file
   */
  showFile() {
    document.getElementById('div_file').style.display = 'inline';
  }

  onChangeExplosionBy() {
    var iOption = $('input[name=explosion_by]:checked').val();

    switch (iOption) {
      case oData.scmms.EXPLOSION_BY.ORDER:
          explosionCore.hidePlanes();
          explosionCore.hideFile();
          explosionCore.showOrders();
        break;
      case oData.scmms.EXPLOSION_BY.PLAN:
          explosionCore.hideOrders();
          explosionCore.hideFile();
          explosionCore.showPlanes();
        break;
      case oData.scmms.EXPLOSION_BY.FILE:
          explosionCore.hideOrders();
          explosionCore.hidePlanes();
          explosionCore.showFile();
        break;
      default:
    }
  }

  readFile(oFile) {
    if (! oFile.name.toLowerCase().includes(".csv")) {
      swal("Error", "El archivo seleccionado no es válido.", "error");
      resetFile();
      return false;
    }

    var fReader = new FileReader();
    fReader.readAsText(oFile, "UTF-8");
    sleep(500).then(() => {
      var sResult = fReader.result;
      var aCsv = sResult.split("\n");

      var aInput = new Array();
      for (var i = 0; i < aCsv.length; i++) {
        var quantity = aCsv[i].substring(aCsv[i].indexOf(",") + 1).replace("\"", "").replace(",", "");
        var itemKey = aCsv[i].substring(0, aCsv[i].indexOf(","));

        if (parseFloat(quantity, 10) > 0 && itemKey.length > 0) {
          aInput.push(new SInputData(itemKey, parseFloat(quantity, 10)));
        }
      }

      explosionCore.aFile = aInput;
    })
  }

  setFields() {
    var selectedValues = [];
    $(".chzn-select :selected").each(function() {
      selectedValues.push($(this).attr('value'));
    });

    document.getElementById('warehouses_array').value = JSON.stringify(selectedValues);
    document.getElementById('csv_file').value = JSON.stringify(explosionCore.aFile);
  }
}

var explosionCore = new SExplosionCore();

function explosionByChange() {
  explosionCore.onChangeExplosionBy();
}

function handleFile() {
  explosionCore.readFile(document.getElementById('file').files[0]);
}

function resetFile() {
  document.getElementById("file").value = "";
}

function validateData() {
  var iOption = $('input[name=explosion_by]:checked').val();

  switch (iOption) {
    case oData.scmms.EXPLOSION_BY.ORDER:
      if (! document.getElementById('production_order').value > 0) {
        swal("Error", "Debe seleccionar una orden de producción.", "error");
        return false;
      }
      break;

    case oData.scmms.EXPLOSION_BY.PLAN:
      if (! document.getElementById('production_plan').value > 0) {
        swal("Error", "Debe seleccionar un plan de producción.", "error");
        return false;
      }
      break;

    case oData.scmms.EXPLOSION_BY.FILE:
      if (document.getElementById('file').value == "") {
        swal("Error", "Debe seleccionar un archivo CSV.", "error");
        return false;
      }
      if (explosionCore.aFile.length == 0) {
        swal("Error", "No hay nada a explosionar en el archivo.", "error");
        return false;
      }
      break;

    default:
  }

  explosionCore.setFields();

  document.getElementById("theForm").submit();
}

class SInputData {
  constructor(sItemKey, dQuantity) {
    this.sItemKey = sItemKey;
    this.dQuantity = dQuantity;
  }
}

/**
 * function sleep
 *
 * @param  {double} dTime time in milliseconds
 *
 */
async function sleepFunction(dTime) {
    await sleep(dTime);
}

function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}
