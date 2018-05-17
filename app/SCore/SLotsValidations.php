<?php namespace App\SCore;

use App\WMS\SWmsLot;
use App\ERP\SErpConfiguration;

use Carbon\Carbon;

/**
 * this class contains functions of validation
 * for lotes used by movements
 */
class SLotsValidations {

    protected $lLotsToCreate = null;
    protected $lErrors = null;
    protected $lLots = null;
    protected $iItem = 0;
    protected $iUnit = 0;

    /**
     * initialize the lots, lots ti create, the current item
     *
     * @param array  $theLots lots implied in row
     * @param array  $theLotsToCreate array of lots that will be created by the movement
     * @param int $theItemId id of current Item
     * @param int $theUnitId id of current Unit
     */
    public function __construct($theLots = [], $theLotsToCreate = [], $theItemId = 0, $theUnitId = 0) {
      $this->lErrors = array();
      $this->lLots = $theLots;
      $this->lLotsToCreate = $theLotsToCreate;
      $this->iItem = $theItemId;
      $this->iUnit = $theUnitId;
    }

    /**
     * get the array of lots to create
     *
     * @return array
     */
    public function getLotsToCreate()
    {
      return $this->lLotsToCreate;
    }

    /**
     * get the array of errors string produced by the process
     *
     * @return array
     */
    public function getErrors()
    {
      return $this->lErrors;
    }

    /**
     * get the array of primary lots
     *
     * @return array
     */
    public function getLots()
    {
      return $this->lLots;
    }

    /**
     * create a new SLot object and add it to array of
     * lots to create
     */
    private function addToCreate($oLotJs = null)
    {
       foreach ($this->lLotsToCreate as $key => $oLotC) {
          if ($oLotC->lot == $oLotJs->sLot) {
              return $key;
          }
       }

       $oLot = new SWmsLot();
       $oLot->lot = $oLotJs->sLot;
       $oLot->dt_expiry = $oLotJs->tExpDate;
       $oLot->item_id = $this->iItem;
       $oLot->unit_id = $this->iUnit;

       $iPKey = array_push($this->lLotsToCreate, $oLot);

       return ($iPKey - 1);
    }

    /**
     * Adds to array of errors a new error
     *
     * @param string $sError [description]
     */
    private function addError($sError = '')
    {
       array_push($this->lErrors, $sError);
    }

    /**
     * assign the lot and the expiration date to lot
     * when this was searched on server side
     *
     * @param  integer $iKey key of array
     * @param  oLotJs  $oLotJs the lot object obtained from client side
     */
    private function assignLot($iKey = 0, $oLot = null)
    {
       $this->lLots[$iKey]->iLotId = $oLot->id_lot;
       $this->lLots[$iKey]->tExpDate = $oLot->dt_expiry;
    }

    /**
     * init the process of validation
     */
    public function validateLots()
    {
       foreach ($this->lLots as $key => $oLot) {
          if ($oLot->iLotId == '0') {
              $this->processLotToCreate($oLot, $key);
          }
          else {
             $oSearchLot = SWmsLot::find($oLot->iLotId);

             if ($oLot->sLot != $oSearchLot->lot) {
                $this->processLotToCreate($oLot, $key);
             }
          }
       }
    }

