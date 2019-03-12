<?php namespace App\WMS;

use App\ERP\SModel;

class SSuppDivision extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_division';
    protected $table = 'wms_supplies_divisions';

    protected $fillable = [
                            'id_division',
                            'is_deleted',
                            'out_division_id',
                            'in_division_id',
                            'mvt_reference_id',
                            'created_by_id',
                            'updated_by_id'
                          ];
   /**
    * References to out movement that makes the pallets division
    *
    * @return App\WMS\SMovement
    */
   public function outDivisionMov()
   {
      return $this->belongsTo('App\WMS\SMovement', 'out_division_id');
   }

   /**
    * References to in movement that makes the pallets division
    *
    * @return App\WMS\SMovement
    */
   public function inDivisionMov()
   {
      return $this->belongsTo('App\WMS\SMovement', 'in_division_id');
   }

   /**
    * References to movement that makes the supply
    *
    * @return App\WMS\SMovement
    */
   public function supplyMov()
   {
      return $this->belongsTo('App\WMS\SMovement', 'mvt_reference_id');
   }
   
   /**
   * [userCreation description]
   * Return object User
   * 
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

}
