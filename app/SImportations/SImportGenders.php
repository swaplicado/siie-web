<?php namespace App\SImportations;

use App\ERP\SItemGender;
use App\ERP\SItemGroup;

/**
 *
 */
class SImportGenders
{
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = 'erp';
  protected $webcon         = '';

  protected $aClasses       = '';
  protected $aTypes         = '';

  function __construct($sHost)
  {
      $this->webcon = mysqli_connect($sHost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }

      $this->aCatClass = [
                        '1' => '2', // VENTAS
                        '2' => '1', // ACTIVOS
                        '3' => '1', // COMPRAS
                        '4' => '3', // GASTOS
                    ];

      $this->aTypes = [];
      array_push($this->aTypes, new TypesMap(1,1,1,5)); // PRODUCTO
      array_push($this->aTypes, new TypesMap(1,1,2,7)); // PRODUCTO TERMINADO
      array_push($this->aTypes, new TypesMap(1,1,3,4)); // PRODUCTO EN PROCESO
      array_push($this->aTypes, new TypesMap(1,1,4,8)); // SUBPRODUCTO
      array_push($this->aTypes, new TypesMap(1,1,5,9)); // DESECHO
      array_push($this->aTypes, new TypesMap(1,2,1,3)); // SERVICIO
      array_push($this->aTypes, new TypesMap(2,1,1,3)); // ACTIVO
      array_push($this->aTypes, new TypesMap(3,1,1,1)); // MATERIAL DIRECTO
      array_push($this->aTypes, new TypesMap(3,1,2,3)); // MATERIAL INDIRECTO
      array_push($this->aTypes, new TypesMap(3,2,1,10)); // GASTO COMPRA
      array_push($this->aTypes, new TypesMap(4,1,1,1)); // MATERIA PRIMA PRODUCCIÃ“N
      array_push($this->aTypes, new TypesMap(4,1,2,11)); // MANO DE OBRA PRODUCCIÃ“N
      array_push($this->aTypes, new TypesMap(4,1,3,12)); // GASTO INDIRECTO PRODUCCIÃ“N
      array_push($this->aTypes, new TypesMap(4,2,1,1)); // MATERIA PRIMA OPERACIÃ“N
      array_push($this->aTypes, new TypesMap(4,2,2,12)); // MANO DE OBRA OPERACIÃ“N
      array_push($this->aTypes, new TypesMap(4,2,3,12)); // GASTO INDIRECTO OPERACIÃ“N

  }

  public function importGenders()
  {
      $sql = "SELECT id_igen, igen,
                      b_len, b_len_variable,
                      b_surf, b_surf_variable,
                      b_vol, b_vol_variable,
                      b_mass, b_mass_variable,
                      b_lot, b_bulk, b_del,
                      fid_igrp, fid_ct_item, fid_cl_item, fid_tp_item,
                      ts_new, ts_edit, ts_del FROM itmu_igen";

      $result = $this->webcon->query($sql);
      $lSiieGenders = array();
      $lWebGenders = SItemGender::get();
      $lGroups = SItemGroup::get();
      $lWebGroups = array();
      $lGenders = array();
      $lGendersToWeb = array();

      foreach ($lWebGenders as $key => $value)
      {
          $lGenders[$value->external_id] = $value;
      }

      foreach ($lGroups as $group) {
          $lWebGroups[$group->external_id] = $group->id_item_group;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_igen"], $lGenders))
             {
                if ($row["ts_edit"] > $lGenders[$row["id_igen"]]->updated_at ||
                      $row["ts_del"] > $lGenders[$row["id_igen"]]->updated_at)
                {
                    $lGenders[$row["id_igen"]]->name = $row["igen"];
                    $lGenders[$row["id_igen"]]->external_id = $row["id_igen"];
                    $lGenders[$row["id_igen"]]->is_length = $row["b_len"];
                    $lGenders[$row["id_igen"]]->is_length_var = $row["b_len_variable"];
                    $lGenders[$row["id_igen"]]->is_surface = $row["b_surf"];
                    $lGenders[$row["id_igen"]]->is_surface_var = $row["b_surf_variable"];
                    $lGenders[$row["id_igen"]]->is_volume = $row["b_vol"];
                    $lGenders[$row["id_igen"]]->is_volume_var = $row["b_vol_variable"];
                    $lGenders[$row["id_igen"]]->is_mass = $row["b_mass"];
                    $lGenders[$row["id_igen"]]->is_mass_var = $row["b_mass_variable"];
                    $lGenders[$row["id_igen"]]->is_lot = $row["b_lot"];
                    $lGenders[$row["id_igen"]]->is_bulk = $row["b_bulk"];
                    $lGenders[$row["id_igen"]]->is_deleted = $row["b_del"];
                    $lGenders[$row["id_igen"]]->item_group_id = $lWebGroups[$row["fid_igrp"]];
                    $lGenders[$row["id_igen"]]->item_class_id = SImportGenders::getClassId($this->aCatClass, $row["fid_ct_item"]);
                    $lGenders[$row["id_igen"]]->item_type_id = SImportGenders::getTypeId($this->aTypes, $row["fid_ct_item"], $row["fid_cl_item"], $row["fid_tp_item"]);
                    $lGenders[$row["id_igen"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lGendersToWeb, $lGenders[$row["id_igen"]]);
                }
             }
             else
             {
                array_push($lGendersToWeb, SImportGenders::siieToSiieWeb($row, $lWebGroups, $this->aCatClass, $this->aTypes));
             }
         }
      }
      else
      {
         echo "0 results";
      }

      foreach ($lGendersToWeb as $key => $oGender) {
         $oGender->save();
      }

      $this->webcon->close();

      return sizeof($lGendersToWeb);
  }

