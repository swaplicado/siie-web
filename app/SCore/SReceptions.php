<?php namespace App\SCore;

use App\WMS\SMovement;

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
          (SUM(IF(wm.mvt_whs_class_id = 1, wmr.quantity, 0))) AS increment,
          (SUM(IF(wm.mvt_whs_class_id = 2, wmr.quantity, 0))) AS decrement,
          wm1.branch_id AS src_branch,
          eb_src.name AS src_branch_name,
          wm.branch_id AS des_branch,
          eb_des.name AS des_branch_name
        ";

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_mvts AS wm')
                  ->join('wms_mvts AS wm1', 'wm.src_mvt_id', '=', 'wm1.id_mvt')
                  ->join('wms_mvt_rows AS wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                  ->join('erpu_branches AS eb_des', 'wm.branch_id', '=', 'eb_des.id_branch')
                  ->join('erpu_branches AS eb_src', 'wm1.branch_id', '=', 'eb_src.id_branch')
                  ->leftJoin('wms_mvt_row_lots AS wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                  ->select(\DB::raw($sSelect))
                  ->where('wm.is_deleted', false)
                  ->where('wm.whs_id', session('transit_whs')->id_whs)
                  ->where('wm.src_mvt_id', '>', 1)
                  // ->where('wm.branch_id', $iBranch)
                  ;

        $lResult = $query->get();

        return $lResult;
    }
}
