class SProgressBar {
  updateProgressbar() {
    var dQtyToSup = 0;
    var dQtySupp = 0;
    oDocsTable.rows().eq(0).each( function ( index ) {
        var row = oDocsTable.row( index );

        var data = row.data();
        dQtyToSup += parseFloat(data[8].replace(',', ''), 10);
        dQtySupp += parseFloat(data[9].replace(',', ''), 10);
    });

    // globalData.lDocData.forEach(function(oDocRow) {
    //    dQtyToSup += parseFloat(oDocRow.pending, 10);
    // });
    //
    // var dQtySupp = 0;
    // for (var [key, element] of oMovement.rows) {
    //     dQtySupp += parseFloat(element.dQuantity, 10);
    // }

    var dPercentage = dQtyToSup == 0 ? 0 : (dQtySupp * 100) / dQtyToSup;

    dPercentage = dPercentage < 0 ? 0 : dPercentage;
    dPercentage = dPercentage > 100 ? 100 : dPercentage;

    var $pb = $('.progress .progress-bar');
    $pb.attr('data-transitiongoal', dPercentage).progressbar({use_percentage: true});
  }
}

progressBar = new SProgressBar();
