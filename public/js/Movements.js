/*
* Movement
*/
class SMovement {

    constructor(counter) {
      this.counter = counter;
    }

    get identifier() {
      return this.counter
    }

    addRow() {
      this.counter++
    }
}

/*
* Rows of movement
*/
class SMovementRow {
    constructor(counter) {
      this.counter = identifier;
    }

    get identifier() {
      return this.counter
    }

    addLotRow() {
      this.counter++
    }
}

/*
* Contains the tables of the Movement
*/
class Tables {
  constructor() {
      this.rowsTable = '';
      this.lotRows = [];
      this.lotTables = [];
      this.counter = 0;
  }

  get numRows() {
    return this.counter
  }

  addRow(id, row, table) {
    if (id in this.lotTables && id in this.lotRows) {
      this.lotRows[id] = row;
      this.lotTables[id] = table;
      console.log("replaced " + id);
    }
    else {
      this.lotRows[id] = row;
      this.lotTables[id] = table;
      this.counter++
      console.log("added " + id);
    }
  }

  setTable(table) {
    this.rowsTable = table;
  }
}

class ServerData {
  constructor(lotTables, movementTable) {
    this.lotTables = lotTables;
    this.movementTable = movementTable;
  }
}


var movement = new SMovement(0); // Initialization, the counter inits in 0
var datas = new Tables();
var lotsCounter = 0;

function validateMovement(tableMov) {
  if (typeof tableMov[0] == 'undefined') {
    return false;
  }
  if (tableMov[0].Cantidad == "No se encontraron registros.")
  {
    alert("No hay renglones");
    return false;
  }
  if (datas.lotRows.length == 0)
  {
    alert("Debe asignar lotes a los renglones.");
    return false;
  }

  if (($("#example tr").length - 1) > datas.lotRows.length) {
    alert("No todos los renglones tienen lotes asignados.");
    return false;
  }

  return true;
}
