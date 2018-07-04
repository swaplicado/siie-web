/**
 * FormulaJs
 */
class FormulaJs {
    constructor() {
      this.idRow = 0;
      this.numNote = 0;
      this.iRecipe = 0;
      this.lFormulaRows = [];
      this.lNotes = [];
    }

    /**
     * get row Ingredient
     *
     * @param  {integer} id [description]
     *
     * @return {Ingredient}    [description]
     */
    getRow(id) {
      id = parseInt(id);
      var mRow = null;

      this.lFormulaRows.forEach(function(element) {
          if (element.idRow == id) {
              mRow = element;
              return true;
          }
      });

      return mRow;
    }

    /**
     * get Note
     *
     * @param  {integer} id [description]
     *
     * @return {Note}    [description]
     */
    getNote(id) {
      id = parseInt(id);
      var note = null;

      this.lNotes.forEach(function(element) {
          if (element.nuNote == id) {
              note = element;
              return true;
          }
      });

      return note;
    }

    /**
     * get row Ingredient
     *
     * @param  {integer} id [description]
     *
     * @return {Ingredient}    [description]
     */
    getRowById(id) {
      id = parseInt(id);
      var mRow = null;

      this.lFormulaRows.forEach(function(element) {
          if (element.iIdFormulaRow == id) {
              mRow = element;
              return true;
          }
      });

      return mRow;
    }

    /**
     * this method add a Row of formula
     * and assign an id depends of counter
     *
     * @param {Ingredient} row
     */
    addRow(row) {
      row.idRow = this.idRow;
      this.lFormulaRows.push(row);
      this.idRow++;
    }

    /**
     * this method add a Row of formula
     * and assign an id depends of counter
     *
     * @param {Note} note
     */
    addNote(note) {
      note.nuNote = this.numNote;
      this.lNotes.push(note);
      this.numNote++;
    }

    /**
     * removes the row of the formula
     * that corresponds to identifier received
     *
     * @param  {integer} ident identifier of row
     */
    removeRow(ident) {
      var row = this.getRow(ident);

      if (row.iIdFormulaRow == 0) {
        this.lFormulaRows = this.lFormulaRows.filter(function( obj ) {
            return obj.idRow != ident;
        });
      }
      else {
        row.bIsDeleted = true;
      }
    }

    /**
     * removes the row of the formula
     * that corresponds to identifier received
     *
     * @param  {integer} ident identifier of row
     */
    removeNote(ident) {
      var note = this.getNote(ident);

      if (note.iIdNote == 0) {
        this.lNotes = this.lNotes.filter(function( obj ) {
            return obj.nuNote != ident;
        });
      }
      else {
        note.bIsDeleted = true;
      }
    }
}

/**
 * Ingredient
 */
class Ingredient {
    constructor() {
      this.idRow = 0;

      this.iIdFormulaRow = 0;
      this.iIdItem = 0;
      this.iIdUnit = 0;
      this.iIdItemRecipe = 1;
      this.tStart = '';
      this.tEnd = '';
      this.dQuantity = 0;
      this.dMass = 0;
      this.dCost = 0;
      this.dDuration = 0;
      this.iIdItemSubstitute = 0;
      this.iIdUnitSubstitute = 0;
      this.iIdItemRecipeSubs = 1;
      this.dSuggested = 0;
      this.dMax = 0;
      this.bIsDeleted = false;
      this.iFormulaId = 0;

      this.sItemRecipe = 'NA';
    }
}

/**
 * updates the values of formula in the view when
 * the product selected changes
 *
 * @param selectObj select object
 */
function setFormulaData(selectObj) {
    var sIdentifier = selectObj.selectedOptions[0].text;
    var sItemValue = selectObj.value;
    var aValues = sItemValue.split('-', 2);
    var iItemId = aValues[0];
    var iUnitId = aValues[1];

    setData(iItemId, iUnitId, sIdentifier);

    if (iItemId != "") {
      document.getElementById('btnAdd').disabled = false;
    }
    else {
      document.getElementById('btnAdd').disabled = true;
    }
}

