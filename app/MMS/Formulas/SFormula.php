<?php namespace App\MMS\Formulas;

use App\ERP\SModel;

class SFormula extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_formula';
    protected $table = 'mms_formulas';

    public function getTable()
    {
      return $this->table;
    }

    protected $fillable = [
                            'id_formula',
                            'folio',
                            'version',
                            'recipe',
                            'dt_date',
                            'identifier',
                            'notes',
                            'quantity',
                            'is_deleted',
                            'external_id',
                            'item_id',
                            'unit_id',
                            'created_by_id',
                            'updated_by_id',
                          ];

    /**
     * [rows description]
     * Return object SSegregartionRow
     * @return SFormulaRow
     */
    public function order()
    {
      return $this->hasmany('App\MMS\SProductionOrder');
    }

    public function rows()
    {
      return $this->hasmany('App\MMS\Formulas\SFormulaRow', 'formula_id', 'id_formula');
    }

    /**
     * [unit description]
     * Return object SUnit
     * @return SUnit
     */
    public function unit()
    {
      return $this->belongsTo('App\ERP\SUnit');
    }

    /**
     * [unit description]
     * Return object SItem
     * @return SItem
     */
    public function item()
    {
      return $this->belongsTo('App\ERP\SItem');
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
