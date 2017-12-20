<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

use App\ERP\SDocument;

class SDocumentsController extends Controller {

    private $oCurrentUserPermission;
    private $iFilter;
    private $iFilterBp;
    private $sClassNav;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.DOCUMENTS_MANAGE'), \Config::get('scsys.MODULES.ERP'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    public function index(Request $request, $iDocCategory, $iDocClass, $sTitle)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

        $lDocuments = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('erpu_documents AS ed')
                      ->join('erpu_partners AS ep', 'ed.partner_id', '=', 'ep.id_partner')
                      ->join('erps_currencies AS ec', 'ed.currency_id', '=', 'ec.id_currency')
                      ->where('doc_category_id', $iDocCategory)
                      ->where('doc_class_id', $iDocClass)
                      ->select('id_document',
                              'dt_date',
                              'dt_doc',
                              'num',
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

          switch ($this->iFilter)
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


          $lDocuments = $lDocuments->get();

        // dd($lDocuments);

        return view('siie.docs.index')
                              ->with('iFilter', $this->iFilter)
                              ->with('iDocCategory', $iDocCategory)
                              ->with('iDocClass', $iDocClass)
                              ->with('actualUserPermission', $this->oCurrentUserPermission)
                              ->with('documents', $lDocuments)
                              ->with('title', $sTitle);
    }

    public function view($iDocumentId)
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

  }
