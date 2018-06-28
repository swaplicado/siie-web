class SIngredientCore {
  constructor() {

  }

  loadIngredients(lIngredients) {
     var listIngredients = new Array();

      lIngredients.forEach(function(oFormulaRow) {
         var oIngredient = new Ingredient();
         oIngredient.iIdFormulaRow = 0;
         oIngredient.iIdItem = oFormulaRow.item_id;
         oIngredient.iIdUnit = oFormulaRow.unit_id;
         oIngredient.iIdItemRecipe = oFormulaRow.item_recipe_id;
         oIngredient.dQuantity = oFormulaRow.quantity;
         oIngredient.bIsDeleted = oFormulaRow.is_deleted;
         oIngredient.iFormulaId = 0;

         addIngredient(oIngredient);
      });

      document.getElementById('btnAdd').disabled = false;
  }
}

var oIngredientCore = new SIngredientCore();
