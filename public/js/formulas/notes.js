/**
 * Note
 *
 * is the estructure of the note object
 */
class Note {
    constructor() {
      this.nuNote = 0;
      this.iIdNote = 0;

      this.sNote = 0;
      this.bIsDeleted = false;
    }
}

var oSelectedNote = null;

/**
 * is invoked when a key on text area is released
 * get the value of text area and transform it to uppercase
 * if the length of text is more than 0 the save button is enabled
 *
 */
function changeNote() {
   var sNote = document.getElementById('note_area').value;
   document.getElementById('note_area').value = sNote.toUpperCase();
   document.getElementById('btnSaveNote').disabled = sNote.length == 0;
}

/**
 * set the text of note to text area
 * disable the new button
 */
function setNote() {
   var row = oNotesTable.row('.selected').data();
   var oNote = oData.jsFormula.getNote(row[0]);
   oSelectedNote = oNote;

   document.getElementById('note_area').value = oNote.sNote;
   document.getElementById('btnEditNote').disabled = false;
   document.getElementById('btnDelNote').disabled = false;
   document.getElementById('note_area').readOnly = true;
}

/**
 * enable the textarea and the save button
 */
function editNote() {
  if (oSelectedNote != null) {
    document.getElementById('note_area').readOnly = false;
    document.getElementById('btnSaveNote').disabled = false;
  }
}

/**
 * clean the textarea and enable it
 * reset the selected row
 */
function newNote() {
  document.getElementById('note_area').value = '';
  document.getElementById('note_area').readOnly = false;
  document.getElementById('btnDelNote').disabled = true;
  document.getElementById('btnEditNote').disabled = true;

  oSelectedNote = null;
}

/**
 * adds or modify the note to the table and to array of notes
 * reset the values of window
 */
function saveNote() {
  var sText = document.getElementById('note_area').value;

  if (oSelectedNote == null) {
    note = new Note();
    note.sNote = sText;

    oData.jsFormula.addNote(note);

    oNotesTable.row.add([
        note.nuNote,
        note.iIdNote,
        note.sNote
    ]).draw( false );
  }
  else {
    oData.jsFormula.getNote(oSelectedNote.nuNote).sNote = sText;
    $('#notes_table').dataTable().fnUpdate(sText , oSelectedNote.nuNote, 2);
  }

  initNote();
}

/**
 * quit the note of table and array
 * if the note already exists in DB only
 * put the var is_deleted to true
 */
function deleteNote() {
  if (oSelectedNote != null) {
    oData.jsFormula.removeNote(oSelectedNote.nuNote);
    oNotesTable.row('.selected').remove().draw( false );
    initNote();
  }
}

/**
 * initializes the values of the window
 */
function initNote() {
  document.getElementById('note_area').value = '';
  document.getElementById('btnNewNote').disabled = false;
  document.getElementById('btnEditNote').disabled = true;
  document.getElementById('btnSaveNote').disabled = true;
  document.getElementById('btnDelNote').disabled = true;

  oSelectedNote = null;
}
