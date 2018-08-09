<script>

  function GlobalData () {
    this.oDocument = <?php echo json_encode($oDocument); ?>;
    this.lDocData = <?php echo json_encode($lDocData); ?>;
    this.iOperation = <?php echo json_encode($iOperation); ?>;
    this.iMovId = <?php echo json_encode($oMovement->id_mvt); ?>;
    this.iMvtClass = <?php echo json_encode($oMovement->mvt_whs_class_id); ?>;
    this.iMvtType = <?php echo json_encode($oMovement->mvt_whs_type_id); ?>;
    this.bIsInputMov = <?php echo json_encode($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')); ?>;
    this.lFItems = [];
    this.lFLots = [];
    this.lFPallets = [];
    this.lFStock = <?php echo json_encode($lStock != null ? $lStock : array()); ?>;
    this.lFSrcLocations = [];
    this.lFDesLocations = [];
    this.lElementsType = <?php echo json_encode(\Config::get('scwms.ELEMENTS_TYPE')) ?>;
    this.lOperationType = <?php echo json_encode(\Config::get('scwms.OPERATION_TYPE')) ?>;
    this.lOperation = <?php echo json_encode(\Config::get('scwms.OPERATION')) ?>; //input-output
    this.bIsExternalTransfer = <?php echo json_encode($bIsExternalTransfer) ?>;

    this.iAssignType = <?php echo json_encode($iAssType) ?>;

    this.scmms = <?php echo json_encode(\Config::get('scmms')) ?>;
    this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;

    this.MVT_CLS_IN = <?php echo json_encode(\Config::get('scwms.MVT_CLS_IN')) ?>; //
    this.MVT_CLS_OUT = <?php echo json_encode(\Config::get('scwms.MVT_CLS_OUT')) ?>; //

    this.MVT_TP_IN_SAL = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_SAL')) ?>;
    this.MVT_TP_IN_PUR = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_PUR')) ?>;
    this.MVT_TP_IN_ADJ = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_ADJ')) ?>;
    this.MVT_TP_IN_TRA = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_TRA')) ?>; // transfer (traspaso)
    this.MVT_TP_IN_CON = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_CON')) ?>; // conversion
    this.MVT_TP_IN_PRO = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_PRO')) ?>; // production
    this.MVT_TP_IN_EXP = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_EXP')) ?>; // expenses
    this.MVT_TP_OUT_SAL = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_SAL')) ?>;
    this.MVT_TP_OUT_PUR = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_PUR')) ?>;
    this.MVT_TP_OUT_ADJ = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_ADJ')) ?>;
    this.MVT_TP_OUT_TRA = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_TRA')) ?>;
    this.MVT_TP_OUT_CON = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_CON')) ?>;
    this.MVT_TP_OUT_PRO = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_PRO')) ?>;
    this.MVT_TP_OUT_EXP = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_EXP')) ?>;

    this.PALLET_RECONFIG_IN  =  <?php echo json_encode(\Config::get('scwms.PALLET_RECONFIG_IN')) ?>;
    this.PALLET_RECONFIG_OUT  =  <?php echo json_encode(\Config::get('scwms.PALLET_RECONFIG_OUT')) ?>;

    this.PHYSICAL_INVENTORY  =  <?php echo json_encode(\Config::get('scwms.PHYSICAL_INVENTORY')) ?>;

    this.lItemLinks = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK')); ?>;

    this.lContainers = <?php echo json_encode(\Config::get('scwms.CONTAINERS')); ?>;

    var qty = <?php echo json_encode(session('decimals_qty')) ?>;
    var amt = <?php echo json_encode(session('decimals_amt')) ?>;
    var loc = <?php echo json_encode(session('location_enabled')) ?>;
    this.DEC_QTY = parseInt(qty);
    this.DEC_AMT = parseInt(amt);
    this.LOCATION_ENABLED = (parseInt(loc) == 1);
    this.isPalletReconfiguration = this.iMvtType == this.PALLET_RECONFIG_IN || this.iMvtType == this.PALLET_RECONFIG_OUT;
    this.isPalletDivision = this.iMvtType == this.PALLET_RECONFIG_IN;
    this.dPerSupp = <?php echo json_encode(($dPerSupp/100)); ?>; //percentage of supply permitted

    this.sRoute = '';
    if (this.iOperation == this.lOperationType.EDITION) {
        this.sRoute = 'edit';
    }
    else if (this.oDocument != 0) {
        this.sRoute = 'supply';
    }
    else {
        if (this.iMvtType == this.PHYSICAL_INVENTORY ) {
          this.sRoute = 'physicalinventory'
        }
        else {
          this.sRoute = 'create';
        }
    }
  }

  var globalData = new GlobalData();
  headerCore.initializeStock();
  if (! globalData.LOCATION_ENABLED) {
      oMovsTable.column( 4 ).visible( false );
  }

  var lDocRows = <?php echo json_encode($lDocData) ?>;

  // if (lDocRows.length > 0) {
  // 		headerCore.transformServerToClientDocRows(lDocRows);
  // }

  var lRows = <?php echo json_encode($oMovement->rows) ?>;

  if (lRows.length > 0) {
      headerCore.transformServerToClientRows(lRows);
      oMovement.iIdMovement = <?php echo json_encode($oMovement->id_mvt) ?>;
  }

  if (localStorage.getItem('movement') !== null) {
    var errors = <?php echo json_encode($errors->all()) ?>;
    console.log(errors);

    if (errors.length > 0) {
      var retrievedObject = localStorage.getItem('movement');
      var movement = JSON.parse(retrievedObject);
      oMovement = loadMovement(movement);

      document.getElementById('mvt_com').value = oMovement.iMvtSubType;

      if (oMovement.iWhsDes != 0) {
        document.getElementById('whs_des').value = oMovement.iWhsDes;
      }
      if (movement.iWhsSrc != 0) {
        document.getElementById('whs_src').value = oMovement.iWhsSrc;
      }

      guiValidations.disableHeader();
    }

    localStorage.removeItem('movement');
  }

  $('.select-one').chosen({
    placeholder_select_single: 'Seleccione un item...'
  });

  if (globalData.iMvtType == globalData.MVT_TP_IN_PUR) {
      progressBar.updateProgressbar();
  }

</script>
