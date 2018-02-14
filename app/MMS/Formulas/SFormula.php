<?php namespace App\MMS\Formulas;

use App\ERP\SModel;

class SFormula extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_formula';
    protected $table = 'mms_formulas';

    protected $fillable = [
                            'id_formula',
                            'dt_start',
                            'dt_end',
                            'name',
                            'quantity',
                            'duration',
                            'cost',
                            'is_exploded',
                            'is_deleted',
                            'item_id',
                            'unit_id',
                            'branch_id',
                            'created_by_id',
                            'updated_by_id',
                          ];

    /**
     * [rows description]
     * Return object SSegregartionRow
     * @return SFormulaRow
     */
    public function rows()
    {
      return $this->hasmany('App\MMS\Formulas\SFormulaRow', 'formula_id', 'id_formula');
    }

    /**
     * [rows description]
     * Return object SSegregartionRow
     * @return SFormulaNote
     */
    public function notes()
    {
      return $this->hasmany('App\MMS\Formulas\SFormulaNote', 'formula_id', 'id_formula');
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
