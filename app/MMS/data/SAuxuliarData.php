<?php namespace App\MMS\data;

/**
 * Auxiliar Data
 */
class SAuxuliarData
{

  public $value = null;
  public $dQuantity = 0;

  function __construct($oValue, $dQty)
  {
     $this->value = $oValue;
     $this->dQuantity = $dQty;
  }
}
