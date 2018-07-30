<?php namespace App\MMS;

use App\ERP\SModel;

class SProductionOrder extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_order';
    protected $table = 'mms_production_orders';

    protected $fillable = [
                            'id_order',
                            'folio',
                            'identifier',
                            'plan_id',
                            'branch_id',
                            'floor_id',
                            'type_id',
                            'status_id',
                            'item_id',
                            'unit_id',
                            'formula_id',
                            'date',
                            'charges',
                            'father_order_id',
                            'is_deleted',
                            'created_by_id',
                            'updated_by_id',
                          ];

  public function branch()
  {
    return $this->belongsTo('App\ERP\SBranch', 'branch_id');
  }

  public function plan(){
    return $this->belongsTo('App\MMS\SProductionPlan');
  }

  public function floor()
  {
    return $this->belongsTo('App\MMS\SFloor');
  }

  public function father()
  {
    return $this->belongsTo('App\MMS\SProductionOrder', 'father_order_id');
  }

  public function formula()
  {
    return $this->belongsTo('App\MMS\Formulas\SFormula');
  }

  public function type()
  {
    return $this->belongsTo('App\MMS\STypeOrder');
  }

  public function status()
  {
    return $this->belongsTo('App\MMS\SStatusOrder');
  }

  public function item()
  {
    return $this->belongsTo('App\ERP\SItem');
  }

  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit');
  }

  public function scopeSearch($query, $name, $iFilter)
  {

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
