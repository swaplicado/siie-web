<?php namespace App\MMS\Formulas;

use Illuminate\Database\Eloquent\Model;

class SFormulaSubstitute extends Model {

    protected $connection = 'siie';
    protected $primaryKey = 'id_substitute';
    protected $table = 'mms_form_substitutes';

    protected $fillable = [
                            'id_substitute',
                            'percentage',
                            'percentage_max',
                            'is_deleted',
                            'formula_row_id',
                            'formula_id',
                            'item_id',
                            'unit_id',
                            'created_by_id',
                            'updated_by_id',
                          ];

    /**
     * [segregation description]
     * Return object SSegregation
     * @return SFormula
     */
    public function formula()
    {
      return $this->belongsTo('App\MMS\Formulas\SFormula');
    }

    /**
     * [segregation description]
     *
     * @return SFormulaRow
     */
    public function formulaRow()
    {
      return $this->hasOne('App\MMS\Formulas\SFormulaRow');
    }

}
