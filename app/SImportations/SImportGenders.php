<?php namespace App\SImportations;

use App\ERP\SItemGender;
use App\ERP\SItemGroup;

/**
 * this class import the data of item genders from siie
 */
class SImportGenders {
  protected $aClasses       = '';
  protected $aTypes         = '';
  protected $webusername;
  protected $webpassword;
  protected $webdbname;
  protected $webcon;

  /**
   * receive the name of host to connect
   * can be a IP or name of host
   *
   * @param string $sHost
   */
  function __construct($sHost)
  {
    $this->webusername = env("SIIE_DB_USER", "");
    $this->webpassword = env("SIIE_DB_PASS", "");
    $this->webdbname = env("SIIE_DB_NAME", "");

    $this->webcon = mysqli_connect(
      $sHost, $this->webusername,
      $this->webpassword, $this->webdbname
    );
    $this->webcon->set_charset("utf8");

    if (mysqli_connect_errno()) {
      echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    }

    $this->aClasses = [
      '1' => '2',
      // VENTAS
      '2' => '1',
      // ACTIVOS
      '3' => '1',
      // COMPRAS
      '4' => '3', // GASTOS
    ];

    $this->aTypes = [];
    //cat, class, type, webId
    array_push($this->aTypes, new TypesMap(1, 1, 1, 5)); // PRODUCTO
    array_push($this->aTypes, new TypesMap(1, 1, 2, 7)); // PRODUCTO TERMINADO
    array_push($this->aTypes, new TypesMap(1, 1, 3, 6)); // PRODUCTO EN PROCESO
    array_push($this->aTypes, new TypesMap(1, 1, 4, 8)); // SUBPRODUCTO
    array_push($this->aTypes, new TypesMap(1, 1, 5, 9)); // DESECHO
    array_push($this->aTypes, new TypesMap(1, 2, 1, 3)); // SERVICIO
    array_push($this->aTypes, new TypesMap(2, 1, 1, 3)); // ACTIVO
    array_push($this->aTypes, new TypesMap(3, 1, 1, 1)); // MATERIAL DIRECTO
    array_push($this->aTypes, new TypesMap(3, 1, 2, 3)); // MATERIAL INDIRECTO
    array_push($this->aTypes, new TypesMap(3, 2, 1, 10)); // GASTO COMPRA
    array_push($this->aTypes, new TypesMap(4, 1, 1, 1)); // MATERIA PRIMA PRODUCCIÃ“N
    array_push($this->aTypes, new TypesMap(4, 1, 2, 11)); // MANO DE OBRA PRODUCCIÃ“N
    array_push($this->aTypes, new TypesMap(4, 1, 3, 12)); // GASTO INDIRECTO PRODUCCIÃ“N
    array_push($this->aTypes, new TypesMap(4, 2, 1, 1)); // MATERIA PRIMA OPERACIÃ“N
    array_push($this->aTypes, new TypesMap(4, 2, 2, 12)); // MANO DE OBRA OPERACIÃ“N
    array_push($this->aTypes, new TypesMap(4, 2, 3, 12)); // GASTO INDIRECTO OPERACIÃ“N
  }

  /**
   * read the data  from siie, transform it, and saves it in the database
   *
   * @return integer number of records imported
   */
  public function importGenders()
  {
      $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.GENDERS'));

      $sql = "SELECT id_igen, igen,
                      b_len, b_len_variable,
                      b_surf, b_surf_variable,
                      b_vol, b_vol_variable,
                      b_mass, b_mass_variable,
                      b_lot, b_bulk, b_del,
                      fid_igrp, fid_ct_item, fid_cl_item, fid_tp_item,
                      ts_new, ts_edit, ts_del FROM itmu_igen
                      WHERE
                      ts_new > '".$oImportation->last_importation."' OR
                      ts_edit > '".$oImportation->last_importation."' OR
                      ts_del > '".$oImportation->last_importation."'
                      ";

      $result = $this->webcon->query($sql);
      // $this->webcon->close();

      $lSiieGenders = array();
      $lWebGenders = SItemGender::get();
      $lGroups = SItemGroup::get();
      $lWebGroups = array();
      $lGenders = array();
      $lGendersToWeb = array();

      foreach ($lWebGenders as $key => $value) {
          $lGenders[$value->external_id] = $value;
      }

      foreach ($lGroups as $group) {
          $lWebGroups[$group->external_id] = $group->id_item_group;
      }

      if ($result->num_rows > 0) {
         // output data of each row
         while($row = $result->fetch_assoc()) {
             if (array_key_exists($row["id_igen"], $lGenders)) {
                if ($row["ts_edit"] > $lGenders[$row["id_igen"]]->updated_at ||
                      $row["ts_del"] > $lGenders[$row["id_igen"]]->updated_at) {

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
                    $lGenders[$row["id_igen"]]->item_class_id = SImportGenders::getClassId($this->aClasses, $row["fid_ct_item"]);
                    $lGenders[$row["id_igen"]]->item_type_id = SImportGenders::getTypeId($this->aTypes, $row["fid_ct_item"], $row["fid_cl_item"], $row["fid_tp_item"]);
                    $lGenders[$row["id_igen"]]->updated_at = $row["ts_edit"] > $row["ts_del"] ? $row["ts_edit"] : $row["ts_del"];

                    array_push($lGendersToWeb, $lGenders[$row["id_igen"]]);
                }
             }
             else {
                array_push($lGendersToWeb, SImportGenders::siieToSiieWeb($row, $lWebGroups, $this->aClasses, $this->aTypes));
             }
         }
      }

      foreach ($lGendersToWeb as $key => $oGender) {
         $oGender->save();
      }

      SImportUtils::saveImportation($oImportation);

      return sizeof($lGendersToWeb);
  }

  /**
   * Transform a siie object to siie-web object
   *
   * @param  Object $oSiieGender
   * @param  array  $lGroups  array of item groups to map siie to siie-web groups
   * @param  array  $lClasses  array of item classes to map siie to siie-web classes
   * @param  array  $lTypes  array of item types to map siie to siie-web types
   *
   * @return SItemGender
   */
  private static function siieToSiieWeb($oSiieGender = '', $lGroups = [], $lClasses = [], $lTypes = [])
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

  /**
   * Obtain the item class id based on map from siie
   *
   * @param  array   $aClassMap
   * @param  integer $iCategoryId category of item from siie
   * @return integer class id from siie-web
   */
  private static function getClassId($aClassMap = [], $iCategoryId = 1)
  {
      foreach ($aClassMap as $key => $rClass) {
         if ($iCategoryId == $key) {
           return $rClass;
         }
      }
  }

  /**
   * Obtain the type siie-web id of item based in category, class and type of siie
   *
   * @param  array $aTypeMap
   * @param  integer $iCategoryId
   * @param  integer $iClassId
   * @param  integer $iTypeId
   *
   * @return integer equivalent primary key of movement type
   */
  private static function getTypeId($aTypeMap = [], $iCategoryId = 0,
                                      $iClassId = 0, $iTypeId = 0)
  {
      foreach ($aTypeMap as $oType) {
         if ($oType->iCategory == $iCategoryId && $oType->iClass == $iClassId && $oType->iType == $iTypeId) {
            return $oType->iEquiv;
         }
      }

      return 1;
  }
}

/**
 * Object to map the document type
 */
class TypesMap {
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
