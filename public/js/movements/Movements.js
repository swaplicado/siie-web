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
      this.iPOSrc = 0;
      this.iPODes = 0;
      this.iDocumentId = 0;
      this.bIsDeleted = false;

      this.rows = new Map();
      this.lAuxRows = null;
      this.lAuxlotsToCreate = null;
      this.auxPalletRow = '';

      this.iBranchDes = 0;
      this.sBranch = '';
      this.sWarehouse = '';
      this.iAuxIndex = 0;
      this.iAuxPallet = 0;
      this.iAuxPalletLocation = 0;
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

      if (! this.rows.has(key)) {
        return false;
      }

      var row = this.rows.get(key);

      if (row.iIdMovRow > 0) {
          row.bIsDeleted = true;
          if (row.bIsLot) {
            for (var [key, lotRow] of row.lotRows) {
               lotRow.bIsDeleted = true;
            }
          }
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
      this.bWithoutRotation = false;
      this.iPalletId = 1;
      this.iLocationId = 0;
      this.dQuantity = 0;
      this.dPrice = 0;
      this.dAuxQuantity = 0;

      this.dLength = 0;
      this.dSurface = 0;
      this.dVolume = 0;
      this.dMass = 0;

      this.iDocOrderRowId = 1;
      this.iDocInvoiceRowId = 1;
      this.iDocDebitNoteRowId = 1;
      this.iDocCreditNoteRowId = 1;
      this.iAuxDocRowId = 0;
      this.bIsDeleted = false;

      this.iLocationDesId = 0;

      this.sItem = 'NA';
      this.sItemCode = 'NA';
      this.sUnit = 'NA';

      this.sLocation = 'DEF';
      this.sLocationDes = 'DEF';
      this.sPallet = 'SIN TARIMA';

      this.iLotId = 1;
      this.sLot = 'NA';
      this.tExpDate = 'NA';
      this.iKeyLot = null; // key to map the new Lot

      this.lotRows = new Map();
      this.lAuxlotRows = null;
      this.lAuxlotsToCreate = null;

      this.iElementType = 404;
      this.oElement = null;

      this.bAuxToStock = true;

      this.iDocRowIndSupp = 0;
      this.sBranch = '';
      this.sWarehouse = '';
      this.iAuxMovIndex = 0;
      this.iTableRowIndex = 0;
      this.dQtyIndSupplied = 0;
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
      if (! this.lotRows.has(key)) {
        return false;
      }

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
      this.dAuxQuantity = 0;

      this.dQuantitySupplied = 0;
    }

    get identifier() {
      return this.id
    }
}

/**
 * [setMovement description]
 * @param {[type]} obj [description]
 */
function loadMovement(obj) {
   var mov = new SMovement();

   mov.iMvtType = obj.iMvtType;
   mov.iMvtSubType = obj.iMvtSubType;
   mov.iWhsSrc = obj.iWhsSrc;
   mov.iWhsDes = obj.iWhsDes;
   mov.idRow = obj.idRow;
   mov.bIsDeleted = obj.bIsDeleted;

   for (var [key, rowS] of obj.lAuxRows) {
     var rowN = new SMovementRow();

     rowN.bAuxToStock = false;

     rowN.iItemId = rowS.iItemId;
     rowN.iUnitId = rowS.iUnitId;
     rowN.iPalletId = rowS.iPalletId;
     rowN.iLocationId = rowS.iLocationId;
     rowN.dQuantity = rowS.dQuantity;
     rowN.dPrice = rowS.dPrice;
     rowN.bIsBulk = rowS.bIsBulk;
     rowN.bWithoutRotation = rowS.bWithoutRotation;
     rowN.bIsLot = rowS.bIsLot;
     rowN.bIsDeleted = rowS.bIsDeleted;
     rowN.dAuxQuantity = rowS.dAuxQuantity;
     rowN.iAuxDocRowId = rowS.iAuxDocRowId;
     rowN.iDocOrderRowId = rowS.iDocOrderRowId;
     rowN.iDocInvoiceRowId = rowS.iDocInvoiceRowId;
     rowN.iDocDebitNoteRowId = rowS.iDocDebitNoteRowId;
     rowN.iDocCreditNoteRowId = rowS.iDocCreditNoteRowId;
     rowN.iElementType = rowS.iElementType;
     rowN.iIdMovRow = rowS.iIdMovRow;
     rowN.iIdRow = rowS.iIdRow;
     rowN.iLocationId = rowS.iLocationId;
     rowN.iLocationDesId = rowS.iLocationDesId;
     rowN.iLotId = rowS.iLotId;
     rowN.iPalletId = rowS.iPalletId;

     rowN.idNewLotRow = rowS.idNewLotRow;
     rowN.sItem = rowS.sItem;
     rowN.sItemCode = rowS.sItemCode;
     rowN.sLocation = rowS.sLocation;
     rowN.sLocationDes = rowS.sLocationDes;
     rowN.sLot = rowS.sLot;
     rowN.sPallet = rowS.sPallet;
     rowN.sUnit = rowS.sUnit;
     rowN.tExpDate = rowS.tExpDate;

     for (var [key, lotRowS] of rowS.lAuxlotRows) {
       var lotRowN = new SLotRow();

       lotRowN.iLotId = lotRowS.iLotId;
       lotRowN.dQuantity = lotRowS.dQuantity;
       lotRowN.dPrice = lotRowS.dPrice;
       lotRowN.bCreate = lotRowS.bCreate;
       lotRowN.bIsDeleted = lotRowS.bIsDeleted;
       lotRowN.iIdLotRow = lotRowS.iIdLotRow;
       lotRowN.id = lotRowS.id;
       lotRowN.sLot = lotRowS.sLot;
       lotRowN.tExpDate = lotRowS.tExpDate;

       rowN.addLotRow(lotRowN);
     }

     rowsCore.addRow(rowN);
     mov.addRow(rowN);
   }

   return mov;
}

function setMovementToForm() {
    for (var [key, oRow] of oMovement.rows) {
       oRow.lAuxlotRows = Array.from(oRow.lotRows);
    }
    oMovement.lAuxRows = Array.from(oMovement.rows);

    try {
      oMovement.lAuxlotsToCreate = lLotsToCreate;
    } catch (e) {
      oMovement.lAuxlotsToCreate = new Array();
    }

    localStorage.setItem('movement', JSON.stringify(oMovement));
    document.getElementById('movement_object').value = JSON.stringify(oMovement);
}

var oMovement = new SMovement(); // Initialization, the counter inits in 0
