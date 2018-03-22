<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\SUtils\SProcess;
use App\SUtils\SGuiUtils;
use App\SCore\SLinkSupplyCore;

use App\ERP\SDocument;
use App\WMS\SMovement;

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
     * @param  $iSuppType  can be:
   *                              \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
   *                              \Config::get('scwms.DOC_VIEW_S.SUPP'),
     * @param  string  $sTitle
     * @return view  wms.docs.index
     */
    public function viewDocs(Request $request, $iDocCategory, $iDocClass, $iDocType, $iViewType, $iSuppType, $sTitle)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $sFilterDate = null;
        $iDocId = 0;
        $bWithPending = $iSuppType == \Config::get('scwms.DOC_VIEW_S.SUPP');

        $lDocuments = session('stock')->getSuppliedRes($iDocCategory, $iDocClass,
                                                    $iDocType, $this->iFilter,
                                                    $sFilterDate, $iViewType,
                                                    $iDocId, $bWithPending);
        $sView = '';
        if ($iSuppType == \Config::get('scwms.DOC_VIEW_S.BY_SUPP')) {
            $lDocuments = $lDocuments->where('ed.is_closed', false);
            $sView = 'wms.docs.index';
            $lDocuments = $lDocuments->get();
        }
        else {
            $sView = 'wms.docs.indexsupp';
            $lAux = $lDocuments->get();

            $lDocuments = array();
            foreach ($lAux as $row) {
              if ($row->pending == 0 || $row->is_closed) {
                array_push($lDocuments, $row);
              }
            }
        }

        return view($sView)
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

    /**
     * close the document to be supplied
     * prevents the document from being stocked
     * (set the flag is_closed to document)
     *
     * @param  integer $iOperation can be:
     *                                    \Config::get('scsiie.DOC_OPER.CLOSE')
     *                                    \Config::get('scsiie.DOC_OPER.OPEN')
     * @param  integer $iDocId
     */
    public function openAndclose($iOperation = 0, $iDocId = 0)
    {
       $oDocument = SDocument::find($iDocId);

       session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oDocument);

       /*
         This method tries to get the lock, if not is obtained returns an array of errors
        */
       $error = session('utils')->validateLock($oDocument);
       if (sizeof($error) > 0)
       {
         return redirect()->back()->withErrors($error);
       }

       $oDocument->is_closed = ($iOperation == \Config::get('scsiie.DOC_OPER.CLOSE'));
       $oDocument->updated_by_id = \Auth::user()->id;

       $errors = $oDocument->save();
       if (sizeof($errors) > 0)
       {
          return redirect()->back()->withErrors($errors);
       }

       if ($iOperation == \Config::get('scsiie.DOC_OPER.CLOSE')) {
          Flash::info(trans('messages.DOC_CLOSED'))->important();
       }
       else {
          Flash::success(trans('messages.DOC_OPENED'))->important();
       }

       return redirect()->back();
    }

    public function link($iDocSource = 0, $iDocDestiny = 0)
    {
        $oDocumentSrc = SDocument::find($iDocSource);
        $oDocumentDes = SDocument::find($iDocDestiny);

        $FilterDel = \Config::get('scsys.FILTER.ACTIVES');
        $sFilterDate = null;
        $iViewType = \Config::get('scwms.DOC_VIEW.DETAIL');
        $bWithPending = true;

        $lDocData = session('stock')::getSuppliedRes($oDocumentDes->doc_category_id,
                                        $oDocumentDes->doc_class_id,
                                        $oDocumentDes->doc_type_id, $FilterDel,
                                        $sFilterDate, $iViewType, $iDocDestiny,
                                        $bWithPending)->get();

        $lMovements = SLinkSupplyCore::getSupplyOfDocument($oDocumentSrc);

        return view('wms.movs.supplies.links')
                          ->with('oDocumentSrc', $oDocumentSrc)
                          ->with('oDocumentDes', $oDocumentDes)
                          ->with('lDocData', $lDocData)
                          ->with('lMovements', $lMovements)
                          ->with('actualUserPermission', $this->oCurrentUserPermission)
                          ->with('title', '$sTitle');
    }

  }
