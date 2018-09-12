<?php namespace App\SCore;

use App\WMS\SMovement;
use App\Database\Config;

/**
 *
 */
class SReceptions
{
    /**
     * obtains the transfers to be received at the branch
     *
     * @param  integer $iBranch id of branch
     *
     * @return array result of query
     */
    public static function getPendingReceptions($iBranch)
    {
        $sSelect = "
          wm.id_mvt,
          wm.src_mvt_id,
          wm.dt_date,
          wm.folio,
          (SELECT SUM(quantity) FROM
            wms_mvt_rows WHERE
            mvt_id = mvt_reference_id
            AND is_deleted = FALSE) AS total_quantity,
          COALESCE((SELECT SUM(quantity) FROM wms_mvt_rows where mvt_id IN
		       (SELECT id_mvt FROM wms_mvts WHERE src_mvt_id = mvt_reference_id AND NOT is_deleted)
            AND NOT is_deleted), 0) AS received,
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
                  ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users AS u', 'wet.created_by_id', '=', 'u.id')
                  ->where('wm.is_deleted', false)
                  ->where('wm.whs_id', session('transit_whs')->id_whs)
                  ->where('wet.des_branch_id', $iBranch)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('wm.id_mvt')
                  ->orderBy('wm.folio');
                  ;

        $lResult = $query->get();

        if (sizeof($lResult) == 0 || (sizeof($lResult) == 1 && $lResult[0]->id_mvt == null)) {
           return array();
        }

        return $lResult;
    }

    /**
     * get the transfers that have been sent to other branches
     *
     * @param  integer $iBranch if of branch
     *
     * @return array result of query
     */
    public static function getTransferredTransfers($iBranch = 0)
    {
        $sSelect = 'mvt_reference_id,
                    wm1.id_mvt,
                    wm1.dt_date,
                    wmt.code AS mov_code,
                    wmt.name AS mov_name,
                    wm1.folio,
                    (SELECT SUM(quantity) FROM wms_mvt_rows WHERE mvt_id = wm1.id_mvt
                                                AND is_deleted = false) AS total,
                    (SELECT SUM(quantity) FROM wms_mvt_rows WHERE mvt_id = wm1.id_mvt
                                                AND is_deleted = false) AS indicted,
                    wet.src_branch_id AS src_branch,
                    wet.des_branch_id AS des_branch,
                    eb_src.name AS src_branch_name,
                    eb_des.name AS des_branch_name,
                    wm1.created_by_id,
                    wm1.is_deleted,
                    u.username';

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_external_transfers AS wet')
                  ->join('wms_mvts AS wm', 'wet.mvt_reference_id', '=', 'wm.id_mvt')
                  ->join('wms_mvts AS wm1', 'wm.src_mvt_id', '=', 'wm1.id_mvt')
                  ->join('wmss_mvt_types AS wmt', 'wm1.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                  ->join('erpu_branches AS eb_src', 'wet.src_branch_id', '=', 'eb_src.id_branch')
                  ->join('erpu_branches AS eb_des', 'wet.des_branch_id', '=', 'eb_des.id_branch')
                  ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users AS u', 'wm.created_by_id', '=', 'u.id')
                  ->where('wm.is_deleted', false)
                  ->where('wm1.is_deleted', false)
                  ->where('wet.is_deleted', false)
                  ->where('wet.src_branch_id', $iBranch)
                  ->select(\DB::raw($sSelect))
                  ->orderBy('wmdes.dt_date', 'DESC');

        $query = $query->get();

        return $query;
    }

    /**
     * obtains the transfers that have already been received totally or partially
     *
     * @param  integer $iBranch if of branch
     *
     * @return array result of query
     */
    public static function getReceivedTransfers($iBranch = 0)
    {
        $sSelect = '
                    wmdes.id_mvt,
                    wm.id_mvt AS src_id_mvt,
                    wm.is_deleted AS src_is_deleted,
                    wmdes.dt_date,
                    wmt.code AS mov_code,
                    wmt.name AS mov_name,
                    wmdes.folio,
                    (SELECT SUM(quantity) FROM wms_mvt_rows WHERE mvt_id = wmsrc.id_mvt
                    AND is_deleted = false) AS total,
                    (SELECT SUM(quantity) FROM wms_mvt_rows WHERE mvt_id = wm.id_mvt
                                                AND is_deleted = false) AS indicted,
                    eb_src.id_branch AS src_branch,
                    0 AS des_branch,
                    eb_src.name AS src_branch_name,
                    \''.session('branch')->name.'\' AS des_branch_name,
                    u.username,
                    wm.created_by_id,
                    wm.is_deleted';

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_mvts AS wm')
                  ->join('wms_mvts AS wm1', 'wm.src_mvt_id', '=', 'wm1.id_mvt')
                  ->join('wms_mvts AS wmsrc', 'wm1.src_mvt_id', '=', 'wmsrc.id_mvt')
                  ->join('wms_mvts AS wmdes', 'wm.id_mvt', '=', 'wmdes.src_mvt_id')
                  ->join('erpu_branches AS eb_src', 'wmsrc.branch_id', '=', 'eb_src.id_branch')
                  ->join('wmss_mvt_types AS wmt', 'wmdes.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                  ->join(\DB::connection(Config::getConnSys())->getDatabaseName().
                                  '.users AS u', 'wmsrc.created_by_id', '=', 'u.id')
                  ->whereIn('wm.src_mvt_id', function($query) use ($iBranch)
                      {
                         $query->from('wms_external_transfers')
                          ->select('mvt_reference_id')
                          ->where('is_deleted', false)
                          ->where('des_branch_id', $iBranch);
                      })
                  ->where('wm.is_deleted', false)
                  ->select(\DB::raw($sSelect))
                  ->groupBy('wmdes.id_mvt')
                  ->orderBy('wmdes.dt_date', 'DESC');

        $query = $query->get();

        return $query;
    }
}
