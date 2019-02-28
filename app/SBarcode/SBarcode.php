<?php

namespace App\SBarcode;
use App\WMS\SComponetBarcode;
use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\ERP\SItem;
use Laracasts\Flash\Flash;
use PDF;

  class SBarcode
  {

  /**
   * [fill description]
   * To have a certain size string
   * @param  integer $lower  start of string
   * @param  integer $higher end of string
   * @return string        string with certain size
   */
  public static function fill($lower,$higher)
    {
      $aux = "";

      while($lower<$higher)
      {
        $aux = '0'.$aux;
        $lower += 1;
      }

      return $aux;
    }

  /**
   * [remove description]
   * Remove 0 to have the original string
   * @param  integer $numberDigits number of digits that must have the string
   * @param  string $data         part of the barcode
   * @return string       string without 0
   */
  public static function remove($numberDigits, $data)
    {
      $answer="";
      for($i=0;$i<$numberDigits;$i++) {
        $aux = substr($data, 0 , $i+1 );
        if($aux != '0')
        {
          $answer = substr($data, $i);
          break;
        }
      }

      return $answer;
    }

  /**
   * [generateItemBarcode description]
   * Generate barcode
   * @param  array $dataBarcode data that will make up the barcode
   * @param  SComponetBarcode $data       numer of digits of the components of the barcode
   * @return string              barcode
   */
  public static function generateItemBarcode($dataBarcode,$data){
      $auxLot = "";
      $numLot = $dataBarcode[1];
      $digitLot = strlen($data->id_lot);
      $auxLot = SBarcode::fill($digitLot,$numLot);

      $barcode = '1'.$auxLot.$data->id_lot;

      return $barcode;

  }

  /**
   * [generatePalletBarcode description]
   * Generate barcode
   * @param  array $dataBarcode data that will make up the barcode
   * @param  SComponetBarcode $data       numer of digits of the components of the barcode
   * @return string              barcode
   */
  public static function generatePalletBarcode($dataBarcode,$data){
      $auxPallet = "";
      $numPallet = $dataBarcode[5];
      $digitPallet = strlen($data->id_pallet);
      $auxPallet = SBarcode::fill($digitPallet,$numPallet);

      $barcode = '2'.$auxPallet.$data->id_pallet;

      return $barcode;

  }

  public static function generateLocationBarcode($dataBarcode,$data){
      $numWhs = $dataBarcode[9];
      $numLoc = $dataBarcode[10];

      $digitWhs = strlen($data->whs_id);
      $digitLoc = strlen($data->id_whs_location);

      $auxWhs = SBarcode::fill($digitWhs,$numWhs);
      $auxLoc = SBarcode::fill($digitLoc,$numLoc);

      $barcode = '3'.$auxWhs.$data->whs_id.$auxLoc.$data->id_whs_location;

      return $barcode;
  }

  /**
   * [decodeBarcode description]
   * @param  String $data string of barcode
   * @return SItem,SWLot,SPallet      depending on the barcode
   */
  public static function decodeBarcode($data){
    //If $data is a code of item return SITem

    $answer = SItem::where('code',$data)->first();

    if ($answer == null) {
      $answer = sPallet::where('id_pallet',$data)->first();
    }

    if ($answer == null) {
      $answer = SWmsLot::where('lot', $data)->orderBy('created_at', 'DESC')->first();
    }

    if ($answer == null) {
    //$type can be
    //1= lots
    //2= Pallets
    //3= Locations
    $type = substr($data, 0 , 1 );
    $code = substr($data,1);

    //barcode Lots
    if($type==1)
    {
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Item')
                                      ->get()->lists('digits','id_component');
      $numLot = $dataBarcode[1];
      $idLot = substr($code, 0, $numLot);
      $Lot = SBarcode::remove($numLot,$idLot);

      $answer = SWmsLot::find($Lot);

      return $answer;

    }
    //barcode Pallets
    if($type==2)
    {
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Tarima')
                                      ->get()->lists('digits','id_component');


      $numPallet = $dataBarcode[5];
      $idPallet = substr($code, 0, $numPallet);
      $Pallet = SBarcode::remove($numPallet,$idPallet);
      $answer = SPallet::find($Pallet);

      return $answer;

    }
    //barcode Locations
    if($type==3)
    {
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Ubicacion')
                                      ->get()->lists('digits','id_component');


      $numWhs = $dataBarcode[9];
      $numLoc = $dataBarcode[10];

      $idWhs = substr($code, 0, $numWhs);
      $idLoc = substr($code, $numWhs,$numLoc);

      $Whs = SBarcode::remove($numWhs,$idWhs);
      $Loc = SBarcode::remove($numLoc,$idLoc);

      $answer = SLocation::find($Loc);

      return $answer;

    }
  }
    return $answer;

  }

}
