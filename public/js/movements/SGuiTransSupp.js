/**
 * this class is responsible for
 * visual elements of the transfers
 */
class SGuiTransSupp {

    /**
     * hide the button to set data to row in transfers or
     * supplied
     */
    hideSetDataButton() {
        try {
          document.getElementById('div_setdata').style.display = 'none';
        }
        catch (e) {
          console.log(e);
        }
    }

    showSetDataButton() {
        try {
          document.getElementById('div_setdata').style.display = 'inline';
        }
        catch (e) {
          console.log(e);
        }
    }

    /**
     * hide the button to set data to row in transfers or
     * supplied
     */
    hideCleanDataButton() {
        try {
          document.getElementById('div_cleandata').style.display = 'none';
        }
        catch (e) {
          console.log(e);
        }
    }

    showCleanDataButton() {
        try {
          document.getElementById('div_cleandata').style.display = 'inline';
        }
        catch (e) {
          console.log(e);
        }
    }

}

var guiTransSupp = new SGuiTransSupp();
