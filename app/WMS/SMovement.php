<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;

class SMovement extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt';
  protected $table = 'wms_mvts';

  protected $fillable = [
                    'dt_date',
                    'folio',
                    'total_amount',
                    'total_length',
                    'total_surface',
                    'total_volume',
                    'total_mass',
                    'is_closed_shipment',
                    'is_deleted',
                    'mvt_whs_class_id',
                    'mvt_whs_type_id',
                    'mvt_trn_type_id',
                    'mvt_adj_type_id',
                    'mvt_mfg_type_id',
                    'mvt_exp_type_id',
                    'branch_id',
                    'whs_id',
                    'auth_status_id',
                    'src_mvt_id',
                    'doc_order_id',
                    'doc_invoice_id',
                    'doc_debit_note_id',
                    'doc_credit_note_id',
                    'mfg_dept_id',
                    'mfg_line_id',
                    'mfg_job_id',
                    'auth_status_by_id',
                    'closed_shipment_by_id',
                    'ts_auth_status',
                    'ts_closed_shipment'
                        ];

  public function rows()
  {
    return $this->hasMany('App\WMS\SMovementRow', 'mvt_id', 'id_mvt');
  }

  public function whs()
  {
    return $this->belongsTo('App\WMS\SWarehouse', 'whs_id');
  }

  public function branch()
  {
    return $this->belongsTo('App\ERP\SBranch', 'branch_id');
  }
}
