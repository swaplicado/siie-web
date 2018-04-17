class SGuiTransfers {
  constructor() {

  }

  disableHeader() {
    document.getElementById('dt_date').readOnly = true;
    $('#whs_id').attr("disabled", true).trigger("chosen:updated");
  }

  enableHeader() {
    document.getElementById('dt_date').readOnly = false;
    $('#whs_id').attr("disabled", false).trigger("chosen:updated");
  }

  hideContinue() {
    document.getElementById('div_continue').style.display = 'none';
  }

  showContinue() {
    document.getElementById('div_continue').style.display = 'block';
  }

  showSearchPanel() {
    document.getElementById('div_search').style.display = 'block';
  }

  showTablePanel() {
    document.getElementById('div_table').style.display = 'block';
  }
}

var guiTransfers = new SGuiTransfers();
