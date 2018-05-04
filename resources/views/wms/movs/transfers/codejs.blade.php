<script type="text/javascript">
  function GlobalData () {
    this.sRoute = 'receivetransfer';
    this.DEC_QTY = <?php echo json_encode(session('decimals_qty')); ?>;
    this.DEC_AMT = <?php echo json_encode(session('decimals_amt')); ?>;

    this.iOperation = <?php echo json_encode($iOperation); ?>;
    this.oMovementSrc = <?php echo json_encode($oMovementSrc); ?>;
    this.oMovement = <?php echo json_encode($oMovement); ?>;
    this.bIsInputMov = <?php echo json_encode(true); ?>;
    this.tReceptionDate = <?php echo json_encode($oMovementSrc); ?>;
    this.iWhsTransit = <?php echo json_encode(session('transit_whs')); ?>;
    this.iWhsSrc = this.oMovementSrc.whs_id;
    this.iWhsDes = 0;
    this.lFItems = [];
    this.lFLots = [];
    this.lFPallets = [];
    this.lFStock = <?php echo json_encode(array()); ?>;
    this.lFSrcLocations = [];
    this.lFDesLocations = [];
    this.lFItems = [];
    this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;
  }

  var globalData = new GlobalData();
  console.log(globalData);
</script>
