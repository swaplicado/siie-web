<?php namespace App\WMS;

use App\ERP\SModel;
use App\ERP\SDocument;
use App\SUtils\SGuiUtils;

class SMovement extends SModel {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt';
  protected $table = 'wms_mvts';

  public $aAuxRows = [];
  public $iAuxBranchDes = [];
  public $aAuxPOs = [];

  const SRC_PO = '0';
  const DES_PO = '1';
  const ASS_TYPE = '2';

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
                    'is_system',
                    'mvt_whs_class_id',
                    'mvt_whs_type_id',
                    'mvt_trn_type_id',
                    'mvt_adj_type_id',
                    'mvt_mfg_type_id',
                    'mvt_exp_type_id',
                    'branch_id',
                    'whs_id',
                    'year_id',
                    'auth_status_id',
                    'src_mvt_id',
                    'doc_order_id',
                    'doc_invoice_id',
                    'doc_debit_note_id',
                    'doc_credit_note_id',
                    'prod_ord_id',
                    'mfg_dept_id',
                    'mfg_line_id',
                    'mfg_job_id',
                    'auth_status_by_id',
                    'closed_shipment_by_id',
                    'ts_auth_status',
                    'ts_closed_shipment'
                        ];

  /**
   * [rows description]
   * Return object SMovementRow
   * @return SMovementRow
   */
  public function rows()
  {
    return $this->hasMany('App\WMS\SMovementRow', 'mvt_id', 'id_mvt');
  }

  /**
   * [invoice description]
   * Return object SDocument
   * @return SDocument
   */
  public function invoice()
  {
    return $this->belongsTo('App\ERP\SDocument', 'doc_invoice_id');
  }

  /**
   * [order description]
   * Return object SDocument
   * @return SDocument
   */
  public function order()
  {
    return $this->belongsTo('App\ERP\SDocument', 'doc_order_id');
  }

  /**
   * [order description]
   * Return object SDocument
   * @return SProductionOrder
   */
  public function productionOrder()
  {
    return $this->belongsTo('App\MMS\SProductionOrder', 'prod_ord_id');
  }

  /**
   * [warehouse description]
   * Return object SWarehouse
   * @return SWarehouse
   */
  public function warehouse()
  {
    return $this->belongsTo('App\WMS\SWarehouse', 'whs_id');
  }

  /**
   * [branch description]
   * Return object SBranch
   * @return SBranch
   */
  public function branch()
  {
    return $this->belongsTo('App\ERP\SBranch', 'branch_id');
  }

  /**
   * [mvtType description]
   * Return object SMvtType
   * @return SMvtType
   */
  public function mvtType()
  {
    return $this->belongsTo('App\WMS\SMvtType', 'mvt_whs_type_id');
  }

  /**
   * [trnType description]
   * Return object SMvtTrnType
   * @return SMvtTrnType
   */
  public function trnType()
  {
    return $this->belongsTo('App\WMS\SMvtTrnType', 'mvt_trn_type_id');
  }

  /**
   * [adjType description]
   * Return object SMvtAdjType
   * @return SMvtAdjType
   */
  public function adjType()
  {
    return $this->belongsTo('App\WMS\SMvtAdjType', 'mvt_adj_type_id');
  }

  /**
   * [mfgType description]
   * Return object SMvtMfgType
   * @return SMvtMfgType
   */
  public function mfgType()
  {
    return $this->belongsTo('App\WMS\SMvtMfgType', 'mvt_mfg_type_id');
  }

  /**
   * [expType description]
   * Return object SMvtExpType
   * @return SMvtExpType
   */
  public function expType()
  {
    return $this->belongsTo('App\WMS\SMvtExpType', 'mvt_exp_type_id');
  }

  /**
   * [userCreation description]
   * Return object User
   * @return User
   */
  public function userCreation()
  {
    return $this->belongsTo('App\User', 'created_by_id');
  }

  /**
   * [userUpdate description]
   * Return object User
   * @return User
   */
  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  public function getDocumentSupplied()
  {
      $iDocument = 0;

      if ($this->doc_invoice_id > 1) {
        $iDocument = $this->doc_invoice_id;
      } elseif ($this->doc_order_id > 1) {
        $iDocument = $this->doc_order_id;
      } elseif ($this->doc_credit_note_id > 1) {
        $iDocument = $this->doc_credit_note_id;
      } else {
        $iDocument = $this->doc_invoice_id;
      }

      $oDocument = SDocument::find($iDocument);

      return $oDocument;
  }


  /**
   * filter of movements
   *
   * @param  Query $query
   * @param  integer $iFilter is_deleted
   * @param  string $sDtFilter date in format 'dd/mm/yyyy - dd/mm/yyyy'
   *
   * @return Query  after filters
   */
  public function scopeSearch($query, $iFilter, $sDtFilter)
  {
      $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);

      $query->whereBetween('dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);

      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            return $query->where($this->table.'.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where($this->table.'.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
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
