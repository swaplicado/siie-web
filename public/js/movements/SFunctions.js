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


}

utilFunctions = new SUtilFunctions();
