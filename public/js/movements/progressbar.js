function updateProgressbar() {
  var dQtyToSup = 0;
  globalData.lDocData.forEach(function(oDocRow) {
     dQtyToSup += parseFloat(oDocRow.pending, 10);
  });

  var dQtySupp = 0;
  movement.rows.forEach(function(element) {
      if (element.lotRows.length > 0) {
          movement.rows.forEach(function(lotRow) {
              dQtySupp += parseFloat(lotRow.dQuantity, 10);
          });
      }
      else {
        dQtySupp += parseFloat(element.dQuantity, 10);
      }
  });

  var dPercentage = dQtyToSup == 0 ? 0 : (dQtySupp * 100) / dQtyToSup;

  dPercentage = dPercentage < 0 ? 0 : dPercentage;
  dPercentage = dPercentage > 100 ? 100 : dPercentage;

  var $pb = $('.progress .progress-bar');
  $pb.attr('data-transitiongoal', dPercentage).progressbar({use_percentage: true});
}
