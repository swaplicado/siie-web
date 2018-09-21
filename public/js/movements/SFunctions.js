class SUtilFunctions {
  constructor() {

  }

  isProductionMovement(iMovementType) {
      return iMovementType == globalData.scwms.MVT_OUT_DLVRY_RM
              || iMovementType == globalData.scwms.MVT_IN_DLVRY_PP
              || iMovementType == globalData.scwms.MVT_IN_DLVRY_FP
              || iMovementType == globalData.scwms.MVT_OUT_ASSIGN_PP
                || iMovementType == globalData.scwms.MVT_OUT_RTRN_RM;
  }

  isProductionTransfer(iMovementType) {
      return iMovementType == globalData.scwms.MVT_OUT_DLVRY_RM
                || iMovementType == globalData.scwms.MVT_OUT_RTRN_RM
                  || iMovementType == globalData.scwms.MVT_OUT_ASSIGN_PP;
  }

  isProductionDelivery(iMovementType) {
      return iMovementType == globalData.scwms.MVT_IN_DLVRY_PP
                || iMovementType == globalData.scwms.MVT_IN_DLVRY_FP;
  }

  round(num, decimales = 2) {
    var signo = (num >= 0 ? 1 : -1);
    num = num * signo;
    if (decimales === 0) //con 0 decimales
        return signo * Math.round(num);
    // round(x * 10 ^ decimales)
    num = num.toString().split('e');
    num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
    // x * 10 ^ (-decimales)
    num = num.toString().split('e');
    return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
}


}

utilFunctions = new SUtilFunctions();
