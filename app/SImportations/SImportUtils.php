<?php namespace App\SImportations;

use App\ERP\SImportation;
use Carbon\Carbon;

class SImportUtils {

      public static function getImportationObject($iImportationType = 0)
      {
         return SImportation::find($iImportationType);
      }

      public static function saveImportation($oImportation = null)
      {
          $date = Carbon::now(new \DateTimeZone('America/Mexico_City'));
          $date = $date->subMinutes(15);

          $oImportation->last_importation = $date->format('Y-m-d h:i:s');
          $oImportation->updated_by_id = \Auth::user()->id;

          $oImportation->save();
      }
}
