/*
* Movement
*/
class SMovement {
    constructor() {
      this.iIdNewRow = 0;

      this.iIdMovement = 0;
      this.iMvtType = 0;
      this.iMvtSubType = 0;
      this.tDate = 0;
      this.iWhsSrc = 0;
      this.iWhsDes = 0;
      this.iDocumentId = 0;
      this.bIsDeleted = false;

      this.rows = new Map();
      this.lAuxRows = null;
      this.lAuxlotsToCreate = null;
      this.auxPalletRow = '';
    }

    get newId() {
      return this.iIdNewRow;
    }

    getRow(id) {
      var key = parseInt(id);

      return this.rows.get(key);
    }

    addRow(row) {
      row.iIdRow = this.iIdNewRow;
      this.rows.set(this.iIdNewRow, row);
      this.iIdNewRow++;
    }

    removeRow(id) {
      var key = parseInt(id);
      var row = this.rows.get(key);

      if (row.iIdMovRow > 0) {
          row.bIsDeleted = true;
      }
      else {
          this.rows.delete(key);
      }
    }
}

/*
* Rows of movement
*/
class SMovementRow {
    constructor() {
      this.iIdRow = 0;
      this.idNewLotRow = 0;

      this.iIdMovRow = 0;
      this.iItemId = 0;
      this.iUnitId = 0;
      this.bIsLot = false;
      this.bIsBulk = false;
      this.iPalletId = 1;
      this.iLocationId = 0;
      this.dQuantity = 0;
      this.dPrice = 0;
      this.dAuxQuantity = 0;

      this.oAuxItem = '';
      this.oAuxUnit = '';
      this.oAuxPallet = '';
      this.oAuxLocation = '';
      this.iDocOrderRowId = 1;
      this.iDocInvoiceRowId = 1;
      this.iDocDebitNoteRowId = 1;
      this.iDocCreditNoteRowId = 1;
      this.bIsDeleted = false;

      this.sItem = 'NA';
      this.sItemCode = 'NA';
      this.sUnit = 'NA';

      this.sLocation = 'DEFAULT';
      this.sPallet = 'NA';

      this.iLotId = 1;
      this.sLot = 'NA';
      this.tExpDate = 'NA';
      this.iKeyLot = null; // key to map the new Lot

      this.lotRows = new Map();
      this.lAuxlotRows = null;
      this.lAuxlotsToCreate = null;

      this.iElementType = 404;
      this.oElement = null;
    }

    get lotIdentifier() {
      return this.idNewLotRow;
    }

    getLotRow(id) {
      var key = parseInt(id);

      return this.lotRows.get(key);
    }

    addLotRow(lotRow) {
      this.lotRows.set(this.idNewLotRow, lotRow);
      this.idNewLotRow++;
    }

    removeLotRow(id) {
      var key = parseInt(id);
      var lotRow = this.lotRows.get(key);

      if (lotRow.iIdLotRow > 0) {
          lotRow.bIsDeleted = true;
      }
      else {
          this.lotRows.delete(key);
      }
    }
}

/*
* Rows of RowMovement (Lots)
*/
class SLotRow {
    constructor() {
      this.id = 0;

      this.iIdLotRow = 0;
      this.iLotId = 0;
      this.sLot = '';
      this.tExpDate = '';
      this.dQuantity = 0;
      this.dPrice = 0;
      this.bIsDeleted = false;

      this.bCreate = false;
    }

    get identifier() {
      return this.id
    }
}

/**
 * [setMovement description]
 * @param {[type]} obj [description]
 */
function setMovement(obj) {
   var mov = new SMovement();
   mov.iMvtType = obj.iMvtType;
   mov.iWhsSrc = obj.iWhsSrc;
   mov.iWhsDes = obj.iWhsDes;
   mov.idRow = obj.idRow;

   var mRows = [];
   obj.rows.forEach(function(rowS) {
       var rowN = new SMovementRow(rowS.iIdRow);
       rowN.idLotRow = rowS.idLotRow;
       rowN.iItemId = rowS.iItemId;
       rowN.iUnitId = rowS.iUnitId;
       rowN.iPalletId = rowS.iPalletId;
       rowN.iLocationId = rowS.iLocationId;
       rowN.dQuantity = rowS.dQuantity;
       rowN.dPrice = rowS.dPrice;
       rowN.oAuxItem = rowS.oAuxItem;
       rowN.oAuxUnit = rowS.oAuxUnit;
       rowN.oAuxPallet = rowS.oAuxPallet;
       rowN.oAuxLocation = rowS.oAuxLocation;
       rowN.aStock = rowS.aStock;

       var mLotRows = [];
       rowS.lotRows.forEach(function(lotRowS) {
          var lotRowN = new SMovementRow(lotRowS.id);
          lotRowN.iLotId = lotRowS.iLotId;
          lotRowN.dQuantity = lotRowS.dQuantity;
          lotRowN.dPrice = lotRowS.dPrice;

          mLotRows.push(lotRowN);
       });
       rowN.lotRows = mLotRows

       mRows.push(rowN);
   });

   mov.rows = mRows;

   return mov;
}

/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setData(data) {
    // var table = $('#example').tableToJSON();
    console.log(data);
    if (data.auxPalletRow != null && data.auxPalletRow != "") {
      localStorage.setItem('pallet', JSON.stringify(data.auxPalletRow));
    }
    localStorage.setItem('movement', JSON.stringify(data));
    var data = { value : data };
      $.ajax({
        type: "POST",
        url: './' + (globalData.oDocument != 0 ? 'supply' : 'create') + '/storetable',
        data: data,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          console.log("Value added ");
        }
      });
}

function setMovementToForm() {
  for (var [key, oRow] of oMovement.rows) {
     oRow.lAuxlotRows = Array.from(oRow.lotRows);
  }
  oMovement.lAuxRows = Array.from(oMovement.rows);
  oMovement.lAuxlotsToCreate = lLotsToCreate;

  localStorage.setItem('movement', JSON.stringify(oMovement));
  document.getElementById('movement_object').value = JSON.stringify(oMovement);
}

var oMovement = new SMovement(); // Initialization, the counter inits in 0
