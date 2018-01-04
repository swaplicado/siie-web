<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;
use Carbon\Carbon;

use App\ERP\SDocument;

class SDocsBySuppController extends Controller {

    private $oCurrentUserPermission;
    private $iFilter;
    private $iFilterBp;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.DOCUMENTS_MANAGE'), \Config::get('scsys.MODULES.WMS'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * [getBaseQuery description]
     * @param  [type] $iDocCategory [description]
     * @param  [type] $iDocClass    [description]
     * @return [type]               [description]
     */
    private function getBaseQuery($iDocCategory, $iDocClass, $iDocType)
    {
       $sub = $this->getSubQuery();
       $query = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('erpu_documents AS ed')
                     ->join('erpu_document_rows AS edr', 'ed.id_document', '=', 'edr.document_id')
                     ->join('erpu_items AS ei', 'edr.item_id', '=', 'ei.id_item')
                     ->join('erpu_units AS eu', 'edr.unit_id', '=', 'eu.id_unit')
                     ->join('erpu_partners AS ep', 'ed.partner_id', '=', 'ep.id_partner')
                     ->leftJoin('wms_mvt_rows AS wmr',
                       function($join) use ($sub)
                       {
                         $join->on('wmr.doc_order_row_id', '=', 'edr.id_document_row')
                              ->on('edr.document_id', '=', \DB::raw("({$sub->toSql()})"));
                       })
                     ->mergeBindings($sub)
                     ->where('doc_category_id', $iDocCategory)
                     ->where('doc_class_id', $iDocClass)
                     ->where('doc_type_id', $iDocType);

       return $query;
    }

    /**
     * [getSubQuery description]
     * @return [type] [description]
     */
    private function getSubQuery()
    {
        $sub = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_mvts')
                      ->select('doc_order_id')
                      ->whereRaw('id_mvt = wmr.mvt_id');

        return $sub;
    }

    /**
     * [filterActives description]
     * @param  [type] $lDocuments [description]
     * @param  [type] $iFilterDel [description]
     * @return [type]             [description]
     */
    private function filterActives($lDocuments, $iFilterDel)
    {
        switch ($iFilterDel)
        {
          case \Config::get('scsys.FILTER.ACTIVES'):
            $lDocuments = $lDocuments->where('ed.is_deleted', '=', \Config::get('scsys.STATUS.ACTIVE'));
            break;

          case \Config::get('scsys.FILTER.DELETED'):
            $lDocuments = $lDocuments->where('ed.is_deleted', '=', \Config::get('scsys.STATUS.DEL'));
            break;

          case \Config::get('scsys.FILTER.ALL'):
          // return $lDocuments;
            break;
        }

        return $lDocuments;
    }

    /**
     * [filterDate description]
     * @param  [type] $lDocuments [description]
     * @param  string $sDtFilter  [description]
     * @return [type]             [description]
     */
    public function filterDate($lDocuments, $sDtFilter = '')
    {
        $sFilterDate = $sDtFilter == null ? \Config::get('scsys.FILTER.MONTH') : $sDtFilter;

        if (! is_null($sFilterDate) && $sFilterDate != '') {
            $sStartDate = substr($sFilterDate, 0, 10);
            $sStartDate = str_replace('/', '-', $sStartDate);
            $sEndDate = substr($sFilterDate, -10, 10);
            $sEndDate = str_replace('/', '-', $sEndDate);
            $dt = Carbon::parse($sStartDate);
            $dtF = Carbon::parse($sEndDate);
            $lDocuments =  $lDocuments->whereBetween('dt_doc', [$dt->toDateString(), $dtF->toDateString()]);
        }

        return $lDocuments;
    }

    /**
     * [viewDocs description]
     *
     * @param  Request $request
     * @param  int  $iDocCategory
     * @param  int  $iDocClass
     * @param  int  $iViewType  [
     *                           \Config::get('scwms.DOC_VIEW.NORMAL'),
     *                           \Config::get('scwms.DOC_VIEW.DETAIL')
     *                            ]
     * @param  string  $sTitle
     * @return view  wms.docs.index
     */
    public function viewDocs(Request $request, $iDocCategory, $iDocClass, $iDocType, $iViewType, $sTitle)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $sFilterDate = $request->filterDate == null ? \Config::get('scsys.FILTER.MONTH') : $request->filterDate;

        $lDocuments = $this->getBaseQuery($iDocCategory, $iDocClass, $iDocType);

        if ($iViewType == \Config::get('scwms.DOC_VIEW.NORMAL')) {
            $lDocuments =  $lDocuments->select('id_document',
                                  'dt_date',
                                  'dt_doc',
                                  'num',
                                  'is_closed',
                                  'ed.external_id',
                                  'ed.partner_id',
                                  'ed.is_deleted',
                                  'ep.name',
                                  'ep.fiscal_id',
                                  'ep.code as cve_an',
                                  \DB::raw('SUM(edr.quantity) AS qty_doc'),
                                  \DB::raw('COALESCE(SUM(wmr.quantity), 0) AS qty_sur'),
                                  \DB::raw('(COALESCE(SUM(wmr.quantity), 0) * 100)/SUM(edr.quantity) AS advance'),
                                  \DB::raw('(SUM(edr.quantity) - COALESCE(SUM(wmr.quantity), 0))  AS pending')
                          )
                          ->groupBy('edr.document_id')
                          ->orderBy('dt_doc', 'desc');
        }
        else {
            $lDocuments =  $lDocuments->select('id_document',
                                  'dt_date',
                                  'dt_doc',
                                  'num',
                                  'is_closed',
                                  'ed.external_id',
                                  'ed.partner_id',
                                  'ed.is_deleted',
                                  'ep.name',
                                  'ep.fiscal_id',
                                  'ep.code as cve_an',
                                  'ei.code as cve_item',
                                  'ei.name as item',
                                  'eu.code as unit',
                                  \DB::raw('edr.quantity AS qty_row'),
                                  \DB::raw('COALESCE(SUM(wmr.quantity), 0) AS qty_sur'),
                                  \DB::raw('(COALESCE(SUM(wmr.quantity), 0) * 100)/edr.quantity AS advance'),
                                  \DB::raw('(edr.quantity - COALESCE(SUM(wmr.quantity), 0))  AS pending')
                          )
                          ->groupBy('edr.id_document_row')
                          ->groupBy('edr.document_id')
                          ->orderBy('document_id', 'document_id');
        }

        $lDocuments = $this->filterActives($lDocuments, $this->iFilter);
        $lDocuments = $this->filterDate($lDocuments, $sFilterDate);
        $lDocuments = $lDocuments->get();

        return view('wms.docs.index')
                              ->with('iFilter', $this->iFilter)
                              ->with('sFilterDate', $sFilterDate)
                              ->with('iDocCategory', $iDocCategory)
                              ->with('iDocClass', $iDocClass)
                              ->with('iDocType', $iDocType)
                              ->with('actualUserPermission', $this->oCurrentUserPermission)
                              ->with('documents', $lDocuments)
                              ->with('iViewType', $iViewType)
                              ->with('title', $sTitle);
    }

  }
