<?php namespace App\SUtils;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\SUtils\SConnectionUtils;
use App\ERP\SErpConfiguration;

use App\User;
use App\ERP\SYear;
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\SYS\SCompany;
use App\ERP\SPartner;

class SSessionUtils {

  /**
   * determines if the user is super depending of the context
   * for the administration area could be \Config::get('scsys.TP_USER.ADMIN')
   * for the company manager area could be \Config::get('scsys.TP_USER.MANAGER')
   *
   * @param  App\User  $oUser
   * @return boolean
   */
  public function isSuperUser($oUser)
  {
      return $oUser->user_type_id == \Config::get('scsys.TP_USER.MANAGER') ||
              $oUser->user_type_id == \Config::get('scsys.TP_USER.ADMIN');
  }

  /**
   * Validate if the current user has the permission to edit
   *
   * @param  int $iPrivilege
   * @param  object $oRecord
   * @return bool
   */
  public function validateEdition($iPrivilege, $oRecord)
  {
      if (!(SValidation::canEdit($iPrivilege) || SValidation::canAuthorEdit($iPrivilege, $oRecord->created_by_id))) {
        return redirect()->route('notauthorized');
      }
  }

  /**
   * blocks the record, if it has not been possible, returns an array with the error
   *
   * @param  object $oRecord
   * @return array  returns an array with the error if an user has the lock or an
   *                 empty array if the lock is free or the current user has it
   */
  public function validateLock($oRecord)
  {
      $iLock = session('lock')->acquireLock($oRecord);
      if ($iLock != -1) {
        return [0 => 'No se puede tener acceso al registro el usuario: '.User::find($iLock)->username.' lo está usando'];
      }
      else {
        return array();
      }
  }

  /**
   * Validate if registry is locked
   *
   * @param  object $oRecord
   * @return array  returns an array with the error if an user has the lock or an
   *                 empty array if the lock is free or the current user has it
   */
  public function validateIsLocked($oRecord)
  {
      $iLock = session('lock')->canUpdateRecord($oRecord);
      if ($iLock != -1) {
        return [0 => 'No se puede tener acceso al registro el usuario: '.User::find($iLock)->username.' lo está usando'];
      }
      else {
        return array();
      }
  }

  /**
   * [validateDestroy validate if the current user has
   *  the permission to delete the record.
   *  If hasn't the permission this redirect to not autorized page]
   *
   * @param  integer $iPrivilege [privilege level of action]
   */
  public function validateDestroy($iPrivilege = 0)
  {
      if (! SValidation::canDestroy($iPrivilege)) {
        return redirect()->route('notauthorized');
      }
  }

  /**
   * get the id of year received, this method returns the id that isn't deleted
   *
   * @param  [int] $iYear year to search in DB
   * @return [int] id of year in DB
   */
  public function getYearId($iYear = 0)
  {
      if ($iYear == 0) {
        $iYear = session('work_date')->year;
      }

      return SYear::where('year', $iYear)
                    ->where('is_deleted', false)
                    ->first()->id_year;
  }


  /**
   * determines the type of received document and returns a String
   *
   * @param  SDocument $oDocument
   *
   * @return String type of document
   */
  public function getDocumentTypeName($oDocument)
  {
      switch ($oDocument->doc_class_id) {
        case \Config::get('scsiie.DOC_CLS.DOCUMENT'):
          if ($oDocument->doc_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
              return trans('siie.labels.PURCHASES_INVOICE');
          }
          else {
              return trans('siie.labels.SALES_INVOICE');
          }
          break;

        case \Config::get('scsiie.DOC_CLS.ORDER'):
          if ($oDocument->doc_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
              return trans('siie.labels.PURCHASES_ORDER');
          }
          else {
              return trans('siie.labels.SALES_ORDER');
          }
          break;

        case \Config::get('scsiie.DOC_CLS.ADJUST'):
          if ($oDocument->doc_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
              return trans('siie.labels.PURCHASES_ADJUST');
          }
          else {
              return trans('siie.labels.SALES_ADJUST');
          }
          break;

        default:
              return '';
          break;
      }
  }

