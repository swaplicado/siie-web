class SRotation {
  constructor() {

  }

  validateRotation(elementToAdd) {
    var mLots = new Map();

    if (globalData.iMvtType != globalData.MVT_TP_OUT_SAL) {
        return true;
    }

    for (var [keyRow, oRow] of oMovement.rows) {
      if (oRow.iItemId == elementToAdd.iItemId && oRow.iUnitId == elementToAdd.iUnitId) {
        for (var [keyLot, oLotRow] of oRow.lotRows) {
            if (! mLots.has(oLotRow.iLotId)) {
              mLots.set(oLotRow.iLotId, oLotRow);
            }
            else {
              var oAux = mLots.get(oLotRow.iLotId);
              oAux.dQuantity += parseFloat(oLotRow.dQuantity, 10)
              mLots.set(oLotRow.iLotId, oAux);
            }
        }
      }
    }

    for (var [key, lotRow] of elementToAdd.lotRows) {
        if (! mLots.has(lotRow.iLotId)) {
          mLots.set(lotRow.iLotId, lotRow);
        }
        else {
          var oAux = mLots.get(lotRow.iLotId);
          oAux.dQuantity += parseFloat(lotRow.dQuantity, 10)
          mLots.set(lotRow.iLotId, oAux);
        }
    }

    var mapAsc = new Map([...mLots.entries()].sort(function(a, b) {
	     return (new Date(b[1].tExpDate.replace("-", "/")) < new Date(a[1].tExpDate.replace("-", "/")));
    }));

    var lLots = new Array();

    var iL;
    for (iL = 0; iL < globalData.lFLots.length; iL++) {
        if (globalData.lFLots[iL].id_item == elementToAdd.iItemId
              && globalData.lFLots[iL].id_unit == elementToAdd.iUnitId) {
                globalData.lFLots[iL].dOcupped = 0;
                lLots.push(globalData.lFLots[iL]);
              }
    }

    var tLastLotDate = null;
    if (globalData.oLastLot != null) {
       var sLastLotDate = globalData.oLastLot.dt_expiry;
       var sLastLotTemp = sLastLotDate.replace(/-/g, "/");
       tLastLotDate = new Date(sLastLotTemp);
    }

    for (var [key, lotRow] of mapAsc) {
      var sExDateT = lotRow.tExpDate;
      var sExDate = sExDateT.replace(/-/g, "/");
      var rowDate = new Date(sExDate);
      var indexLot = -1;

      var bContinue = false;
      var iL;
      for (iL = 0; iL < lLots.length; iL++) {
         var sExpLoT = lLots[iL].dt_expiry;
         var lotDate = new Date(sExpLoT.replace(/-/g, "/"));

         if (tLastLotDate == null || (tLastLotDate != null && lotDate >= tLastLotDate)) {
           if (lotDate < rowDate
                && (parseFloat(lLots[iL].available_stock, 10) - lLots[iL].dOcupped) > 0) {
                if (! bContinue) {
                  if (confirm("El lote " + lotRow.sLot  +
                        " no es el más próximo a vencerse. Desea continuar?")) {
                      bContinue = true;
                  } else {
                      return false;
                  }
                }
           }

           if (lotRow.iLotId == lLots[iL].id_lot) {
              indexLot = iL;
           }
         }
      }

      if (indexLot > 0) {
          lLots[indexLot].dOcupped += parseFloat(lotRow.dQuantity, 10);
      }
    }

    // if (lotRow.iLotId == globalData.lFLots[iL].id_lot) {
        // var tDtExpiry = new Date(globalData.lFLots[iL].dt_expiry.replace("-", "/"));
        // console.log(tDtExpiry);
    // }

    return true;
  }
}

var oRotation = new SRotation();
