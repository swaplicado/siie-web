<?php namespace App\SCore;

use App\WMS\SMovement;
use App\Database\Config;

/**
 *
 */
class SReceptions
{
    public static function getPendingReceptions($iBranch)
    {
        $sSelect = "
          wm.id_mvt,
          wm.src_mvt_id,
          wm.dt_date,
          wm.folio,
          (SELECT SUM(quantity) FROM wms_mvt_rows WHERE mvt_id = mvt_reference_id) AS total_quantity,
          (SUM(IF(wm1.mvt_whs_class_id = 2, wmr1.quantity, 0))) AS received,
          wet.src_branch_id AS src_branch,
          wet.des_branch_id AS des_branch,
          eb_src.name AS src_branch_name,
          eb_des.name AS des_branch_name,
          u.username
        ";

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_external_transfers AS wet')
                  ->join('erpu_branches AS eb_src', 'wet.src_branch_id', '=', 'eb_src.id_branch')
                  ->join('erpu_branches AS eb_des', 'wet.des_branch_id', '=', 'eb_des.id_branch')
                  ->join('wms_mvts AS wm', 'wet.mvt_reference_id', '=', 'wm.id_mvt')
                  ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users AS u', 'wm.created_by_id', '=', 'u.id')
                  ->leftJoin('wms_mvts AS wm1', 'wm.id_mvt', '=', 'wm1.src_mvt_id')
                  ->leftJoin('wms_mvt_rows AS wmr1', 'wm1.id_mvt', '=', 'wmr1.mvt_id')
                  ->where('wm.is_deleted', false)
                  ->where('wm.whs_id', session('transit_whs')->id_whs)
                  ->where('wet.des_branch_id', $iBranch)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('wm.id_mvt')
                  ;

        $lResult = $query->get();

        if (sizeof($lResult) == 0 || (sizeof($lResult) == 1 && $lResult[0]->id_mvt == null)) {
           return array();
        }

        return $lResult;
    }
}
