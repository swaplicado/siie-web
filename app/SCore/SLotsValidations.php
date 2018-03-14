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
    protected $oItem = null;

    /**
     * initialize the lots, lots ti create, the current item
     *
     * @param array  $theLots lots implied in row
     * @param array  $theLotsToCreate array of lots that will be created by the movement
     *
     * @param SItem $theItem  object of current SItem
     */
    public function __construct($theLots = [], $theLotsToCreate = [], $theItem = null) {
      $this->lErrors = array();
      $this->lLots = $theLots;
      $this->lLotsToCreate = $theLotsToCreate;
      $this->oItem = $theItem;
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
          if ($oLotC->lot == $oLotJs->{'sLot'}) {
              return $key;
          }
       }

       $oLot = new SWmsLot();
       $oLot->lot = $oLotJs->{'sLot'};
       $oLot->dt_expiry = $oLotJs->{'tExpDate'};
       $oLot->item_id = $this->oItem->id_item;
       $oLot->unit_id = $this->oItem->unit_id;

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
       $this->lLots[$iKey]->{'iLotId'} = $oLot->id_lot;
       $this->lLots[$iKey]->{'tExpDate'} = $oLot->dt_expiry;
    }

    /**
     * init the process of validation
     */
    public function validateLots()
    {
       foreach ($this->lLots as $key => $oLot) {
          if ($oLot->{'iLotId'} == '0') {
              $this->processLotToCreate($oLot, $key);
          }
          else {
             $oSearchLot = SWmsLot::find($oLot->{'iLotId'});

             if ($oLot->{'sLot'} != $oSearchLot->lot) {
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
        if ($oLotJs->{'bCreate'}) {
            if ($this->canCreateTheLot($this->oItem->gender->item_class_id)) {
                $oFoundLot = $this->lotExists($oLotJs->{'sLot'});

                if ($oFoundLot == null) {
                  $oLotJs->{'iLotId'} = 0;
                  $oLotJs->{'iKeyLot'} = $this->addToCreate($oLotJs);
                }
                else {
                  $this->addError('El lote '.$oLotJs->{'sLot'}.' ya existe');
                }
            }
            else {
                $this->addError('No tiene permisos para crear el lote '.$oLotJs->{'sLot'});
            }
        }
        else {
           $oFoundLot = $this->lotExists($oLotJs->{'sLot'});

           if ($oFoundLot != null) {
             if ($oFoundLot->is_deleted) {
                $this->addError('El lote '.$oLotJs->{'sLot'}.' está eliminado');
             }
             else {
                $this->assignLot($iKey, $oFoundLot);
             }
           }
           else {
             $this->addError('El lote '.$oLotJs->{'sLot'}.' no existe');
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
           if ($lot->item_id == $this->oItem->id_item && $lot->unit_id == $this->oItem->unit_id) {
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
}
