/**
 * reset the values of window
 */
function cleanModal() {
    $('.ing-chos').val('').trigger("chosen:updated");
    $('.ing-chos').prop('disabled', false).trigger("chosen:updated");
    document.getElementById('item_type').value = "";
    document.getElementById('sel_formula').value = "";
    document.getElementById('div_formula').style.display = 'none';
    // document.getElementById('dt_start_ing').value = "";
    // document.getElementById('dt_end_ing').value = "";
    document.getElementById('quantityIngredient').value = "";
    // document.getElementById('costIngredient').value = "";
    // document.getElementById('sel_formula_subs').value = "";
    // document.getElementById('div_formula_subs').style.display = 'none';
    // document.getElementById('duration').value = "";
    // document.getElementById('suggested').value = "";
    // document.getElementById('max').value = "";
}

/*
* Calls the method to save table when the button close of modal window is clicked
*/
$('#closeIngredient').on('click', function(e) {
    var row = new Ingredient();

    row.iIdItem = document.getElementById('mat_prod').value;
    row.iIdItemFormula = document.getElementById('sel_formula').value;
    row.sItemRecipe = row.iIdItemFormula > 1 ? document.getElementById('sel_formula').textContent : 'NA';
    // row.tStart = document.getElementById('dt_start_ing').value;
    // row.tEnd = document.getElementById('dt_end_ing').value;
    row.dQuantity = document.getElementById('quantityIngredient').value;
    // row.dCost = document.getElementById('costIngredient').value;
    // row.dDuration = document.getElementById('duration').value;
    // row.iIdItemSubstitute = document.getElementById('substitute').value;
    // row.iIdItemRecipeSubs = document.getElementById('sel_formula_subs').value;
    // row.dSuggested = document.getElementById('suggested').value;
    // row.dMax = document.getElementById('max').value;

    if (! validateIngredient(row.iIdItem, row.tStart, row.tEnd,
                            row.dQuantity, row.dCost, row.dDuration,
                            row.iIdItemSubstitute, row.dSuggested, row.dMax)) {
      return false;
    }

    addIngredient(row);
});

/**
 * validate the input data from user
 *
 * @param  {integer} iIdItem       id of material
 * @param  {date} tStart        start of validity
 * @param  {date} tEnd          end of validity
 * @param  {double} dQuantity     necessary amount of the added ingredient
 * @param  {double} dCost         cost of ingredient
 * @param  {double} dDuration     duration of preparation
 * @param  {integer} iIdItemSubstitute  id of material substitute
 * @param  {double} dSuggested    percentage suggested of substitute
 * @param  {double} dMax         percentage max of substitute
 *
 * @return {boolean}   return true if the ingredient pass all validations
 */
function validateIngredient(iIdItem, tStart, tEnd, dQuantity, dCost, dDuration,
                              iIdItemSubstitute, dSuggested, dMax) {

    if (iIdItem == "") {
      swal("Error", "Debe seleccionar un ingrediente.", "error");
      return false;
    }
    var iClass = 0;
    var bIsBulk = 0;

    oData.lMaterials.forEach(function(oMaterial) {
        if (oMaterial.id_item == iIdItem) {
            iClass = oMaterial.item_class;
            bIsBulk = oMaterial.is_bulk;
        }
    });

    // if (tStart == "") {
    //   swal("Error", "Debe seleccionar una fecha de inicio.", "error");
    //   return false;
    // }
    // if (tEnd == "") {
    //   swal("Error", "Debe seleccionar una fecha de fin.", "error");
    //   return false;
    // }
    if (iClass == oData.scsiie.ITEM_CLS.PRODUCT) {
      var sel = document.getElementById('sel_formula');
      if (sel.value == '') {
        swal("Error", "Debe seleccionar una fórmula para el ingrediente, " +
                        "si no existe, debe crear una.", "error");
        return false;
      }
    }

    if (dQuantity == "" || dQuantity <= "0") {
      swal("Error", "La cantidad debe ser mayor a cero.", "error");
      return false;
    }

    if (!bIsBulk && isNumberFloat(dQuantity)) {
      swal("Error", "El material/producto no admite decimales.", "error");
      return false;
    }
    // if (dCost == "" || dCost <= "0") {
    //   swal("Error", "El costo debe ser mayor a cero.", "error");
    //   return false;
    // }
    // if (dDuration == "") {
    //   swal("Error", "Introduzca valor en la duración.", "error");
    //   return false;
    // }
    // if (iIdItemSubstitute == "") {
    //   swal("Error", "Debe seleccionar un ingrediente sustituto.", "error");
    //   return false;
    // }
    // if (dSuggested == "") {
    //   swal("Error", "Introduzca valor en el porcentaje sugerido.", "error");
    //   return false;
    // }
    // if (dMax == "") {
    //   swal("Error", "Introduzca valor en el porcentaje máximo.", "error");
    //   return false;
    // }

    return true;
}

function validateFormula() {
  console.log(oData.jsFormula);

  if (! document.getElementById('product') > 1) {
    swal("Error", "Debe seleccionar un material/producto.", "error");
    return false;
  }
  if (oData.jsFormula.lFormulaRows.length == 0) {
    swal("Error", "La fórmula no tiene renglones.", "error");
    return false;
  }
  else {
    for (var i = 0; i < oData.jsFormula.lFormulaRows.length; i++) {
      var oFormulaRow = oData.jsFormula.lFormulaRows[i];
      console.log(oFormulaRow);
      if (oFormulaRow.iItemClass == oData.scsiie.ITEM_CLS.PRODUCT
            && oFormulaRow.iIdItemRecipe <= 1) {
          swal("Error", "El renglón " + oFormulaRow.sItem + " no tiene asignada una fórmula.", "error");
          return false;
      }
    }
  }

  return true;
}

function submitAction() {
  if (validateFormula()) {
     document.getElementById('theForm').submit();
  }
}

function setFormulaToForm() {
  document.getElementById('formula_object').value = JSON.stringify(oData.jsFormula);
}

/**
 * determine if the value correspond to float number (with decimals)
 * 1.45 return true
 * 1 return false
 * 1.0 return false
 * 'hi' return false
 *
 * @param  {text}  value
 * @return {Boolean}
 */
function isNumberFloat(value) {
  return isFloat(parseFloat(value));
}
