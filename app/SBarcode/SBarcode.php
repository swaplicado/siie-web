<?php namespace App\SBarcode;
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

  public static function generateItemBarcode($dataBarcode,$dataLot){
      $auxLot = "";
      $auxTextLot = "";
      $auxItem = "";
      $auxUnit = "";

      $numLot = $dataBarcode[1];
      $textLot = $dataBarcode[2];
      $numItem = $dataBarcode[3];
      $numUnit = $dataBarcode[4];

      $digitLot = strlen($dataLot->id_lot);
      $digitItem = strlen($dataLot->item_id);
      $digitUnit = strlen($dataLot->unit_id);
      $digitTextLot = strlen($dataLot->name);

      $auxLot = SBarcode::fill($digitLot,$numLot);
      $auxTextLot = SBarcode::fill($digitTextLot,$textLot);
      $auxItem = SBarcode::fill($digitItem,$numItem);
      $auxUnit = SBarcode::fill($digitUnit,$numUnit);

      //dd($auxLot);
      //dd($auxTextLot);
      //dd($auxItem);
      //dd($auxUnit);
      $barcode = '1'.$auxLot.$dataLot->id_lot.$auxTextLot.$dataLot->name.$auxItem.$dataLot->item_id.$auxUnit.$dataLot->unit_id;

      return $barcode;

  }

  public static function generatePalletBarcode($dataBarcode,$dataLot){
      $auxPallet = "";
      $auxTextPallet = "";


      $numPallet = $dataBarcode[5];
      $textPallet = $dataBarcode[6];


      $digitPallet = strlen($dataLot->id_pallet);
      $digitTextPallet = strlen($dataLot->pallet);


      $auxPallet = SBarcode::fill($digitPallet,$numPallet);
      $auxTextPallet = SBarcode::fill($digitTextPallet,$textPallet);

      //dd($auxLot);
      //dd($auxTextLot);
      //dd($auxItem);
      //dd($auxUnit);
      $barcode = '2'.$auxPallet.$dataLot->id_pallet.$auxTextPallet.$dataLot->pallet;

      return $barcode;

  }

}
?>
