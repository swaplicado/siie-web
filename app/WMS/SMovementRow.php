<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;
use App\SUtils\SGuiUtils;

class SMovementRow extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_mvt_row';
  protected $table = 'wms_mvt_rows';
  public $timestamps = false;
  protected $auxLots = [];
  public $iAuxLotId = 0;
  public $aAuxStock = [];
  public $dIndSupplied = [];
  public $iAuxLocationDesId = 0;
  public $dReceived = 0;
  public $oAuxSupplyLink = 0;

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

  /**
   * [movement description]
   * Return object SMovement
   * @return SMovement
   */
  public function movement()
  {
    return $this->belongsTo('App\WMS\SMovement', 'mvt_id', 'id_mvt');
  }

  /**
   * [lotRows description]
   * Return object SMovementRowLot
   * @return SMovementRowLot
   */
  public function lotRows()
  {
    return $this->hasMany('App\WMS\SMovementRowLot', 'mvt_row_id');
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
   * [pallet description]
   * Return object SPallet
   * @return SPallet
   */
  public function pallet()
  {
    return $this->belongsTo('App\WMS\SPallet', 'pallet_id');
  }

  /**
   * [location description]
   * Return object SLocation
   * @return SLocation
   */
  public function location()
  {
    return $this->belongsTo('App\WMS\SLocation', 'location_id');
  }

  /**
   * [setAuxLots description]
   * Set value to auxLots
   * @param array $value values to put in auxLots
   */
  public function setAuxLots($value = array())
  {
     $this->auxLots = $value;
  }

  /**
   * [getAuxLots description]
   * Get value of auxLots
   * @return [type] [description]
   */
  public function getAuxLots()
  {
     return $this->auxLots;
  }

  public function getDIndSupplied()
  {
    return $this->dIndSupplied;
  }

  public function setDIndSupplied($value = [])
  {
    $this->dIndSupplied = $value;
  }

  /**
   * [setLotId description]
   * Set value of LotId
   * @param string $value value to put in iAuxLotId
   */
  public function setLotId($value = '0')
  {
    $this->iAuxLotId = $value;
  }

  /**
   * [getLotId description]
   * Get value of LotId
   * @return iAuxLotId
   */
  public function getLotId()
  {
    return $this->iAuxLotId;
  }

  /**
   * [setStockAux description]
   * Set value of aAuxStock
   * @param array $value value to put in $aAuxStock
   */
  public function setStockAux($value = [])
  {
    $this->aAuxStock = $value;
  }

  /**
   * [getStockAux description]
   * Get value of aAuxStock
   * @return aAuxStock
   */
  public function getStockAux()
  {
    return $this->aAuxStock;
  }

  public function setReceived($value = 0)
  {
    $this->dReceived = $value;
  }

  public function getReceived()
  {
    return $this->dReceived;
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query     query to do
   * @param  string $name      where clause
   * @param  integer $iFilter   type of filter
   * @param  date $sDtFilter date to the filter
   * @return string            query
   */
  public function scopeSearch($query, $iFilter, $sDtFilter)
  {
      $aDates = SGuiUtils::getDatesOfFilter($sDtFilter);

      $query->join('erpu_items as ei', $this->table.'.item_id', '=', 'ei.id_item')
              ->join('wms_mvts as wm', $this->table.'.mvt_id', '=', 'wm.id_mvt')
              ->where('branch_id', session('branch')->id_branch)
              ->select($this->table.'.*')
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
