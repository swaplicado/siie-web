<?php namespace App\SUtils;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\ERP\SYear;

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

}
