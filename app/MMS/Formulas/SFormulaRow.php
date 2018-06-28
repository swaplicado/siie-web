<?php namespace App\MMS\Formulas;

use Illuminate\Database\Eloquent\Model;

class SFormulaRow extends Model {

    protected $connection = 'siie';
    protected $primaryKey = 'id_formula_row';
    protected $table = 'mms_formula_rows';

    protected $fillable = [
                            'id_formula_row',
                            'quantity',
                            'is_deleted',
                            'formula_id',
                            'item_id',
                            'unit_id',
                            'item_recipe_id',
                            'created_by_id',
                            'updated_by_id',
                          ];

    /**
     * [segregation description]
     *
     * @return SFormula
     */
    public function formula()
    {
      return $this->belongsTo('App\MMS\Formulas\SFormula');
    }

    /**
     * [segregation description]
     *
     * @return SSubstitute
     */
    public function substitute()
    {
      return $this->hasOne('App\MMS\Formulas\SFormulaSubstitute', 'formula_row_id', 'id_formula_row');
    }

    /**
     * [item description]
     * Return object SItem
     * @return SItem
     */
    public function item()
    {
      return $this->belongsTo('App\ERP\SItem', 'item_id');
    }

    /**
     * [unit description]
     * Return object SUnit
     * @return SUnit
     */
    public function unit()
    {
      return $this->belongsTo('App\ERP\SUnit', 'unit_id');
    }

    /**
     * Return object SUnit
     * @return SFormula
     */
    public function itemFormula()
    {
      return $this->belongsTo('App\MMS\Formulas\SFormula', 'item_recipe_id');
    }

}
