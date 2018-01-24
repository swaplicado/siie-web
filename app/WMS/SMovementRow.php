<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;
use App\SUtils\SGuiUtils;

class SMovementRow extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_row';
  protected $table = 'wms_mvt_rows';
  public $timestamps = false;
  protected $auxLots = array();
  public $iAuxLotId = 0;
  public $aAuxStock = [];

  protected $fillable = [
                          'id_mvt_row',
                          'quantity',
                          'amount_unit',
                          'amount',
                          'length',
                          'surface',
                          'volume',
                          'mass',
                          'is_deleted',
                          'mvt_id',
                          'item_id',
                          'unit_id',
                          'pallet_id',
                          'location_id',
                          'doc_order_row_id',
                          'doc_invoice_row_id',
                          'doc_debit_note_row_id',
                          'doc_credit_note_row_id',
                          'aux_lot_id',
                          'aux_lots',
                          'aux_stock',
                        ];

  public function movement()
  {
    return $this->belongsTo('App\WMS\SMovement', 'mvt_id', 'id_mvt');
  }

  public function lotRows()
  {
    return $this->hasMany('App\WMS\SMovementRowLot', 'mvt_row_id');
  }

  public function item()
  {
    return $this->belongsTo('App\ERP\SItem', 'item_id');
  }

  public function unit()
  {
    return $this->belongsTo('App\ERP\SUnit', 'unit_id');
  }

  public function pallet()
  {
    return $this->belongsTo('App\WMS\SPallet', 'pallet_id');
  }

  public function location()
  {
    return $this->belongsTo('App\WMS\SLocation', 'location_id');
  }

  public function setAuxLots($value = array())
  {
     $this->auxLots = $value;
  }

  public function getAuxLots()
  {
     return $this->auxLots;
  }

  public function setLotId($value = '0')
  {
    $this->iAuxLotId = $value;
  }

  public function getLotId()
  {
    return $this->iAuxLotId;
  }

  public function setStockAux($value = [])
  {
    $this->aAuxStock = $value;
  }

  public function getStockAux()
  {
    return $this->aAuxStock;
  }

  public function scopeSearch($query, $name, $iFilter, $sDtFilter)
  {
      $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);

      $query->join('erpu_items as ei', $this->table.'.item_id', '=', 'ei.id_item')
              ->join('wms_mvts as wm', $this->table.'.mvt_id', '=', 'wm.id_mvt')
              ->select($this->table.'.*')
              ->where('ei.is_deleted', false)
              ->whereBetween('wm.dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);

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
