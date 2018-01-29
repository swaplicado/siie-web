<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SAddress extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_branch_address';
  protected $table = 'erpu_branch_addresses';

  protected $fillable = [
                          'id_branch_address',
                          'name',
                          'street',
                          'num_ext',
                          'num_int',
                          'neighborhood',
                          'reference',
                          'locality',
                          'county',
                          'state',
                          'zip_code',
                          'external_id',
                          'is_main',
                          'is_deleted',
                          'branch_id',
                          'country_id',
                          'state_id',
                          ];

  /**
   * [branch description]
   * Return object SBranch
   * @return SBranch
   */
  public function branch()
  {
    return $this->belongsTo('App\ERP\SBranch', 'branch_id');
  }

  /**
   * [country description]
   * Return object SCountry
   * @return SCountry
   */
  public function country()
  {
    return $this->belongsTo('App\ERP\SCountry', 'country_id');
  }

  /**
   * [state description]
   * Return object SState
   * @return SState
   */
  public function state()
  {
    return $this->belongsTo('App\ERP\SState', 'state_id');
  }

  /**
   * [scopeSearch description]
   * @param  string $query   query to do
   * @param  string $name    variable for where clause
   * @param  integer $iFilter type of filter
   * @param  boolean $iBranchId if have filter of branch
   * @return [type]            [description]
   */
  public function scopeSearch($query, $name, $iFilter, $iBranchId)
  {
      if ($iBranchId == NULL) {
          switch ($iFilter) {
            case \Config::get('scsys.FILTER.ACTIVES'):
              return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                          ->where('name', 'LIKE', "%".$name."%");
              break;

            case \Config::get('scsys.FILTER.DELETED'):
              return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                            ->where('name', 'LIKE', "%".$name."%");
              break;

            case \Config::get('scsys.FILTER.ALL'):
              return $query->where('name', 'LIKE', "%".$name."%");
              break;

            default:
              return $query->where('name', 'LIKE', "%".$name."%");
              break;
          }
      }
      else
      {
          switch ($iFilter) {
            case \Config::get('scsys.FILTER.ACTIVES'):
              return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                          ->where('branch_id', $iBranchId)
                          ->where('name', 'LIKE', "%".$name."%");
              break;

            case \Config::get('scsys.FILTER.DELETED'):
              return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                            ->where('branch_id', $iBranchId)
                            ->where('name', 'LIKE', "%".$name."%");
              break;

            case \Config::get('scsys.FILTER.ALL'):
              return $query->where('name', 'LIKE', "%".$name."%")->where('branch_id', $iBranchId);
              break;

            default:
              return $query->where('name', 'LIKE', "%".$name."%")->where('branch_id', $iBranchId);
              break;
          }
      }
  }
}
