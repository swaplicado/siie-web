<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\SUtils\SProcess;
use App\SUtils\SGuiUtils;
use App\SCore\SLinkSupplyCore;
use App\WMS\Data\SData;

use App\ERP\SDocument;
use App\WMS\SMovement;
use App\WMS\SIndSupplyLink;
use App\WMS\SIndSupplyLinkLot;

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

    /**
     * [link description]
     * @param  integer $iDocSource  [description]
     * @param  integer $iDocDestiny [description]
     * @return [type]               [description]
     */
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

        if ($iDocSource == 0) {
            $lDocuments = SDocument::where('doc_src_id', $iDocDestiny)
                                      ->where('is_deleted', false)
                                      ->get();

            $lMovements = array();
            foreach ($lDocuments as $oDoc) {
               $lMovs = SLinkSupplyCore::getSupplyOfDocument($oDoc);

               foreach ($lMovs as $mov) {
                  $mov->branch;
                  $mov->warehouse;

                  foreach ($mov->rows as $row) {
                     $row->item;

                     foreach ($row->lotRows as $lotRow) {
                         $lotRow->lot;
                     }
                  }

                  array_push($lMovements, $mov);
               }
            }
        }
        else {
            $lMovements = SLinkSupplyCore::getSupplyOfDocument($oDocumentSrc);

            foreach ($lMovements as $mov) {
               $mov->branch;
               $mov->warehouse;

               foreach ($mov->rows as $row) {
                  $row->item;

                  foreach ($row->lotRows as $lotRow) {
                      $lotRow->lot;
                  }
               }
            }
        }

        return view('wms.movs.supplies.links')
                          ->with('oDocumentSrc', $oDocumentSrc)
                          ->with('oDocumentDes', $oDocumentDes)
                          ->with('lDocData', $lDocData)
                          ->with('lMovements', $lMovements)
                          ->with('actualUserPermission', $this->oCurrentUserPermission)
                          ->with('title', '$sTitle');
    }

    public function storeLinks(Request $request)
    {
        $oPackageJs = json_decode($request->input('spackage_object'));

        $lMovements = array();
        foreach ($oPackageJs->lMovements as $mov) {
           $key = $mov['0'];
           $lMovements[$key] = $mov['1'];

           $lRows = array();
           foreach ($lMovements[$key]->lAuxRows as $row) {
               $keyRow = $row['0'];
               $lRows[$keyRow] = $row['1'];

               $lLotRows = array();
               foreach ($lRows[$keyRow]->lAuxlotRows as $lotRow) {
                   $keyLotRow = $lotRow['0'];
                   $lLotRows[$keyLotRow] = $lotRow['1'];
               }
               $lRows[$keyRow]->lotRows = $lLotRows;
           }
           $lMovements[$key]->rows = $lRows;
        }

        \DB::connection('company')->transaction(function() use ($lMovements) {
            foreach ($lMovements as $oMovement) {
               foreach ($oMovement->rows as $oMovRow) {
                  if ($oMovRow->dAuxQuantity > 0) {
                      $oSuppLink = new SIndSupplyLink();

                      $oSuppLink->quantity = $oMovRow->dAuxQuantity;
                      $oSuppLink->is_deleted = false;
                      $oSuppLink->src_doc_row_id = $this->getDocReferenceId($oMovRow);
                      $oSuppLink->des_doc_row_id = $oMovRow->iAuxDocRowId;
                      $oSuppLink->mvt_row_id = $oMovRow->iIdMovRow;
                      $oSuppLink->pallet_id = $oMovRow->iPalletId;
                      $oSuppLink->created_by_id = \Auth::user()->id;
                      $oSuppLink->updated_by_id = \Auth::user()->id;

                      $oSuppLink->save();

                      if ($oMovRow->bIsLot && sizeof($oMovRow->lotRows) > 0) {
                          $lLinkLots = array();
                          foreach ($oMovRow->lotRows as $oLotRow) {
                             $oSuppLinkLot = new SIndSupplyLinkLot();

                             $oSuppLinkLot->quantity = $oLotRow->dAuxQuantity;
                             $oSuppLinkLot->is_deleted = false;
                             $oSuppLinkLot->lot_id = $oLotRow->iLotId;
                             $oSuppLinkLot->mvt_row_lot_id = $oLotRow->iIdLotRow;

                             array_push($lLinkLots, $oSuppLinkLot);
                          }

                          $oSuppLink->linkLots()->saveMany($lLinkLots);
                      }
                  }
               }
            }
        });

        $oDocumentDes = SDocument::find($oPackageJs->iDocumentDestinyId);

        Flash::success(trans('messages.SUCCESS_SUPP'))->important();

        return redirect()->route('wms.docs.index', [$oDocumentDes->doc_category_id,
                                                      $oDocumentDes->doc_class_id,
                                                      $oDocumentDes->doc_type_id,
                                                      \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                      \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                      $this->getTitle($oDocumentDes->doc_category_id, $oDocumentDes->doc_class_id, $oDocumentDes->doc_type_id),
                                                      ]);
    }

    private function getDocReferenceId($oMovRow)
    {
        if ($oMovRow->iDocOrderRowId > 1) {
            return $oMovRow->iDocOrderRowId;
        }
        elseif ($oMovRow->iDocInvoiceRowId > 1) {
            return $oMovRow->iDocInvoiceRowId;
        }
        elseif ($oMovRow->iDocDebitNoteRowId > 1) {
            return $oMovRow->iDocDebitNoteRowId;
        }
        elseif ($oMovRow->iDocCreditNoteRowId > 1) {
            return $oMovRow->iDocCreditNoteRowId;
        }
        else {
            return 1;
        }
    }

    private function getTitle($iDocCategory, $iDocClass, $iDocType)
    {
        switch ($iDocClass) {
          case \Config::get('scsiie.DOC_CLS.DOCUMENT'):
            if ($iDocCategory == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
                return trans('userinterface.titles.LIST_INVS_PUR_BY_SUPP');
            }
            else {
                return trans('userinterface.titles.LIST_INVS_SAL_BY_SUPP');
            }
            break;

          case \Config::get('scsiie.DOC_CLS.ORDER'):
            if ($iDocCategory == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
                return trans('userinterface.titles.LIST_OR_PUR_BY_SUPP');
            }
            else {
                return trans('userinterface.titles.LIST_OR_SAL_BY_SUPP');
            }
            break;

          case \Config::get('scsiie.DOC_CLS.ADJUST'):
            if ($iDocCategory == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
                return trans('userinterface.titles.LIST_CN_PUR_BY_SUPP');
            }
            else {
                return trans('userinterface.titles.LIST_CN_SAL_BY_SUPP');
            }
            break;

          default:
                return '';
            break;
        }

    }

    public function getIndirectSupplied(Request $request)
    {
       $aRows = json_decode($request->value);
       $oData = new SData();

       foreach ($aRows as $row) {
          $row->dQtyIndSupplied = SLinkSupplyCore::getIndirectSupplyRow($row->iIdMovRow, $row->iDocRowIndSupp);

          foreach ($row->lAuxlotRows as $rowLot) {
             $rowLot[1]->dQuantitySupplied = SLinkSupplyCore::getIndirectSupplyRowLot($rowLot[1]->iIdLotRow, $row->iDocRowIndSupp);
          }
       }

       $oData->lRowsSupplied = $aRows;

       return json_encode($oData);
    }

  }
