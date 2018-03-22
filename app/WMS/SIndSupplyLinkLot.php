<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;
use App\SUtils\SGuiUtils;

class SIndSupplyLinkLot extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_indirect_supply_link_lot';
  protected $table = 'wms_indirect_supply_link_lots';

  public $timestamps = false;

  protected $fillable = [
                          'id_indirect_supply_link_lot',
                          'quantity',
                          'is_deleted',
                          'indirect_supply_link_id',
                          'lot_id',
                        ];

  /**
   * Return object SMovement
   * @return SIndSuppyLink
   */
  public function supplyLink()
  {
    return $this->belongsTo('App\WMS\SIndSuppyLink', 'indirect_supply_link_id', 'id_indirect_supply_link');
  }

}