  private static function siieToSiieWeb($oSiieGender = '', $lGroups, $lClasses, $lTypes)
  {
     $oGender = new SItemGender();
     $oGender->name = $oSiieGender["igen"];
     $oGender->external_id = $oSiieGender["id_igen"];
     $oGender->is_length = $oSiieGender["b_len"];
     $oGender->is_length_var = $oSiieGender["b_len_variable"];
     $oGender->is_surface = $oSiieGender["b_surf"];
     $oGender->is_surface_var = $oSiieGender["b_surf_variable"];
     $oGender->is_volume = $oSiieGender["b_vol"];
     $oGender->is_volume_var = $oSiieGender["b_vol_variable"];
     $oGender->is_mass = $oSiieGender["b_mass"];
     $oGender->is_mass_var = $oSiieGender["b_mass_variable"];
     $oGender->is_lot = $oSiieGender["b_lot"];
     $oGender->is_bulk = $oSiieGender["b_bulk"];
     $oGender->is_deleted = $oSiieGender["b_del"];
     $oGender->item_group_id = $lGroups[$oSiieGender["fid_igrp"]];
     $oGender->item_class_id = SImportGenders::getClassId($lClasses, $oSiieGender["fid_ct_item"]);
     $oGender->item_type_id = SImportGenders::getTypeId($lTypes, $oSiieGender["fid_ct_item"], $oSiieGender["fid_cl_item"], $oSiieGender["fid_tp_item"]);
     $oGender->created_by_id = 1;
     $oGender->updated_by_id = 1;
     $oGender->created_at = $oSiieGender["ts_new"];
     $oGender->updated_at = $oSiieGender["ts_edit"];

     return $oGender;
  }

  private static function getClassId($aClassMap, $iCategoryId = 1)
  {
      foreach ($aClassMap as $key => $rClass)
      {
         if ($iCategoryId == $key)
         {
           return $rClass;
         }
      }
  }

  private static function getTypeId($aTypeMap, $iCategoryId, $iClassId, $iTypeId)
  {
      foreach ($aTypeMap as $oType)
      {
         if ($oType->iCategory == $iCategoryId && $oType->iClass == $iClassId && $oType->iType = $iTypeId)
         {
            return $oType->iEquiv;
         }
      }
  }
}

/**
 *
 */
class TypesMap
{
  public $iCategory = 0;
  public $iClass = 0;
  public $iType = 0;
  public $iEquiv = 0;

  function __construct($iCategory, $iClass, $iType, $iEquiv)
  {
    $this->iCategory = $iCategory;
    $this->iClass = $iClass;
    $this->iType = $iType;
    $this->iEquiv = $iEquiv;
  }
}