  /**
   * get the warehouses that the user has access, if a user is not received
   * take the user of session
   *
   * @param  integer $iUser user id
   *
   * @return array array of integers with the id of the warehouses
   */
  public static function getUserWarehousesArray($iUser = 0)
  {
     $oUser = $iUser == 0 ? \Auth::user() : User::find($iUser);

     $whss = array();
     if (session('utils')->isSuperUser($oUser)) {
        $warehouses = SWarehouse::where('is_deleted', false)->get();

        foreach ($warehouses as $whs) {
          array_push($whss, $whs->id_whs);
        }

        return $whss;
     }

     $whsAccess = $oUser->userWarehouses;

     foreach ($whsAccess as $access) {
        if (! $access->warehouses->is_deleted) {
            array_push($whss, $access->whs_id);
        }
     }

     return $whss;
  }

  /**
   * get the branches that the user has access, if a user is not received
   * take the user of session
   *
   * @param  integer $iUser user id
   *
   * @return array array of integers with the id of the branches
   */
  public static function getUserBranchesArray($iUser = 0)
  {
     $oUser = $iUser == 0 ? \Auth::user() : User::find($iUser);

     $bchs = array();
     if (session('utils')->isSuperUser($oUser)) {
        $branches = SBranch::where('is_deleted', false)->get();

        foreach ($branches as $bch) {
          array_push($bchs, $bch->id_branch);
        }

        return $bchs;
     }

     $bchAccess = $oUser->userBranches;

     foreach ($bchAccess as $access) {
        if (! $access->branch->is_deleted) {
            array_push($bchs, $access->branch_id);
        }
     }

     return $bchs;
  }

  /**
   * get the warehouses that the user has access, if a user is not received
   * take the user of session
   *
   * @param  integer $iUser user id
   * @param  integer $iBranch branch id
   * @param  boolean $bWithCode indicates if the method return the name of warehouses with code
   *
   * @return array array of integers and name with the warehouses
   */
  public static function getUserWarehousesArrayWithName($iUser = 0, $iBranch = 0, $bWithCode = false)
  {
     $oUser = $iUser == 0 ? \Auth::user() : User::find($iUser);

     $whss = array();
     if (session('utils')->isSuperUser($oUser)) {
        $warehouses = SWarehouse::where('is_deleted', false);

        if ($iBranch > 0) {
          $warehouses = $warehouses->where('branch_id', $iBranch);
        }

        $warehouses = $warehouses->get();

        foreach ($warehouses as $whs) {
          $whss[$whs->id_whs] = ($bWithCode ? $whs->code.'-' : '').$whs->name;
        }

        return $whss;
     }

     $whsAccess = $oUser->userWarehouses;

     foreach ($whsAccess as $access) {
        if (! $access->warehouses->is_deleted) {
          if ($iBranch == 0 || $access->warehouses->branch_id == $iBranch) {
            $whss[$access->whs_id] = ($bWithCode ? $access->warehouses->code.'-' : '').$access->warehouses->name;
          }

        }
     }

     return $whss;
  }

