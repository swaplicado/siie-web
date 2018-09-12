<?php

namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Database\Config;
use App\SUtils\SMovsUtils;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;
use App\SUtils\SValidation;
use App\SUtils\SGuiUtils;

class SMovsQuerysController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.STK_MOVS'), \Config::get('scsys.MODULES.MMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    private function getProductionMovements()
    {
          $oQuery = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('wms_mvt_rows as wmr')
                        ->join('wms_mvts as wm', 'wmr.mvt_id', '=', 'wm.id_mvt')
                        ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                        ->join('erpu_items as ei', 'wmr.item_id', '=', 'ei.id_item')
                        ->join('erpu_units as eu', 'wmr.unit_id', '=', 'eu.id_unit')
                        ->join('wms_pallets as wp', 'wmr.pallet_id', '=', 'wp.id_pallet')
                       ->join('mms_production_orders as mpo', 'wm.prod_ord_id', '=', 'mpo.id_order')
                       ->join('wmss_mvt_trn_types as wmtt', 'wm.mvt_trn_type_id', '=', 'wmtt.id_mvt_trn_type')
                       ->join('wmss_mvt_adj_types as wmat', 'wm.mvt_adj_type_id', '=', 'wmat.id_mvt_adj_type')
                       ->join('wmss_mvt_mfg_types as wmmt', 'wm.mvt_mfg_type_id', '=', 'wmmt.id_mvt_mfg_type')
                       ->join('wmss_mvt_exp_types as wmet', 'wm.mvt_exp_type_id', '=', 'wmet.id_mvt_exp_type')
                       ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                       ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'wm.created_by_id', '=', 'uc.id')
                       ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'wm.updated_by_id', '=', 'uu.id');

         return $oQuery;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $iQueryType
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $iQueryType = 0, $sTitle = '')
    {
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;
        $iFilterWhs = $request->warehouse == null ? \Config::get('scwms.FILTER_ALL_WHS') : $request->warehouse;
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

        $lWarehouses = session('utils')->getUserWarehousesArrayWithName(0, session('branch')->id_branch, false);
        $lWarehouses['0'] = 'TODOS';

        $sSelect = '
                wm.id_mvt,
                wmt.code AS mov_code,
                wm.folio AS mov_folio,
                wm.dt_date AS mov_date,
                ei.code AS item_code,
                ei.name AS item,
                eu.code AS unit_code,
                wmr.quantity,
                eb.code AS branch_code,
                eb.name AS branch,
                ww.code AS whs_code,
                ww.name AS warehouse,
                wmt.name AS movement,
                wm.mvt_whs_class_id,
                wm.mvt_trn_type_id,
                wmtt.code AS trn_code,
                wmtt.name AS trn_name,
                wm.mvt_adj_type_id,
                wmat.code AS adj_code,
                wmat.name AS adj_name,
                wm.mvt_mfg_type_id,
                wmmt.code AS mfg_code,
                wmmt.name AS mfg_name,
                wm.mvt_exp_type_id,
                wmet.code AS exp_code,
                wmet.name AS exp_name,
                wm.prod_ord_id,
                mpo.folio AS po_folio,
                wm.is_deleted,
                wm.created_at,
                wm.updated_at,
                wm.created_by_id,
                wm.updated_by_id,
                uc.username AS username_creation,
                uu.username AS username_update
                ';

        $oQuery = $this->getProductionMovements();

        $oQuery = $oQuery->whereIn('whs_id', session('utils')->getUserWarehousesArray());

        if ($iFilterWhs != \Config::get('scwms.FILTER_ALL_WHS')) {
            $oQuery = $oQuery->where('wm.whs_id', $iFilterWhs);
        }

        switch ($this->iFilter) {
          case \Config::get('scsys.FILTER.ACTIVES'):
              $oQuery = $oQuery->where('wm.is_deleted', \Config::get('scsys.STATUS.ACTIVE'));
            break;

          case \Config::get('scsys.FILTER.DELETED'):
              $oQuery = $oQuery->where('wm.is_deleted', \Config::get('scsys.STATUS.DEL'));
            break;

          default:
        }

        $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);

        $oQuery = $oQuery->whereBetween('wm.dt_date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);

        switch ($iQueryType) {
          case \Config::get('scmms.MOVS_QUERY.RM_DELIVERY'):
              $oQuery = $oQuery->where(function ($querytemp) {
                                    $querytemp->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_RM'))
                                          ->orWhere('wm.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_DLVRY_RM'));
                                })
                                ->where('mvt_mfg_type_id', \Config::get('scwms.MVT_MFG_TP_MAT'));
            break;

          case \Config::get('scmms.MOVS_QUERY.RM_RETURN'):
              $oQuery = $oQuery->where(function ($querytemp) {
                                    $querytemp->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_RTRN_RM'))
                                          ->orWhere('wm.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_RTRN_RM'));
                                })
                                ->where('mvt_mfg_type_id', \Config::get('scwms.MVT_MFG_TP_MAT'));
            break;

          case \Config::get('scmms.MOVS_QUERY.PM_DELIVERY'):
              $oQuery = $oQuery->where(function ($querytemp) {
                                    $querytemp->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_RM'))
                                          ->orWhere('wm.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_DLVRY_RM'));
                                })
                                ->where('mvt_mfg_type_id', \Config::get('scwms.MVT_MFG_TP_PACK'));
            break;

          case \Config::get('scmms.MOVS_QUERY.PM_RETURN'):
              $oQuery = $oQuery->where(function ($querytemp) {
                                    $querytemp->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_RTRN_RM'))
                                          ->orWhere('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_RTRN_RM'));
                                })
                                ->where('mvt_mfg_type_id', \Config::get('scwms.MVT_MFG_TP_PACK'));
            break;

          case \Config::get('scmms.MOVS_QUERY.PP_DELIVERY'):
              $oQuery = $oQuery->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_PP'));
            break;

          case \Config::get('scmms.MOVS_QUERY.PP_ASSIGNAMENT'):
              $oQuery = $oQuery->where(function ($querytemp) {
                                    $querytemp->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_ASSIGN_PP'))
                                          ->orWhere('wm.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_ASSIGN_PP'));
                                });
            break;

          case \Config::get('scmms.MOVS_QUERY.FP_DELIVERY'):
              $oQuery = $oQuery->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_IN_DLVRY_FP'));
            break;

          case \Config::get('scmms.MOVS_QUERY.CONSUMPTION_MVTS'):
              $oQuery = $oQuery->where('wm.mvt_whs_type_id', \Config::get('scwms.MVT_OUT_CONSUMPTION'));
            break;

          default:
            // code...
            break;
        }

        $oQuery = $oQuery->where('wm.is_deleted', false)
          ->where('wm.branch_id', session('branch')->id_branch)
          ->select(\DB::raw($sSelect))
          ->groupBy('id_mvt')
          ->get();

        return view('mms.movs.query')
                    ->with('lWarehouses', $lWarehouses)
                    ->with('iFilterWhs', $iFilterWhs)
                    ->with('sFilterDate', $sFilterDate)
                    ->with('iFilter', $this->iFilter)
                    ->with('iQueryType', $iQueryType)
                    ->with('sTitle', $sTitle)
                    ->with('lMovs', $oQuery);
    }

}
