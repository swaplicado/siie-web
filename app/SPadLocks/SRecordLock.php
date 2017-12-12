<?php namespace App\SPadLocks;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;

/**
* RecordLock facade
* Adaptation of https://github.com/tokenly/laravel-record-lock
*/
class SRecordLock {

    /**
     * [acquireLock lock the record to prevent current modification]
     * @param  [object] $record [record of Model]
     * @return [int]             [returns -1 if the lock was obtained
     *                            returns the id of user if the lock is
     *                            occuped]
     */
    public function acquireLock($record)
    {
        return $this->aquire(session('company')->id_company, $record->getTable(), $record->getKey(), \Auth::user()->id);
    }

    /**
     * lock the record to prevent current modification. If the record is locked
     * the method verify if the lock has expired and release the lock.
     *
     * @param  [int] $iCompanyId [company id from SCompany model]
     * @param  [string] $sTable     [name of table of model]
     * @param  [int] $iRecordId  [id of record to lock]
     * @param  [int] $iUserId    [description]
     * @return [int]             [returns -1 if the lock was obtained
     *                            returns the id of user if the lock is
     *                            occuped.]
     */
    protected function aquire($iCompanyId, $sTable, $iRecordId, $iUserId)
    {
        if (is_null($iRecordId))
        {
          return -1;
        }
        
        $oLock = SLock::where('company_id', $iCompanyId)
                        ->where('table_name', $sTable)
                        ->where('record_id', $iRecordId)
                        ->first();

        if (is_null($oLock)) {
          $oLock = new SLock();
          $oLock->session_id = session()->getId();
          $oLock->company_id = $iCompanyId;
          $oLock->table_name = $sTable;
          $oLock->record_id = $iRecordId;
          $oLock->user_id = $iUserId;
          $oLock->save();

          return -1;
        }
        else
        {
          if ($oLock->session_id == session()->getId())
          {
              return -1;
          }
          else
          {
             $timeDiff = $oLock->created_at->diff(Carbon::now());
             if ($timeDiff->i > session('lock_time'))
             {
                $released = $this->release($iCompanyId, $sTable, $iRecordId, $iUserId);
                if ($released == -1)
                {
                  return $this->aquire($iCompanyId, $sTable, $iRecordId, $iUserId);
                }
                else
                {
                  return $released;
                }
             }
             else
             {
                return $oLock->user_id;
             }
          }
        }
    }

    /**
     * [releaseLock unlock the record of model]
     * @param  [object from model] $record
     * @return [int]             [returns -1 if the lock was released
     *                            returns the id of user if the lock is
     *                            occuped]
     */
    public function releaseLock($record)
    {
      return $this->release(session('company')->id_company, $record->getTable(), $record->getKey(), \Auth::user()->id);
    }

    /**
     * [release unlock the record of model]
     * @param  [int] $iCompanyId [company id from SCompany model]
     * @param  [string] $sTable     [name of table of model]
     * @param  [int] $iRecordId  [id of record to lock]
     * @param  [int] $iUserId    [description]
     * @return [int]             [returns -1 if the lock was released
     *                            returns the id of user if the lock is
     *                            occuped]
     */
    protected function release($iCompanyId, $sTable, $iRecordId, $iUserId)
    {
        $oLock = SLock::where('company_id', $iCompanyId)
                        ->where('table_name', $sTable)
                        ->where('record_id', $iRecordId)
                        ->first();

        if (! is_null($oLock)) {
          if ($oLock->session_id == session()->getId())
          {
            $oLock->delete();
            return -1;
          }
          else
          {
            if ($oLock->created_at->diffInMinutes(Carbon::now()) > session('lock_time'))
            {
                $oLock->delete();
                return -1;
            }
            else
            {
               return $oLock->user_id;
            }
          }

        }
        else
        {
          return -1;
        }
    }

    /**
     * returns true if the record is locked
     *
     * @param  [object]   $record
     * @return bool
     */
    public function isLocked($record)
    {
        return $this->isAlreadyLocked(session('company')->id_company, $record->getTable(), $record->getKey());

    }

    protected function isAlreadyLocked($iCompanyId, $sTable, $iRecordId)
    {
        $oLock = SLock::where('company_id', $iCompanyId)
                        ->where('table_name', $sTable)
                        ->where('record_id', $iRecordId)
                        ->first();

        return !is_null($oLock);
    }

    /**
     * returns true if the record is locked.
     * If the record is locked returns the id of user who has the locked
     * If the record isn't locked returns -1
     *
     * @param  object   $record
     * @return int
     */
    public function canUpdateRecord($record)
    {
      return $this->canUpdateByLock(session('company')->id_company, $record->getTable(), $record->getKey());
    }

    /**
     * [canUpdateByLock description]
     * @param  [int] $iCompanyId [company id from SCompany model]
     * @param  [string] $sTable     [name of table of model]
     * @param  [int] $iRecordId  [id of record to lock]
     * @param  [int] $iUserId    [description]
     * @return [type]             [description]
     */
    public function canUpdateByLock($iCompanyId, $sTable, $iRecordId)
    {
        $oLock = SLock::where('company_id', $iCompanyId)
                        ->where('table_name', $sTable)
                        ->where('record_id', $iRecordId)
                        ->first();

        if (! is_null($oLock))
        {
            if ($oLock->session_id == session()->getId())
            {
                return -1;;
            }
            else
            {
                return $oLock->user_id;
            }
        }
        else
        {
           return $this->aquire($iCompanyId, $sTable, $iRecordId, \Auth::user()->id);
        }
    }

    /**
     * [getLockUserId get the id of user who has the padlock]
     * @param  object $record [description]
     * @return [int]  [returns the id of the user who has the padlock
     *                  if the lock not exists return -1 ]
     */
    public function getLockUserId($record)
    {
        return $this->isAlreadyLocked(session('company')->id_company, $record->getTable(), $record->getKey());
    }

    /**
     * [getLockUser get the id of user who has the padlock]
     * @param  [type] $iCompanyId
     * @param  [type] $sTable
     * @param  [type] $iRecordId
     * @return [int]   [returns the id of the user who has the padlock
     *                  if the lock not exists return -1 ]
     */
    protected function getLockUser($iCompanyId, $sTable, $iRecordId)
    {
        $oLock = SLock::where('company_id', $iCompanyId)
                        ->where('table_name', $sTable)
                        ->where('record_id', $iRecordId)
                        ->first();

        if (is_null($oLock))
        {
            return -1;
        }
        else
        {
          return $oLock->user_id;
        }
    }
}
