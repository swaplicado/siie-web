<?php namespace App\SImportations;

use App\ERP\SPartner;

/**
 *
 */
class SImportPartners
{
  protected $webhost        = 'localhost';
  protected $webusername    = 'root';
  protected $webpassword    = 'msroot';
  protected $webdbname      = 'erp';
  protected $webcon         = '';

  function __construct()
  {
      $this->webcon = mysqli_connect($this->webhost, $this->webusername, $this->webpassword, $this->webdbname);
      $this->webcon->set_charset("utf8");
      if (mysqli_connect_errno())
      {
          echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
      }
  }

  public function importPartners()
  {
      $sql = "SELECT id_bp, bp, lastname, firstname, fiscal_id,
              b_del, b_co, b_cus, b_sup, b_att_rel_pty,
              ts_new, ts_edit, ts_del FROM bpsu_bp";
      $result = $this->webcon->query($sql);
      $lSiiePartners = array();
      $lWebPartners = SPartner::get();
      $lPartners = array();
      $lPartnersToWeb = array();

      foreach ($lWebPartners as $key => $value)
      {
          $lPartners[$value->external_id] = $value;
      }

      if ($result->num_rows > 0)
      {
         // output data of each row
         while($row = $result->fetch_assoc())
         {
             if (array_key_exists($row["id_bp"], $lPartners))
             {
                if ($row["ts_edit"] > $lPartners[$row["id_bp"]]->updated_at ||
                      $row["ts_del"] > $lPartners[$row["id_bp"]]->updated_at)
                {
                    $lPartners[$row["id_bp"]]->code = $row["fiscal_id"];
                    $lPartners[$row["id_bp"]]->name = $row["bp"];
                    $lPartners[$row["id_bp"]]->last_name = $row["lastname"];
                    $lPartners[$row["id_bp"]]->first_name = $row["firstname"];
                    $lPartners[$row["id_bp"]]->fiscal_id = $row["fiscal_id"];
                    $lPartners[$row["id_bp"]]->person_id = $row["fiscal_id"];
                    $lPartners[$row["id_bp"]]->external_id = $row["id_bp"];
                    $lPartners[$row["id_bp"]]->is_deleted = $row["b_del"];
                    $lPartners[$row["id_bp"]]->is_company = $row["b_co"];
                    $lPartners[$row["id_bp"]]->is_customer = $row["b_cus"];
                    $lPartners[$row["id_bp"]]->is_supplier = $row["b_sup"];
                    $lPartners[$row["id_bp"]]->is_related_party = $row["b_att_rel_pty"];

                    array_push($lPartnersToWeb, $lPartners[$row["id_bp"]]);
                }
             }
             else
             {
                array_push($lPartnersToWeb, SImportPartners::siieToSiieWeb($row));
             }
         }
      }
      else
      {
         echo "0 results";
      }
       $this->webcon->close();

       foreach ($lPartnersToWeb as $key => $partner) {
         $partner->save();
       }
  }

  private static function siieToSiieWeb($oSiiePartner = '')
  {
     $oPartner = new SPartner();
     $oPartner->code = $oSiiePartner["fiscal_id"];
     $oPartner->name = $oSiiePartner["bp"];
     $oPartner->last_name = $oSiiePartner["lastname"];
     $oPartner->first_name = $oSiiePartner["firstname"];
     $oPartner->fiscal_id = $oSiiePartner["fiscal_id"];
     $oPartner->person_id = $oSiiePartner["fiscal_id"];
     $oPartner->external_id = $oSiiePartner["id_bp"];
     $oPartner->is_deleted = $oSiiePartner["b_del"];
     $oPartner->is_company = $oSiiePartner["b_co"];
     $oPartner->is_customer = $oSiiePartner["b_cus"];
     $oPartner->is_supplier = $oSiiePartner["b_sup"];
     $oPartner->is_related_party = $oSiiePartner["b_att_rel_pty"];
     $oPartner->created_by_id = 1;
     $oPartner->updated_by_id = 1;
     $oPartner->created_at = $oSiiePartner["ts_new"];
     $oPartner->updated_at = $oSiiePartner["ts_edit"];

     return $oPartner;
  }
}
