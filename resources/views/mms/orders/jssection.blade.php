<script>

  function GlobalData () {
    this.scmms = <?php echo json_encode(\Config::get('scmms')) ?>;
    this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;

    var qty = <?php echo json_encode(session('decimals_qty')) ?>;
    var amt = <?php echo json_encode(session('decimals_amt')) ?>;
    var loc = <?php echo json_encode(session('location_enabled')) ?>;
    this.DEC_QTY = parseInt(qty);
    this.DEC_AMT = parseInt(amt);
  }

  var globalData = new GlobalData();

</script>
