<?php
namespace App\Http\Controllers\ERP;

use App\SUtils\SDocumentsUtils;
use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

use App\ERP\SDocument;

class SDocumentsController extends Controller
{

  private $oCurrentUserPermission;
  private $iFilter;
  private $iFilterBp;
  private $sClassNav;

  public function __construct()
  {
    $this->oCurrentUserPermission = SProcess::constructor(
      $this,
      \Config::get('scperm.PERMISSION.DOCUMENTS_MANAGE'), \Config::get('scsys.MODULES.ERP')
    );

    $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
  }

  /**
   * display a list of documents that were imported from siie depending of cat, class, and doc type
   *
   * @param  Request $request
   * @param  integer $iDocCategory
   * @param  integer $iDocClass
   * @param  integer $iDocType
   * @param  string  $sTitle
   *
   * @return \Illuminate\Http\Response|\Illuminate\View\View
   */
  public function index(Request $request, $iDocCategory = 0, $iDocClass = 0, $iDocType = 0, $sTitle = '')
  {
    $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

    $lDocuments = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('erpu_documents AS ed')
                            ->join('erpu_partners AS ep', 'ed.partner_id', '=', 'ep.id_partner')
                            ->join('erps_currencies AS ec', 'ed.currency_id', '=', 'ec.id_currency')
                            ->where('doc_category_id', $iDocCategory)
                            ->where('doc_class_id', $iDocClass)
                            ->where('doc_type_id', $iDocType)
                            ->select(
                              'id_document',
                              'dt_date',
                              'dt_doc',
                              'num',
                              'service_num',
                              'subtotal',
                              'tax_charged',
                              'tax_retained',
                              'total',
                              'exchange_rate',
                              'exchange_rate_sys',
                              'subtotal_cur',
                              'tax_charged_cur',
                              'tax_retained_cur',
                              'total_cur',
                              'is_closed',
                              'ed.external_id',
                              'ed.partner_id',
                              'ed.is_deleted',
                              'ep.name',
                              'ep.fiscal_id',
                              'ec.code as cur_code'
                            );

    switch ($this->iFilter) {
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

    $lDocuments = $lDocuments->get();

    return view('siie.docs.index')
                              ->with('iFilter', $this->iFilter)
                              ->with('iDocCategory', $iDocCategory)
                              ->with('iDocClass', $iDocClass)
                              ->with('iDocType', $iDocType)
                              ->with('actualUserPermission', $this->oCurrentUserPermission)
                              ->with('documents', $lDocuments)
                              ->with('title', $sTitle);
  }

  /**
   * display a Document with rows in a view
   *
   * @param  integer $iDocumentId primary key of SDocument
   *
   * @return \Illuminate\Http\Response|\Illuminate\View\View
   */
  public function view($iDocumentId = 0)
  {
    $oDocument = SDocument::find($iDocumentId);
    $oDocument->rows;

    foreach ($oDocument->rows as $key => $row) {
      $row->unit;
    }

    $oDocument->partner;
    $oDocument->currency;

    return view('siie.docs.view')->with('document', $oDocument)
      ->with('title', 'Documento a detalle');
  }

  public function fixDocuments(Request $request, $year)
  {
    SDocumentsUtils::fixDocuments($year);

    return redirect()->route('wms.home');
  }
}