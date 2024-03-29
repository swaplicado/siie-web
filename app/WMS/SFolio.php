<?php namespace App\WMS;

use App\ERP\SModel;

use App\WMS\SLocation;
use App\WMS\SWarehouse;
use App\ERP\SBranch;
use App\ERP\SPartner;

class SFolio extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_container_folio';
    protected $table = 'wmsu_container_folios';

    protected $fillable = [
                            'folio_start',
                            'is_deleted',
                            'container_type_id',
                            'container_id',
                            'mvt_class_id',
                            'mvt_type_id',
                            'mvt_trn_type_id',
                            'mvt_adj_type_id',
                            'mvt_mfg_type_id',
                            'mvt_exp_type_id',
                            'created_by_id',
                            'updated_by_id',
                            'aux_location_id',
                            'aux_whs_id',
                            'aux_branch_id',
                          ];

   protected $auxWarehouse = '';
   protected $auxBranch = '';
   protected $auxCompany = '';

   /**
    * [mvtClass description]
    * Return object SMvtClass
    * @return SMvtClass
    */
    public function mvtClass()
    {
      return $this->belongsTo('App\WMS\SMvtClass', 'mvt_class_id');
    }

    /**
     * [mvtType description]
     * Return object SMvtType
     * @return SMvtType
     */
    public function mvtType()
    {
      return $this->belongsTo('App\WMS\SMvtType', 'mvt_type_id');
    }

    /**
     * [getLocation description]
     * Return object SLocation
     * @return SLocation
     */
    public function getLocation()
    {
       if ($this->container_type_id == \Config::get('scwms.CONTAINERS.LOCATION'))
       {
          $location = SLocation::find($this->container_id);

          $this->auxWarehouse = $location->warehouse;
          $this->auxBranch = $this->auxWarehouse->branch;
          $this->auxCompany = $this->auxBranch->partner;

          return $location;
       }
       else
       {
          return SLocation::find(1);
       }
    }

    /**
     * [getWarehouse description]
     * Return object SWarehouse
     * @return SWarehouse
     */
    public function getWarehouse()
    {
      if ($this->auxWarehouse != '')
      {
          return $this->auxWarehouse;
      }
      elseif ($this->container_type_id == \Config::get('scwms.CONTAINERS.WAREHOUSE'))
       {
          $warehouse = SWarehouse::find($this->container_id);

          $this->auxBranch = $warehouse->branch;
          $this->auxCompany = $this->auxBranch->partner;

          return $warehouse;
       }
       else
       {
          return SWarehouse::find(1);
       }
    }

    /**
     * [getBranch description]
     * Return object SBranch
     * @return SBranch
     */
    public function getBranch()
    {
        if ($this->auxBranch != '')
        {
            return $this->auxBranch;
        }
       elseif ($this->container_type_id == \Config::get('scwms.CONTAINERS.BRANCH'))
       {
            $branch =  SBranch::find($this->container_id);

            $this->auxCompany = $branch->partner;

            return $branch;
       }
       else
       {
          return SBranch::find(1);
       }
    }

    /**
     * [getCompany description]
     * Return object SPartner
     * @return SPartner
     */
    public function getCompany()
    {
        if ($this->auxCompany != '')
        {
            return $this->auxCompany;
        }
       else if ($this->container_type_id == \Config::get('scwms.CONTAINERS.COMPANY'))
       {
          return SPartner::find($this->container_id);
       }
       else
       {
          return SPartner::find(1);
       }
    }

    /**
     * [scopeSearch description]
     * To search in a query
     * @param  string $query      query to do
     * @param  integer $folioStart in what folio start to search
     * @param  integer $iFilter    type of filter
     * @return string             query
     */
    public function scopeSearch($query, $folioStart, $iFilter)
    {
        switch ($iFilter) {
          case \Config::get('scsys.FILTER.ACTIVES'):
              return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                          ->where('folio_start', 'LIKE', "%".$folioStart."%");
            break;

          case \Config::get('scsys.FILTER.DELETED'):
              return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                            ->where('folio_start', 'LIKE', "%".$folioStart."%");
            break;

          case \Config::get('scsys.FILTER.ALL'):
              return $query->where('folio_start', 'LIKE', "%".$folioStart."%");
            break;

          default:
              return $query->where('folio_start', 'LIKE', "%".$folioStart."%");
            break;
        }
    }

}
