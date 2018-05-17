<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SPartner extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_partner';
  protected $table = "erpu_partners";

  protected $fillable = [
                          'id_partner',
                          'code',
                          'name',
                          'last_name',
                          'first_name',
                          'fiscal_id',
                          'person_id',
                          'external_id',
                          'is_company',
                          'is_supplier',
                          'is_customer',
                          'is_creditor',
                          'is_debtor',
                          'is_bank',
                          'is_employee',
                          'is_agt_sales',
                          'is_related_party',
                          'is_deleted',
                        ];
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
   * Return objct User
   * @return User
   */
  public function userUpdate()
  {
    return $this->belongsTo('App\User', 'updated_by_id');
  }

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query     query to do
   * @param  string $bpName    business partner name
   * @param  integer  $iFilter   type of filter
   * @param  integer $iFilterBp type of filter business partner
   * @return query
   */
  public function scopeSearch($query, $bpName, $iFilter, $iFilterBp)
  {
      $bAtt = true;
      $sAtt = '';

      switch ($iFilterBp) {
        case \Config::get('scsiie.ATT.ALL'):
          $bAtt = false;
          break;
        case \Config::get('scsiie.ATT.IS_COMP'):
          $sAtt = 'is_company';
          break;
        case \Config::get('scsiie.ATT.IS_SUPP'):
          $sAtt = 'is_supplier';
          break;
        case \Config::get('scsiie.ATT.IS_CUST'):
          $sAtt = 'is_customer';
          break;
        // case \Config::get('scsiie.ATT.IS_CRED'):
        //   $sAtt = 'is_creditor';
        //   break;
        // case \Config::get('scsiie.ATT.IS_DEBT'):
        //   $sAtt = 'is_debtor';
        //   break;
        // case \Config::get('scsiie.ATT.IS_BANK'):
        //   $sAtt = 'is_bank';
        //   break;
        // case \Config::get('scsiie.ATT.IS_EMPL'):
        //   $sAtt = 'is_employee';
        //   break;
        // case \Config::get('scsiie.ATT.IS_AGTS'):
        //   $sAtt = 'is_agt_sales';
        //   break;
        case \Config::get('scsiie.ATT.IS_PART'):
          $sAtt = 'is_related_party';
          break;
        default:
          $bAtt = false;
          break;
      }

      if ($bAtt) {
          $query = $query->where($sAtt, '=',  1);
      }

      $query = $query->where(function ($query) use ($bpName) {
                    $query->where('name', 'LIKE', "%".$bpName."%")
                          ->orWhere('external_id', 'LIKE', "%".$bpName."%")
                          ->orWhere('last_name', 'LIKE', "%".$bpName."%")
                          ->orWhere('first_name', 'LIKE', "%".$bpName."%")
                          ->orWhere('fiscal_id', 'LIKE', "%".$bpName."%");
               });

      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
            break;

        case \Config::get('scsys.FILTER.DELETED'):
            return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
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
