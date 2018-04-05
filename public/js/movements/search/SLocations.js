var oLocation = null;
var oLocationDes = null;

class SLocations {

  /**
   * assign the location object to global object
   */
  setLocation(oLocationjs) {
    oLocation = oLocationjs;
  }

  /**
   * assign the location object to global object
   */
  setLocationDes(oLocationjs) {
    oLocationDes = oLocationjs;
  }

/**
 * set the location to current object and update the label
 */
  setDefaultLocation(lLocations) {
    lLocations.forEach(function (loc) {
      if (loc.is_default) {
          locationsJs.setLocation(loc);
          guiValidations.setLocationLabel(loc.code + '-' + loc.name);
      }
    });
  }

  /**
   * set the destiny location object to global object
   */
  setDefaultLocationDes(lLocations) {
    lLocations.forEach(function (loc) {
      if (loc.is_default) {
          locationsJs.setLocationDes(loc);
          guiValidations.setLocationDesLabel(loc.code + '-' + loc.name);
      }
    });
  }

  searchLocation() {
    var sCode = document.getElementById('location').value;
    itemSelection.search(sCode);
  }

  searchLocationDes() {
    var sCode = document.getElementById('location_des').value;
    itemSelection.search(sCode);
  }

  searchDestinyLocation(sCode) {
    $.get('./' + (globalData.sRoute) +
                  '/search?code=' + sCode,
     function(data) {
        var serverData = JSON.parse(data);
        console.log(serverData);
        guiFunctions.setSearchCode('');
        var bLoc = false;
        switch (serverData.iElementType) {
          case globalData.lElementsType.ITEMS:
          case globalData.lElementsType.LOTS:
          case globalData.lElementsType.PALLETS:
                elementToAdd = null;
                swal("Error", "Sólo pueden escanearse ubicaciones.", "error");
                break;

          case globalData.lElementsType.LOCATIONS:
              var bFound = false;
              globalData.lFDesLocations.forEach(function(loc) {
                 if (loc.id_whs_location == serverData.oElement.id_whs_location) {
                    bFound = true;
                 }
              });

              if (! bFound) {
                swal("Error", "La ubicación no pertenece al almacén.", "error");
                return false;
              }

              guiValidations.setLocationDesLabel(serverData.oElement.code + '-'
                                                  + serverData.oElement.name);
              locationsJs.setLocationDes(serverData.oElement);
              guiValidations.setSearchLocationDesText('');
              return true;

          case globalData.lElementsType.NOT_FOUND:
              swal("Error", "No se encontraron resultados.", "error");
              return false;
              break;

          default:

        }
     });
  }

  updateLocationsTable() {
    if (oLocationsTable != null) {
      oLocationsTable.destroy();
    }
    if (oLocationsDesTable != null) {
      oLocationsDesTable.destroy();
    }

    var aColumns = [];
    var oData = [];

    aColumns = [
          {
              "title": "idLocation",
              "data": "id_whs_location"
          }, {
              "title": "Código",
              "data": "code"
          }, {
              "title": "Ubicación",
              "data": "name"
          }, {
              "title": "Default",
              "data": "is_default"
          }
      ];

      if (globalData.bIsInputMov) {
          oData = globalData.lFDesLocations;
      }
      else {
          oData = globalData.lFSrcLocations;
      }

      if (globalData.iMvtType == globalData.MVT_TP_OUT_TRA) {
        var oDataDes = globalData.lFDesLocations;

        oLocationsDesTable = $('#locations_des_table').DataTable({
            "language": {
              "sProcessing":     "Procesando...",
              "sLengthMenu":     "Mostrar _MENU_ registros",
              "sZeroRecords":    "No se encontraron resultados",
              "sEmptyTable":     "Ningún dato disponible en esta tabla",
              "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
              "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
              "sInfoPostFix":    "",
              "sSearch":         "Buscar:",
              "sUrl":            "",
              "sInfoThousands":  ",",
              "sLoadingRecords": "Cargando...",
              "oPaginate": {
                  "sFirst":    "Primero",
                  "sLast":     "Último",
                  "sNext":     "Siguiente",
                  "sPrevious": "Anterior"
              },
              "oAria": {
                  "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                  "sSortDescending": ": Activar para ordenar la columna de manera descendente"
              }
            },
            "scrollY":        "50vh",
            "scrollCollapse": true,
            "paging":         false,
            "data": oDataDes,
            "columns": aColumns
        });

        oLocationsDesTable.column( 0 ).visible( false );
      }

      oLocationsTable = $('#locations_table').DataTable({
          "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
          },
          "scrollY":        "50vh",
          "scrollCollapse": true,
          "paging":         false,
          "data": oData,
          "columns": aColumns
      });

      oLocationsTable.column( 0 ).visible( false );
  }
}

var locationsJs = new SLocations();

function searchLoc(e) {
    if (e.keyCode == 13) {
      locationsJs.searchLocation();
    }
}

function searchLocDes(e) {
    if (e.keyCode == 13) {
      locationsJs.searchLocationDes();
    }
}

$('#select_button_loc').on('click', function(e) {
    var row = oLocationsTable.row('.selected').data();

    if (row == undefined) {
      swal("Error", "Debe seleccionar un elemento.", "error");
      return false;
    }

    oLocation = new Object();

    oLocation.id_whs_location = row['id_whs_location'];
    oLocation.name = row['name'];
    oLocation.code = row['code'];
    oLocation.whs_id = row['whs_id'];
    oLocation.created_by_id = row['created_by_id'];
    oLocation.created_by_id = row['created_by_id'];

    guiValidations.setLocationLabel(row['code'] + '-' + row['name']);
});

$('#select_button_loc_des').on('click', function(e) {
    var row = oLocationsDesTable.row('.selected').data();

    if (row == undefined) {
      swal("Error", "Debe seleccionar un elemento.", "error");
      return false;
    }

    oLocationDes = new Object();

    oLocationDes.id_whs_location = row['id_whs_location'];
    oLocationDes.name = row['name'];
    oLocationDes.code = row['code'];
    oLocationDes.whs_id = row['whs_id'];
    oLocationDes.created_by_id = row['created_by_id'];
    oLocationDes.created_by_id = row['created_by_id'];

    guiValidations.setLocationDesLabel(row['code'] + '-' + row['name']);
});