/**
 * set the data to view
 *
 * @param {integer} iItemId item id  of item related to formula
 * @param {integer} iUnitId unit if of item related to formula
 * @param {string} sIdentifier   name of formula
 */
function setData(iItemId, iUnitId, sIdentifier) {
    var sUnitCode = '';
    oData.lUnits.forEach(function(oUnit) {
        if (oUnit.id_unit == iUnitId) {
            sUnitCode = oUnit.code;
            return false;
        }
    });

    document.getElementById('item_id').value = iItemId;
    document.getElementById('unit_id').value = iUnitId;
    // document.getElementById('item_recipe_id').value = iIdItemRecipe;
    document.getElementById('identifier').value = sIdentifier;
    document.getElementById('unit').innerHTML = sUnitCode;
}

/**
 * update the code of unit when the material on
 * ingredient view changes
 *
 * @param selectObj
 */
function setIngredientData(selectObj) {
    var iItemId = selectObj.value;
    var sUnitCode = '';
    var bIsBulk = '';
    var sItemType = '';
    var iIdItemType = 0;
    var iIdItemClass = 0;

    oData.lMaterials.forEach(function(oMaterial) {
        if (oMaterial.id_item == iItemId) {
            sUnitCode = oMaterial.unit_code;
            sItemType = oMaterial.item_type;
            bIsBulk = oMaterial.is_bulk;
            iIdItemType = oMaterial.id_item_type;
            iIdItemClass = oMaterial.item_class;
            return false;
        }
    });

    if (iIdItemClass == oData.scsiie.ITEM_CLS.PRODUCT) {
      getFormulasOfItem(iItemId);
    }
    else {
      document.getElementById('div_formula').style.display = 'none';
    }

    document.getElementById('lUnitIngredient').innerHTML = sUnitCode;
    document.getElementById('item_type').value = sItemType;
    document.getElementById('lBulk').innerHTML = bIsBulk ? 'A GRANEL' : 'EN UNIDAD';
}

function getFormulasOfItem(iItemId) {
    var formulas = $.get((oData.oFormula.id_formula != undefined ?
                      './edit/itemformulas' :
                      './create/itemformulas') + '?id=' + iItemId,
                      function(response){
        $('#sel_formula').empty();

        response.forEach(function(oFormula) {
          var option = $("<option value=" + oFormula.id_formula + "></option>")
  	                  .attr(oFormula, oFormula.id_formula)
  	                  .text(oFormula.identifier);

  				$('#sel_formula').append(option);
        });

        document.getElementById('div_formula').style.display = '';
    });
}

/**
 * load the ingredient to view when a row of formula
 * will be edited
 *
 * @param {integer} iIngredientId [description]
 */
function setIngredient(iIngredientId) {
    var row = oIngredientsTable.row('.selected').data();

    if (row != undefined) {
      oRow = oData.jsFormula.getRow(row[0]);
    }
    else {
      swal("Error", "No se puede cargar este renglón.", "error");
      return null;
    }

    var sUnit = '';
    oData.lUnits.forEach(function(oUnit) {
      if (oUnit.id_unit == oRow.iIdUnit) {
        sUnit = oUnit.code;
      }
    });

    var oItem = null
    oData.lMaterials.forEach(function(oMaterial) {
      if (oMaterial.id_item == oRow.iIdItem) {
        oItem = oMaterial;
      }
    });

    $('.cls-ing').val(oRow.iIdItem).trigger("chosen:updated");
    $('.cls-ing').prop('disabled', true).trigger("chosen:updated");
    $('.cls-subs').val(oRow.iIdItemSubstitute).trigger("chosen:updated");

    if (oRow.iIdItemRecipe != 1) {
      $('sel_formula').val(oRow.iIdItemRecipe).trigger("chosen:updated");
      document.getElementById('div_formula').style.display = '';
      getFormulasOfItem(oItem.id_item);
    }
    if (oRow.iIdItemRecipeSubs != 1) {
      $('sel_formula_subs').val(oRow.iIdItemRecipeSubs).trigger("chosen:updated");
      document.getElementById('div_formula_subs').style.display = '';
    }

    document.getElementById('item_type').value = oItem.item_type;
    document.getElementById('dt_start_ing').value = oRow.tStart;
    document.getElementById('dt_end_ing').value = oRow.tEnd;
    document.getElementById('quantityIngredient').value = oRow.dQuantity;
    document.getElementById('lUnitIngredient').innerHTML = sUnit;
    document.getElementById('lBulk').innerHTML = oItem.is_bulk ? 'A GRANEL' : 'POR UNIDAD';
    document.getElementById('costIngredient').value = oRow.dCost;
    document.getElementById('duration').value = oRow.dDuration;
    document.getElementById('suggested').value = oRow.dSuggested;
    document.getElementById('max').value = oRow.dMax;
}

