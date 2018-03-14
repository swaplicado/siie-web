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
        document.getElementById('div_setdata').style.display = 'none';
    }

    showSetDataButton() {
        document.getElementById('div_setdata').style.display = 'inline';
    }

}

var guiTransSupp = new SGuiTransSupp();
