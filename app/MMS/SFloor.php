<?php namespace App\MMS;

use App\ERP\SModel;

class SFloor extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_floor';
    protected $table = 'mms_floor';

    protected $fillable = [
                            'id_floor',
                            'code',
                            'name',
                            'branch_id',
                            'created_by_id',
                            'updated_by_id',
                          ];

    public function getTable()
    {
        return $this->table;
    }

    public function branch()
    {
      return $this->belongsTo('App\ERP\SBranch', 'branch_id');
    }

    public function plan()
    {
      return $this->hasmany('App\MMS\SProductionPlan');
    }
    /**
     * [scopeSearch description]
     * To search in a query
     * @param  string $query   query to do
     * @param  string $name    where clause
     * @param  integer $iFilter type of filter
     * @return string          query
     */
    public function scopeSearch($query, $name, $iFilter)
    {
        $query = $query->where('name', 'LIKE', "%".$name."%");

        switch ($iFilter) {
          case \Config::get('scsys.FILTER.ACTIVES'):
              return $query->where('is_deleted', \Config::get('scsys.STATUS.ACTIVE'));
              break;

          case \Config::get('scsys.FILTER.DELETED'):
              return $query->where('is_deleted', \Config::get('scsys.STATUS.DEL'));
              break;

          case \Config::get('scsys.FILTER.ALL'):
              return $query;
              break;

          default:
              return $query;
              break;
        }
    }

}