/**
 * add the ingredient set on view to the table
 * and to the formula object.
 * Refresh too the object to be sent to the server
 *
 * @param {Ingredient} oRow [description]
 */
function addIngredient(oRow) {
    var oItem = null;
    var oItemSubstitute = null;
    var dPercentage = 0;

    oData.lMaterials.forEach(function(oMaterial) {
        if (oMaterial.id_item == oRow.iIdItem) {
            oItem = oMaterial;
        }
        if (oRow.iIdItemSubstitute != "" &&
                  oMaterial.id_item == oRow.iIdItemSubstitute) {
            oItemSubstitute = oMaterial;
        }
    });

    if (oRow.iIdItemRecipe == '') {
        oRow.iIdItemRecipe = 1;
    }

    if (oItem.item_type_id == oData.lItemTypes.BASE_PRODUCT &&
          oRow.iIdItemRecipe == 1) {
        swal("Error", "Debe elegir una fórmula.", "error");
        return false;
    }

    if (oRow.iIdItemSubstitute == "") {
      oRow.iIdItemSubstitute = 0;
      oRow.iIdUnitSubstitute = 0;
    }
    else {
      oRow.iIdUnitSubstitute = oItemSubstitute.unit_id;
    }

    oRow.iIdUnit = oItem.unit_id;
    oRow.dMass = oItem.mass * oRow.dQuantity;

    oData.jsFormula.addRow(oRow);

    var dTotalMass = 0;
    for (var i = 0; i < oData.jsFormula.lFormulaRows.length; i++) {
        dTotalMass += oData.jsFormula.lFormulaRows[i].dMass;
    }

    dPercentage = parseFloat(oRow.dMass, 10) == 0 ? 0 : parseFloat(oRow.dMass, 10) * 100 / dTotalMass;

    oIngredientsTable.row.add([
        oRow.idRow,
        oRow.iIdFormulaRow,
        oItem.code,
        oItem.name,
        parseFloat(oRow.dQuantity, 10).toFixed(oData.DEC_QTY),
        oItem.unit_code,
        parseFloat(oRow.dMass, 10).toFixed(oData.DEC_QTY),
        parseFloat(dPercentage, 10).toFixed(oData.DEC_QTY),
        oItem.item_type,
        oRow.sItemRecipe
        // oRow.dCost,
        // oRow.tStart,
        // oRow.tEnd
    ]).draw( false );

    setFormulaToForm();

    var column = oIngredientsTable.column( 6 );

    column.footer().innerHTML =  parseFloat(dTotalMass).toFixed(oData.DEC_QTY);

    $('mat_prod').val(null).trigger("chosen:updated");
    document.getElementById('lUnitIngredient').innerHTML = '-';
    document.getElementById('lBulk').innerHTML = '-';
}

/**
 * remove the row of formula from view table
 */
$('#btnDel').click( function () {
    var row = oIngredientsTable.row('.selected').data();
    oData.jsFormula.removeRow(row[0]);

    oIngredientsTable.row('.selected').remove().draw( false );

    setFormulaToForm();
});
