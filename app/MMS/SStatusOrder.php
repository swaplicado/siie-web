<?php namespace App\MMS;

use App\ERP\SModel;

class SStatusOrder extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_status';
    protected $table = 'mms_status_order';

    protected $fillable = [
                            'id_status',
                            'name',
                            'is_deleted',
                            'created_by_id',
                            'updated_by_id',
                          ];

    public function order()
    {
      return $this->hasmany('App\MMS\SProductionOrder');
    }


    // /**
    //  * [rows description]
    //  * Return object SSegregartionRow
    //  * @return SFormulaNote
    //  */
    // public function notes()
    // {
    //   return $this->hasmany('App\MMS\Formulas\SFormulaNote', 'formula_id', 'id_formula');
    // }

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
        $query = $query->where('identifier', 'LIKE', "%".$name."%");

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
