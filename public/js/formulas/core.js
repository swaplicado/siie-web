/**
 * FormulaJs
 */
class FormulaJs {
    constructor() {
      this.idRow = 0;
      this.numNote = 0;
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
      this.iIdItemFormula = 1;
      this.tStart = '';
      this.tEnd = '';
      this.dQuantity = 0;
      this.dCost = 0;
      this.dDuration = 0;
      this.iIdItemSubstitute = 0;
      this.iIdUnitSubstitute = 0;
      this.iIdItemFormulaSubs = 1;
      this.dSuggested = 0;
      this.dMax = 0;
      this.bIsDeleted = false;
      this.iFormulaId = false;
    }
}

/**
 * updates the values of formula in the view when
 * the product selected changes
 *
 * @param selectObj select object
 */
function setFormulaData(selectObj) {
    var sName = selectObj.selectedOptions[0].text;
    var sItemValue = selectObj.value;
    var aValues = sItemValue.split('-', 2);
    var iItemId = aValues[0];
    var iUnitId = aValues[1];

    setData(iItemId, iUnitId, sName);

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
 * @param {string} sName   name of formula
 */
function setData(iItemId, iUnitId, sName) {
    var sUnitCode = '';
    oData.lUnits.forEach(function(oUnit) {
        if (oUnit.id_unit == iUnitId) {
            sUnitCode = oUnit.code;
            return false;
        }
    });

    document.getElementById('item_id').value = iItemId;
    document.getElementById('unit_id').value = iUnitId;
    // document.getElementById('item_formula_id').value = iIdItemFormula;
    document.getElementById('name').value = sName;
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
    var sItemType = '';
    var iIdItemType = 0;

    oData.lMaterials.forEach(function(oMaterial) {
        if (oMaterial.id_item == iItemId) {
            sUnitCode = oMaterial.unit_code;
            sItemType = oMaterial.item_type;
            iIdItemType = oMaterial.id_item_type;
            return false;
        }
    });

    if (iIdItemType == oData.lItemTypes.BASE_PRODUCT) {
      getFormulasOfItem(iItemId);
    }

    document.getElementById('lUnitIngredient').innerHTML = sUnitCode;
    document.getElementById('item_type').value = sItemType;
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
  	                  .text(oFormula.name);

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
    var row = oTable.row('.selected').data();

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

    if (oRow.iIdItemFormula != 1) {
      $('sel_formula').val(oRow.iIdItemFormula).trigger("chosen:updated");
      document.getElementById('div_formula').style.display = '';
      getFormulasOfItem(oItem.id_item);
    }
    if (oRow.iIdItemFormulaSubs != 1) {
      $('sel_formula_subs').val(oRow.iIdItemFormulaSubs).trigger("chosen:updated");
      document.getElementById('div_formula_subs').style.display = '';
    }

    document.getElementById('item_type').value = oItem.item_type;
    document.getElementById('dt_start_ing').value = oRow.tStart;
    document.getElementById('dt_end_ing').value = oRow.tEnd;
    document.getElementById('quantityIngredient').value = oRow.dQuantity;
    document.getElementById('lUnitIngredient').innerHTML = sUnit;
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

    if (oRow.iIdItemFormula == '') {
        oRow.iIdItemFormula = 1;
    }

    if (oItem.item_type_id == oData.lItemTypes.BASE_PRODUCT &&
          oRow.iIdItemFormula == 1) {
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
    oData.jsFormula.addRow(oRow);

    oTable.row.add([
        oRow.idRow,
        oRow.iIdFormulaRow,
        oItem.code,
        oItem.name,
        oRow.dQuantity,
        oItem.unit_code,
        dPercentage,
        oRow.dCost,
        oRow.tStart,
        oRow.tEnd
    ]).draw( false );

    setFormulaToForm();
}

/**
 * remove the row of formula from view table
 */
$('#btnDel').click( function () {
    var row = oTable.row('.selected').data();
    oData.jsFormula.removeRow(row[0]);

    oTable.row('.selected').remove().draw( false );

    setFormulaToForm();
});
