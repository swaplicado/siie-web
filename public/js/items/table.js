var oItemsTable = null;
var oLocationsTable = null;

$(document).ready(function() {

    $('#items_table tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            oItemsTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );

    $('#locations_table tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            oLocationsTable.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
} );
