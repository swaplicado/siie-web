<?php namespace App\MMS\Formulas;

use App\ERP\SModel;

class SFormulaNote extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_note';
    protected $table = 'mms_formula_notes';

    protected $fillable = [
                            'id_note',
                            'note',
                            'is_deleted',
                            'formula_id',
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
      return $this->belongsTo('App\MMS\SFormula');
    }

}
