var oElementsTable = null;
var oItemsTable = null;

$(document).ready(function() {

    $('#items_table tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            oElementsTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );

    $('#search_items_table tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            oItemsTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
} );
