class SExplosionCore {
  constructor() {
     this.BY_ORDER = 1;
     this.BY_PLAN = 2;
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

  onChangeExplosionBy() {
    var iOption = $('input[name=explosion_by]:checked').val();

    if (iOption == explosionCore.BY_ORDER) {
       explosionCore.hidePlanes();
       explosionCore.showOrders();
    }
    else if (iOption == explosionCore.BY_PLAN) {
        explosionCore.hideOrders();
        explosionCore.showPlanes();
    }
  }
}

var explosionCore = new SExplosionCore();

function explosionByChange() {
    explosionCore.onChangeExplosionBy();
}
