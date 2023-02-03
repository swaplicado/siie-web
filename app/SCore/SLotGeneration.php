<?php

namespace App\SLotGeneration;

  class SLotGeneration
  {

    public static function  createLot($product,$date,$identifier)
    {
      $middlePart = datePart($date);
      $firtsPart = firstLetter($product);

      $Lot = $firtsPart.$middlePart.$identifier;

    }

    public static function datePart($date)
    {
      $digitYear = substr($date,2,4);
      $digitMonth = substr($date,4,5);
      $digitDay = substr($date,6,7);

      $aux = $digitYear.$digitMonth.$digitDay;
      return $aux;
    }

    public static function firtsLetter($product)
    {
      $Letter = substr($product,0,1);

      return $Letter;
    }

  }
?>