  /**
   * get the warehouses that the user has access, if a user is not received
   * take the user of session
   *
   * @param  integer $iUser user id
   * @param  integer $iBranch branch id
   * @param  boolean $bWithCode indicates if the method return the name of warehouses with code
   *
   * @return array array of integers and name with the warehouses
   */
  public static function getUserBranchesArrayWithName($iUser = 0, $iPartner = 0, $bWithCode = false)
  {
     $oUser = $iUser == 0 ? \Auth::user() : User::find($iUser);

     $branchess = array();
     if (session('utils')->isSuperUser($oUser)) {
        $branches = SBranch::selectRaw('CONCAT(code, "-", name) as branch_w_code,
                                        name,
                                        code,
                                        id_branch')
                              ->where('is_deleted', false);

        if ($iPartner > 0) {
           $branches = $branches->where('partner_id', $iPartner);
        }

        if ($bWithCode) {
           $branches = $branches->lists('branch_w_code', 'id_branch');
        }
        else {
           $branches = $branches->lists('name', 'id_branch');
        }

        return $branches;
     }

     $branchAccess = $oUser->userBranches;

     foreach ($branchAccess as $access) {
        if (! $access->branch->is_deleted) {
            $branchess[$access->branch_id] = ($bWithCode ? $access->branch->code.' - ' : '').$access->branch->name;
        }
     }

     return $branchess;
  }

  /**
   * format the folio number or text to longitude configured
   * and fill with zeros to left
   *
   * @param  string $oFolio
   *
   * @return string
   */
  public function formatFolio($oFolio = '')
  {
      if ($oFolio == null) {
        $oFolio = '';
      }

      return str_pad($oFolio, session('long_folios'), "0", STR_PAD_LEFT);
  }

  /**
   * format the folio number or text to longitude configured
   * and fill with zeros to left
   *
   * @param  string $oFolio
   *
   * @return string
   */
  public function formatQltyDocFolio($oFolio = '')
  {
      if ($oFolio == null) {
        $oFolio = '';
      }

      return str_pad($oFolio, session('qltydocs_folios'), "0", STR_PAD_LEFT);
  }

  /**
   * format the pallet number or text to longitude configured
   * and fill with zeros to left
   *
   * @param  string $sPallet
   *
   * @return string
   */
  public function formatPallet($sPallet = '')
  {
      if ($sPallet == null) {
        $sPallet = '';
      }

      $log_folios = session('qltydocs_folios') > 0 ? session('qltydocs_folios') : 6;

      return str_pad($oFolio, $log_folios, "0", STR_PAD_LEFT);
  }

  /**
   * Format the number received
   * the decimals of number are configured in the
   * table configuration of company on the database
   *
   * @param  string $value the number to be formatted
   * @param  string $type  can be :
   *                                \Config::get('scsiie.FRMT.AMT')
   *                                \Config::get('scsiie.FRMT.QTY')
   * @return string  the formatted number
   */
  public function formatNumber($value = '0', $type = '1')
  {
      try {
        switch ($type) {
          case \Config::get('scsiie.FRMT.AMT'):
            $iDecimals = session('decimals_amt');
            break;
          case \Config::get('scsiie.FRMT.QTY'):
            $iDecimals = session('decimals_qty');
            break;
          case \Config::get('scsiie.FRMT.PERC'):
            $iDecimals = session('decimals_percent');
            break;

          default:
            $iDecimals = 1;
            break;
        }
        return number_format($value, $iDecimals, '.', ',');

      }
      catch (Exception $e) {
        return number_format(0, 1, '.', ',');
      }
  }

  /**
   * set or refresh the permissions of user to session.
   */
  public function setUserPermissions() {
    $UsrPerm = \Auth::user()->userPermission;

    foreach ($UsrPerm as $up) {
       $up->thePermission = $up->permission;
    }

    session(['usr_permissions' => $UsrPerm]);
  }

  /**
   * Undocumented function
   *
   * @param integer $idCompany
   * @return void
   */
  public function getPartnerByCompany($idCompany)
  {
    $oCompany = SCompany::find($idCompany);

    $sConnection = 'siie';
    $bDefault = true;
    $sHost = $oCompany->host;
    $sDataBase = $oCompany->database_name;
    $sUser = $oCompany->database_user;
    $sPassword = $oCompany->password;

    SConnectionUtils::reconnectDataBase($sConnection, $bDefault, $sHost, $sDataBase, $sUser, $sPassword);

    $oErpConfigurationPartner = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PARTNER_ID'));

    return SPartner::find($oErpConfigurationPartner->val_int);;
  }

}
