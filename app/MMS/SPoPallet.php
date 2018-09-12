<?php namespace App\MMS;

use App\ERP\SModel;

class SPoPallet extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_po_pallet';
    protected $table = 'mmss_po_pallets';

    public function getTable()
    {
      return $this->table;
    }

    protected $fillable = [
                            'id_po_pallet',
                            'po_id',
                            'pallet_id',
                          ];
    /**
     * [unit description]
     * Return object SUnit
     * @return SProductionOrder
     */
    public function productionOrder()
    {
      return $this->belongsTo('App\MMS\SProductionOrder');
    }

    /**
     * [unit description]
     * Return object SUnit
     * @return SPallet
     */
    public function pallet()
    {
      return $this->belongsTo('App\WMS\SPallet');
    }

}
