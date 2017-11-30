/**
 * [whenChangeBranch description]
 * @param  {[type]} idSelectBranch [description]
 * @return {[type]}                [description]
 */
function whenChangeClass(idSelectClass) {
    // console.log("here");
    var iClassId = parseInt(document.getElementById(idSelectClass).value);

    var opt = '<select class="form-control select-one"  name="mvt_type_id" id="mvt_type_id">';
    // opt +='<option value="' + 0 + '">Seleccione Tipo...</option>';

    oData.lTypes.forEach(function(type) {
        if (type.mvt_class_id == iClassId) {
            opt += '<option value="' + type.id_mvt_type + '">' + type.code + "-" + type.name + '</option>';
        }
    });

    $('.tps').empty(" ");
    $('.tps').append(opt);

    // $('.wh').chosen({
    //   placeholder_select_single: 'Seleccione un item...'
    // });
}
/**
 * [whenChangeBranch description]
 * @param  {[type]} idSelectBranch [description]
 * @return {[type]}                [description]
 */
function whenChangeBranch(idSelectBranch) {
    // console.log("here");
    var iBranchId = parseInt(document.getElementById(idSelectBranch).value);

    var opt = '<select onChange="whenChangeWarehouse(\'aux_whs_id\')" class="form-control select-one wh"  name="aux_whs_id" id="aux_whs_id">';
    opt +='<option value="' + 0 + '">Seleccione almacén...</option>';

    oData.lWarehouses.forEach(function(whs) {
        if (whs.branch_id == iBranchId) {
            opt += '<option value="' + whs.id_whs + '">' + whs.code + "-" + whs.name + '</option>';
        }
    });

    $('.whss').empty(" ");
    $('.whss').append(opt);

    // $('.wh').chosen({
    //   placeholder_select_single: 'Seleccione un item...'
    // });

    whenChangeWarehouse('aux_whs_id');
}

/**
 * [whenChangeWarehouse description]
 * @param  {[type]} idSelectWhs [description]
 * @return {[type]}             [description]
 */
function whenChangeWarehouse(idSelectWhs) {
    // console.log("here");
    var iWhsId = parseInt(document.getElementById(idSelectWhs).value);

    var opt = '<select class="form-control select-one lc"  name="aux_location_id" id="aux_location_id">';
    opt +='<option value="' + 0 + '">Seleccione ubicación...</option>';

    oData.lLocations.forEach(function(location) {
        if (location.whs_id == iWhsId) {
            opt += '<option value="' + location.id_whs_location + '">' + location.code + "-" + location.name + '</option>';
        }
    });

    $('.locs').empty(" ");
    $('.locs').append(opt);

    // $('.lc').chosen({
    //   placeholder_select_single: 'Seleccione un item...'
    // });
}
