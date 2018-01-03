<?php

namespace App\SBarcode;
use App\WMS\SComponetBarcode;
use App\WMS\SWmsLot;
use App\WMS\SPallet;
use Laracasts\Flash\Flash;
use PDF;

  class SBarcode
  {

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

  public static function generateItemBarcode($dataBarcode,$data){
      $auxLot = "";
      $auxTextLot = "";
      $auxItem = "";
      $auxUnit = "";

      $numLot = $dataBarcode[1];
      $textLot = $dataBarcode[2];
      $numItem = $dataBarcode[3];
      $numUnit = $dataBarcode[4];

      $digitLot = strlen($data->id_lot);
      $digitItem = strlen($data->item_id);
      $digitUnit = strlen($data->unit_id);
      $digitTextLot = strlen($data->lot);

      $auxLot = SBarcode::fill($digitLot,$numLot);
      $auxTextLot = SBarcode::fill($digitTextLot,$textLot);
      $auxItem = SBarcode::fill($digitItem,$numItem);
      $auxUnit = SBarcode::fill($digitUnit,$numUnit);

      //dd($auxLot);
      //dd($auxTextLot);
      //dd($auxItem);
      //dd($auxUnit);
      $barcode = '1'.$auxLot.$data->id_lot.$auxTextLot.$data->lot.$auxItem.$data->item_id.$auxUnit.$data->unit_id;

      return $barcode;

  }



  public static function generatePalletBarcode($dataBarcode,$data){
      $auxPallet = "";
      $auxTextPallet = "";


      $numPallet = $dataBarcode[5];
      $textPallet = $dataBarcode[6];
      $numItem = $dataBarcode[7];
      $numUnit = $dataBarcode[8];



      $digitPallet = strlen($data->id_pallet);
      $digitTextPallet = strlen($data->pallet);
      $digitUnit = strlen($data->unit_id);
      $digitItem = strlen($data->item_id);



      $auxPallet = SBarcode::fill($digitPallet,$numPallet);
      $auxTextPallet = SBarcode::fill($digitTextPallet,$textPallet);
      $auxUnit = SBarcode::fill($digitUnit,$numUnit);
      $auxItem = SBarcode::fill($digitItem,$numItem);

      //dd($auxLot);
      //dd($auxTextLot);
      //dd($auxItem);
      //dd($auxUnit);
      $barcode = '2'.$auxPallet.$data->id_pallet.$auxTextPallet.$data->pallet.$auxItem.$data->item_id.$auxUnit.$data->unit_id;

      return $barcode;

  }

  public static function decodeBarcode($data){

    $type = substr($data, 0 , 1 );
    $code = substr($data,1);

    if($type==1)
    {
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Item')
                                      ->get()->lists('digits','id_component');
      $numLot = $dataBarcode[1];
      $numtextLot = $dataBarcode[2];
      $numItem = $dataBarcode[3];
      $numUnit = $dataBarcode[4];

      $idLot = substr($code, 0, $numLot);
      $textLot = substr($code, $numLot,$numtextLot);
      $idItem = substr($code, $numLot+$numtextLot, $numItem);
      $idUnit = substr($code, $numLot+$numtextLot+$numItem, $numUnit);

      $Lot = SBarcode::remove($numLot,$idLot);
      $Text = SBarcode::remove($numtextLot,$textLot);
      $Item = SBarcode::remove($numItem,$idItem);
      $Unit = SBarcode::remove($numUnit,$idUnit);

      $answer = SWmsLot::find($Lot);
      if($answer != null){

          if($answer->item_id == $Item && $answer->unit_id == $Unit)
          {
                return $answer;
          }
          $answer = null;
      }

      return $answer;



    }
    if($type==2)
    {
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Tarima')
                                      ->get()->lists('digits','id_component');


      $numPallet = $dataBarcode[5];
      $numtextPallet = $dataBarcode[6];
      $numItem = $dataBarcode[7];
      $numUnit = $dataBarcode[8];

      $idPallet = substr($code, 0, $numPallet);
      $textPallet = substr($code, $numPallet,$numtextPallet);
      $idItem = substr($code, $numPallet+$numtextPallet, $numItem);
      $idUnit = substr($code, $numPallet+$numtextPallet+$numItem, $numUnit);

      $Pallet = SBarcode::remove($numPallet,$idPallet);
      $Text = SBarcode::remove($numtextPallet,$textPallet);
      $Item = SBarcode::remove($numItem,$idItem);
      $Unit = SBarcode::remove($numUnit,$idUnit);

      $answer = SPallet::find($Pallet);

      if($answer != null){

        if($answer->item_id == $Item && $answer->unit_id == $Unit)
        {
          return $answer;
        }

        $answer = null;
      }

      return $answer;

    }

    $answer = null;
    return $answer;

  }

}
