function GlobalData (lots, locations, pallets, isInput) {
  this.lLots = lots;
  this.lPallets = pallets;
  this.lLocations = locations;
  this.bIsInputMov = isInput;
  this.IS_ITEM = 1;
  this.IS_LOT = 2;
  this.IS_PALLET = 3;
}

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

    addRow(row) {
      this.rows[this.idRow] = row;
      this.idRow++;
    }

    removeRow(ident) {
      if (ident in this.rows) {
        this.rows.splice(ident, 1);
      }
    }
}

/*
* Rows of movement
*/
class SMovementRow {
    constructor() {
      this.idLotRow = 0;
      this.iItemId = 0;
      this.iUnitId = 0;
      this.iPalletId = 0;
      this.iLocationId = 0;
      this.dQuantity = 0;
      this.dPrice = 0;
      this.bAuxIsLot = false;
      this.bAuxIsBulk = false;
      this.sAuxItemCode = '';

      this.lotRows = [];
    }

    get identifier() {
      return this.idLotRow
    }

    addLotRow(lotRow) {
      this.lotRows[this.idLotRow] = lotRow;
      this.idLotRow++;
    }

    removeLotRow(ident) {
      if (ident in this.lotRows) {
        this.lotRows.splice(ident, 1);
      }
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

/*
* This method sends the data of table to the server when
* the button of freeze is pressed
*/
function setData(data) {
    // var table = $('#example').tableToJSON();

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
