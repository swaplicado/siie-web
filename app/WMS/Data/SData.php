<?php namespace App\WMS\Data;

/**
 * Object used for return data to client side from server
 */
class SData
{
    public $lItems = array();
    public $lLots = array();
    public $lPallets = array();

    public $lStock = array();
    public $dStock = 0;
    public $lPalletStock = array();

    public $lSrcLocations = array();
    public $lDesLocations = array();

    public $iFolioSrc = 0;
    public $iFolioDes = 0;

    public $oElement = null;
    public $iElementType = 404;

    public $lErrors = array();
    public $lNewLots = null;
    public $lLotRows = null;

    public $lRowsSupplied = array();
}
