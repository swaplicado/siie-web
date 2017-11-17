/*
* Movement
*/
class SMovement {
    constructor() {
      this.idRow = 0;
      this.rows = [];
    }

    get rowIdentifier() {
      return this.idRow;
    }

    getRow(id) {
      id = parseInt(id);
      var mRow = null;

      this.rows.forEach(function(element) {
          if (element.iIdRow == id) {
              mRow = element;
              return true;
          }
      });

      return mRow;
    }

    addRow(row) {
      this.rows[this.idRow] = row;
      this.idRow++;
    }

    removeRow(ident) {
      this.rows = this.rows.filter(function( obj ) {
          return obj.iIdRow != ident;
      });
    }

    updateLocation(idRow, idLoc) {
      this.getRow(idRow).iLocationId = parseInt(idLoc);
    }

    updatePallet(idRow, idPallet) {
      this.getRow(idRow).iPalletId = parseInt(idPallet);
    }
}

/*
* Rows of movement
*/
class SMovementRow {
    constructor(idRow) {
      this.iIdRow = idRow;
      this.idLotRow = 0;
      this.iItemId = 0;
      this.iUnitId = 0;
      this.iPalletId = 0;
      this.iLocationId = 0;
      this.dQuantity = 0;
      this.dPrice = 0;
      this.oAuxItem = '';
      this.oAuxUnit = '';

      this.lotRows = [];
      this.aStock = [];
    }

    get identifier() {
      return this.idLotRow
    }

    getLotRow(id) {
      var lRow = null;
      id = parseInt(id);

      this.lotRows.forEach(function(element) {
          if (element.id == id) {
              lRow = element;
              return true;
          }
      });

      return lRow;
    }

    addLotRow(lotRow) {
      this.lotRows[this.idLotRow] = lotRow;
      this.idLotRow++;
    }

    removeLotRow(ident) {
      this.lotRows = this.lotRows.filter(function( obj ) {
          return obj.iIdRow !== ident;
      });
    }

    updateLot(id, idLot) {
      id = parseInt(id);
      idLot = parseInt(idLot);
      this.getLotRow(id).iLotId = idLot;
    }
}

/*
* Rows of RowMovement (Lots)
*/
class SLotRow {
    constructor(id, lot, qty, price) {
      this.id = id;
      this.iLotId = lot;
      this.dQuantity = qty;
      this.dPrice = price;
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
   mov.idRow = obj.idRow;
   mov.rows = obj.rows;

   return mov;
}

/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setData(data) {
    // var table = $('#example').tableToJSON();
    console.log(data);
    localStorage.setItem('movement', JSON.stringify(data));
    var data = { value : data };
      $.ajax({
        type: "POST",
        url: './create/storetable',
        data: data,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          console.log("Value added ");
        }
      });
}


var movement = new SMovement(); // Initialization, the counter inits in 0
