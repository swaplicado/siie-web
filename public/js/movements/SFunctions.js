class SUtilFunctions {
  constructor() {

  }

  isProductionMovement(iMovementType) {
      return iMovementType == globalData.scwms.MVT_OUT_DLVRY_RM
              || iMovementType == globalData.scwms.MVT_OUT_DLVRY_PP;
  }
}

utilFunctions = new SUtilFunctions();
