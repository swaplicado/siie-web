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
        $iDocId = 0;

        $lDocuments = session('stock')->getSupplied($iDocCategory, $iDocClass, $iDocType, $this->iFilter, $sFilterDate, $iViewType, $iDocId);

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
