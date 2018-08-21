<?php namespace App\WMS;

use Illuminate\Database\Eloquent\Model;
use App\SUtils\SGuiUtils;

class SIndSupplyLink extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_indirect_supply_link';
  protected $table = 'wms_indirect_supply_links';

  public $lAuxLotLinks = [];

  protected $fillable = [
                          'id_indirect_supply_link',
                          'quantity',
                          'is_deleted',
                          'src_doc_row_id',
                          'des_doc_row_id',
                          'mvt_row_id',
                          'pallet_id',
                          'created_by_id',
                          'updated_by_id',
                        ];

  /**
   * Return object SIndSupplyLinkLot
   * @return array of SIndSupplyLinkLot
   */
  public function linkLots()
  {
    return $this->hasMany('App\WMS\SIndSupplyLinkLot', 'indirect_supply_link_id', 'id_indirect_supply_link');
  }

}
