<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SStock extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_stock';
  protected $table = 'wms_stock';
  public $timestamps = false;

  protected $fillable = [
                          'id_stock',
                          'dt_date',
                          'input',
                          'output',
                          'cost_unit',
                          'debit',
                          'credit',
                          'is_deleted',
                          'mvt_whs_class_id',
                          'mvt_whs_type_id',
                          'mvt_trn_type_id',
                          'mvt_adj_type_id',
                          'mvt_mfg_type_id',
                          'mvt_exp_type_id',
                          'branch_id',
                          'whs_id',
                          'location_id',
                          'mvt_id',
                          'mvt_row_id',
                          'mvt_row_lot_id',
                          'item_id',
                          'unit_id',
                          'lot_id',
                          'pallet_id',
                          'doc_order_row_id',
                          'doc_invoice_row_id',
                          'doc_debit_note_row_id',
                          'doc_credit_note_row_id',
                          'mfg_dept_id',
                          'mfg_line_id',
                          'mfg_job_id',
                        ];

  public function getTable()
  {
    return $this->table;
  }

  public function movement()
  {
    return $this->belongsTo('App\WMS\SMovement', 'mvt_id');
  }

  public function item()
  {
    return $this->belongsTo('App\ERP\SItem', 'item_id');
  }

}