    /**
     * process the lot to know if will be created,
     * assigned or the process produce an error
     *
     * @param  oLotJs  $oLotJs the lot object obtained from client side
     * @param  integer $iKey key of the array
     */
    private function processLotToCreate($oLotJs = null, $iKey = 0)
    {
        $oFoundLot = $this->lotExists($oLotJs->sLot);

        if ($oFoundLot == null) {
          $oLotJs->iLotId = 0;
          $oLotJs->iKeyLot = $this->addToCreate($oLotJs);
        }
        else {
          if ($oFoundLot->dt_expiry == $oLotJs->tExpDate) {
              $this->assignLot($iKey, $oFoundLot);
          }
          else {
              $this->addError('El lote '.$oLotJs->sLot.' ya existe, pero
                              la fecha de vencimiento no coincide.');
          }
        }
    }

    /**
     *  Validate if the lot exists comparing the text of lot and the id of
     *  item and unit. Take these elements from global Item
     *
     * @param string $sLot text of lot
     * @return SWmsLot  a Lot object if the lot was found or null if was not found
     */
    private function lotExists($sLot = '')
    {
        $oSearchLot = SWmsLot::where('lot', $sLot)->get();

        $oFoundLot = null;
        foreach ($oSearchLot as $lot) {
           if ($lot->item_id == $this->iItem && $lot->unit_id == $this->iUnit) {
             $oFoundLot = $lot;
             break;
           }
        }

        return $oFoundLot;
    }

    /**
     * Determine if the lot o pallet can be created from the class
     * the configuration is read from the database
     *
     * @param  integer $iItemClass class of item, can be:
     *                             \Config::get('scsiie.ITEM_CLS.MATERIAL')
     *                             \Config::get('scsiie.ITEM_CLS.PRODUCT')
     *
     * @return boolean  true if the configuration has the permission to create the element
     */
    private function canCreateTheLot($iItemClass = 0)
    {
        $bCanCreateToMat = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.CAN_CREATE_LOT_PAL_MAT'))->val_boolean;
        $bCanCreateToProd = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.CAN_CREATE_LOT_PAL_PROD'))->val_boolean;

        if ($iItemClass == \Config::get('scsiie.ITEM_CLS.MATERIAL')) {
            return $bCanCreateToMat;
        }
        elseif ($iItemClass == \Config::get('scsiie.ITEM_CLS.PRODUCT')) {
            return $bCanCreateToProd;
        }

        return false;
    }

    public function validatelotsByExpiration($iMvtType = 0, $iPartner = 0, $iAddress = 0)
    {
        // $sSelect = 'wm.id_mvt,
        //             wmr.id_mvt_row,
        //             wmrl.id_mvt_row_lot,
        //             wmr.item_id,
        //             wmr.unit_id,
        //             wmrl.lot_id,
        //             wl.lot,
        //             wl.dt_expiry,
        //             eba.id_branch_address,
        //             eba.name,
        //             eba.street,
        //             eba.locality';

        $lLastLot = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_mvts AS wm')
                      ->join('wms_mvt_rows AS wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                      ->join('wms_mvt_row_lots AS wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                      ->join('wms_lots AS wl', 'wmrl.lot_id', '=', 'wl.id_lot')
                      ->join('erpu_documents AS ed', 'wm.doc_invoice_id', '=', 'ed.id_document')
                      ->join('erpu_branch_addresses AS eba', 'ed.address_id', '=', 'eba.id_branch_address')
                      ->select('wm.id_mvt', 'wmrl.lot_id', 'wl.lot', 'wl.dt_expiry', 'eba.name')
                      ->where('wmr.item_id', $this->iItem)
                      ->where('wmr.unit_id', $this->iUnit)
                      ->where('wm.mvt_whs_type_id', $iMvtType)
                      ->where('ed.partner_id', $iPartner)
                      ->where('eba.id_branch_address', $iAddress)
                      ->groupBy(['eba.id_branch_address', 'lot_id'])
                      ->orderBy('dt_expiry', 'DESC')
                      ->take(1)
                      ->get();

        if (sizeof($lLastLot) > 0) {
          $tLastLotDate = Carbon::parse($lLastLot[0]->dt_expiry);

          foreach ($this->lLots as $oLot) {
             $lotExpDate = Carbon::parse($oLot->tExpDate);

             if ($tLastLotDate->gt($lotExpDate)) {
                $this->addError('La fecha de vencimiento del último lote del producto
                                entregado a este cliente en el centro: '.$lLastLot[0]->name.'
                                es '.$lLastLot[0]->dt_expiry.', no se pueden entregar lotes con una
                                fecha de vencimiento anterior');
                break;
             }
          }
        }
    }
}
