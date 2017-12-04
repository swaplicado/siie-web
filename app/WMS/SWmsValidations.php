<?php namespace App\WMS;

use App\WMS\SFolio;

/**
 *
 */
class SWmsValidations
{
    /**
     * [validateFolios description]
     * @param  string  $oFolio   [description]
     * @param  boolean $isUpdate [description]
     * @return [type]            [description]
     */
    public static function validateFolios($oFolio = '', $isUpdate = false)
    {
        $aErrors = array();

        $lFolios = SFolio::where('container_type_id', $oFolio->container_type_id)
                              ->where('container_id', $oFolio->container_id)
                              ->where('mvt_class_id', $oFolio->mvt_class_id)
                              ->where('mvt_type_id', $oFolio->mvt_type_id)
                              ->where('is_deleted', $oFolio->is_deleted);
       if ($isUpdate)
       {
         $lFolios->where('id_container_folio', '!=', $oFolio->id_container_folio);
       }

       $lFolios = $lFolios->get();

       if (sizeof($lFolios) > 0)
       {
          array_push($aErrors, 'Ya existe un folio asignado a esta combinación');
       }

       return $aErrors;
    }

}
