<?php

namespace App\Http\Controllers\QMS;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use App\Database\Config;
use App\SUtils\SConnectionUtils;
use App\SUtils\SProcess;
use App\QMS\SQDocument;
use App\QMS\SQMongoDoc;
use App\MMS\SProductionOrder;
use App\User;
use App\QMS\core\SQDocsCore;
use App\SUtils\SGuiUtils;

class SQDocumentsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_DOCUMENTS'), \Config::get('scsys.MODULES.QMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $fathero
     * @param [type] $sono
     * @param [type] $lot
     * @param int $cfgZone {
    *              \Config::get('scqms.CFG_ZONE.FQ')
    *              \Config::get('scqms.CFG_ZONE.QB')
    *              \Config::get('scqms.CFG_ZONE.OL')
     *          }
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $fathero, $sono, $lot, $cfgZone)
    {
        SConnectionUtils::reconnectCompany();

        $oSonPo = null;
        $oFatherPo = null;
        $oMongoDocument = null;
        $oDoc = SQDocument::where('is_deleted', false);

        if ($fathero > 0) {
            $oDoc = $oDoc->where('father_po_id', $fathero);
            $oFatherPo = SProductionOrder::find($fathero);
        }
        if ($sono > 0) {
            $oDoc = $oDoc->where('son_po_id', $sono);
            $oSonPo = SProductionOrder::find($sono);
        }
        if ($lot > 1) {
            $oDoc = $oDoc->where('lot_id', $lot);
        }

        $oDoc = $oDoc->orderBy('id_document', 'ASC')->first();

        if ($oDoc == null) {
            $oDoc = new SQDocument();

            $oDoc->title = '';
            $oDoc->dt_document = date("y-m-d");
            $oDoc->body_id = '';
            $oDoc->is_deleted = false;

            if ($lot > 1) {
                $oDoc->lot_id = $lot; 
            }
            else {
                $oDoc->lot_id = 1;
            }

            if ($oSonPo != null) {
                $oDoc->son_po_id = $oSonPo->id_order;
                $oDoc->item_id = $oSonPo->item_id;
                $oDoc->unit_id = $oSonPo->unit_id;
            }
            else {
                $oDoc->son_po_id = 1;
                $oDoc->item_id = 1;
                $oDoc->unit_id = 1;
            }

            if ($fathero > 0) {
                $oDoc->father_po_id = $fathero;
            }
            else {
                $oDoc->father_po_id = 1;
            }

            $oDoc->sup_quality_id = 1;
            $oDoc->sup_process_id = 1;
            $oDoc->sup_production_id = 1;
            $oDoc->signature_argox_id = 1;
            $oDoc->signature_coding_id = 1;
            $oDoc->signature_mb_id = 1;
            $oDoc->created_by_id = \Auth::user()->id;
            $oDoc->updated_by_id = \Auth::user()->id;

            $oDoc->save();
        }
        else {
            $bFlag = false;

            if (strlen($oDoc->body_id) > 0) {
                $oMongoDocument = SQMongoDoc::find($oDoc->body_id);
            }
            else {
                $oMongoDocument = null;
            }

            if ($oDoc->son_po_id == 1 && $sono > 1) {
                $oDoc = clone $oDoc;

                $oDoc->id_document = 0;
                $oDoc->son_po_id = $oSonPo->id_order;
                $oDoc->item_id = $oSonPo->item_id;
                $oDoc->unit_id = $oSonPo->unit_id;
                $oDoc->body_id = '';

                if ($oMongoDocument != null) {
                    $oMongoDocument->id = '';
                    $oMongoDocument->save();
                    $oDoc->body_id = $oMongoDocument->id;
                }

                $bFlag = true;
            }
            if ($oDoc->lot_id == 1 && $lot > 1) {
                $oDoc->lot_id = $lot;
                $bFlag = true;
            }

            if ($bFlag) {
                $oDoc->save();
            }
        }

        $lData = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_quality_documents as qqd')
                            ->join('erp_signatures as esa', 'qqd.signature_argox_id', '=', 'esa.id_signature')
                            ->join('erp_signatures as esc', 'qqd.signature_coding_id', '=', 'esc.id_signature')
                            ->join('erp_signatures as esm', 'qqd.signature_mb_id', '=', 'esm.id_signature')
                            ->join('wms_lots as wl', 'qqd.lot_id', '=', 'wl.id_lot')
                            ->join('mms_production_orders as mpof', 'qqd.father_po_id', '=', 'mpof.id_order')
                            ->join('mms_production_orders as mpos', 'qqd.son_po_id', '=', 'mpos.id_order')
                            ->join('erpu_items as ei', 'qqd.item_id', '=', 'ei.id_item')
                            ->join('erpu_units as eu', 'qqd.unit_id', '=', 'eu.id_unit')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as sq', 'qqd.sup_quality_id', '=', 'sq.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as sp', 'qqd.sup_process_id', '=', 'sp.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as supp', 'qqd.sup_production_id', '=', 'supp.id')
                            ->select(
                                    'qqd.id_document',
                                    'qqd.dt_document',
                                    'qqd.body_id',
                                    'qqd.title',
                                    'esa.signed AS b_argox',
                                    'esc.signed AS b_coding',
                                    'esm.signed AS b_mb',
                                    'wl.lot',
                                    'wl.dt_expiry',
                                    'mpof.folio AS father_folio',
                                    'mpof.identifier AS father_idr',
                                    'mpof.date AS father_date',
                                    'mpos.folio AS son_folio',
                                    'mpos.identifier AS son_idr',
                                    'mpos.date AS son_date',
                                    'ei.name AS item',
                                    'ei.code AS item_code',
                                    'eu.name AS unit',
                                    'eu.code AS unit_code',
                                    'sq.username AS sup_quality',
                                    'sq.id AS sup_quality_id',
                                    'sp.username AS sup_process',
                                    'sp.id AS sup_process_id',
                                    'supp.username AS sup_production',
                                    'supp.id AS sup_production_id'
                            );
        if ($sono > 0) {
            $lData = $lData->where('mpos.id_order', $sono);
        }

        $lData = $lData->where('mpof.id_order', $fathero)
                        ->first();

        $lUsers = User::where('is_deleted', false)
                            ->select('username', 'id')
                            ->orderBy('username', 'ASC')
                            ->whereNotIn('username', ['admin', 'adminswap', 
                                                        'saporis', 'contraloria',
                                                        'swap',
                                                        'consultas', 'manager'])
                            ->get();

        $aResult = SQDocsCore::getConfigurations($oFatherPo, $oSonPo, $cfgZone);

        return view('qms.docs.index')
                    ->with('oQDocument', $oDoc)
                    ->with('oMongoDocument', $oMongoDocument)
                    ->with('cfgZone', $cfgZone)
                    ->with('lSections', $aResult[0])
                    ->with('lConfigurations', $aResult[1])
                    ->with('lUsers', $lUsers)
                    ->with('aData', $lData);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function docs(Request $request)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;

        $lQltyDocs = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_quality_documents as qqd')
                            ->join('erp_signatures as esa', 'qqd.signature_argox_id', '=', 'esa.id_signature')
                            ->join('erp_signatures as esc', 'qqd.signature_coding_id', '=', 'esc.id_signature')
                            ->join('erp_signatures as esm', 'qqd.signature_mb_id', '=', 'esm.id_signature')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uesa', 'esa.signed_by_id', '=', 'uesa.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uesc', 'esc.signed_by_id', '=', 'uesc.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uesm', 'esm.signed_by_id', '=', 'uesm.id')
                            ->join('wms_lots as wl', 'qqd.lot_id', '=', 'wl.id_lot')
                            ->join('erpu_items as ei', 'qqd.item_id', '=', 'ei.id_item')
                            ->select('qqd.id_document',
                                        'qqd.lot_id',
                                        'qqd.dt_document',
                                        'qqd.title',
                                        'qqd.body_id',
                                        'esa.signed AS b_argox',
                                        'esc.signed AS b_coding',
                                        'esm.signed AS b_mb',
                                        'esa.created_at AS creation_argox',
                                        'esc.created_at AS creation_coding',
                                        'esm.created_at AS creation_mb',
                                        'uesa.username AS usr_argox',
                                        'uesc.username AS usr_coding',
                                        'uesm.username AS usr_mb',
                                        'wl.lot',
                                        'wl.dt_expiry',
                                        'ei.name AS item_name',
                                        'ei.code AS item_code'
                                    );

        $lQltyDocs = $lQltyDocs->get();

        return view('qms.docs.indexdocs')
                ->with('lQltyDocs', $lQltyDocs)
                ->with('actualUserPermission', $this->oCurrentUserPermission)
                ->with('sFilterDate', $sFilterDate)
                ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vDoc = json_decode($request->vdoc);
        $lResults = json_decode($request->results);
        $lConfigs = json_decode($request->configurations);
        $iZone = json_decode($request->zone);

        $oDoc = SQDocument::find($vDoc->id_document);

        $oDoc->sup_quality_id = $vDoc->sup_quality_id;
        $oDoc->sup_process_id = $vDoc->sup_process_id;
        $oDoc->sup_production_id = $vDoc->sup_production_id;
        $oDoc->updated_by_id = \Auth::user()->id;
        
        $oLot = $oDoc->lot;

        if (strlen($oDoc->body_id) > 0) {
            $oMongoDoc = SQMongoDoc::find($oDoc->body_id);
            
            $oMongoDoc->lot_date = SQDocsCore::getLotDate($oLot->lot)->format('Y-m-d');
            $oMongoDoc->lot = $oLot->lot;
            $oMongoDoc->dt_expiry = $oLot->dt_expiry;
            $oMongoDoc->lot_id = $oDoc->lot_id;
            $oMongoDoc->item_id = $oDoc->item_id;
            $oMongoDoc->unit_id = $oDoc->unit_id;
            $oMongoDoc->qlty_doc_id = $oDoc->id_document;
            
            switch ($iZone) {
                case \Config::get('scqms.CFG_ZONE.FQ'):
                    $oMongoDoc->results = $lResults;
                    break;
                case \Config::get('scqms.CFG_ZONE.MB'):
                    $oMongoDoc->resultsMb = $lResults;
                    break;
                
                default:
                    # code...
                    break;
            }

            $oMongoDoc->usr_upd = \Auth::user()->username;

            $oMongoDoc->save();
        }
        else {
            $oMongoDoc = new SQMongoDoc();

            $oMongoDoc->lot_date = SQDocsCore::getLotDate($oLot->lot)->format('Y-m-d');
            $oMongoDoc->lot = $oLot->lot;
            $oMongoDoc->dt_expiry = $oLot->dt_expiry;
            $oMongoDoc->lot_id = $oDoc->lot_id;
            $oMongoDoc->item_id = $oDoc->item_id;
            $oMongoDoc->unit_id = $oDoc->unit_id;
            $oMongoDoc->qlty_doc_id = $oDoc->id_document;

            switch ($iZone) {
                case \Config::get('scqms.CFG_ZONE.FQ'):
                    $oMongoDoc->results = $lResults;
                    break;
                case \Config::get('scqms.CFG_ZONE.MB'):
                    $oMongoDoc->resultsMb = $lResults;
                    break;
                
                default:
                    # code...
                    break;
            }

            $oMongoDoc->usr_creation = \Auth::user()->username;
            $oMongoDoc->usr_upd = \Auth::user()->username;

            try {
                $oMongoDoc->save();
                
                $oDoc->body_id = $oMongoDoc->id;
            }
            catch (\Throwable $th) {
                \Log::info($th);
            }

        }

        $oDoc->save();

        return json_encode($oDoc);
    }

    public function image(Request $request)
    {
        $file = $request->file('image');
        $destinationPath = 'uploads/qms';
        $dt = Carbon::now();
        $name = $dt->format('Y_m_d_h_m_s') . '.' . $file->getClientOriginalExtension();
        try {
            $file->move($destinationPath, $name);
        } catch (\Throwable $th) {
            return "Error";
        }

        return $name;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $idQltyDoc
     * @param  int  $cfgZone
     *              {
     *               \Config::get('scqms.CFG_ZONE.FQ')
     *               \Config::get('scqms.CFG_ZONE.QB')
     *               \Config::get('scqms.CFG_ZONE.OL')
     *              }
     * @return \Illuminate\Http\Response
     */
    public function show($idQltyDoc, $cfgZone)
    {
        $oQualityDocument = SQDocument::find($idQltyDoc);
        
        $lData = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_quality_documents as qqd')
                            ->join('erp_signatures as esa', 'qqd.signature_argox_id', '=', 'esa.id_signature')
                            ->join('erp_signatures as esc', 'qqd.signature_coding_id', '=', 'esc.id_signature')
                            ->join('erp_signatures as esm', 'qqd.signature_mb_id', '=', 'esm.id_signature')
                            ->join('wms_lots as wl', 'qqd.lot_id', '=', 'wl.id_lot')
                            ->join('mms_production_orders as mpof', 'qqd.father_po_id', '=', 'mpof.id_order')
                            ->join('mms_production_orders as mpos', 'qqd.son_po_id', '=', 'mpos.id_order')
                            ->join('erpu_items as ei', 'qqd.item_id', '=', 'ei.id_item')
                            ->join('erpu_units as eu', 'qqd.unit_id', '=', 'eu.id_unit')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uesa', 'esa.signed_by_id', '=', 'uesa.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uesc', 'esc.signed_by_id', '=', 'uesc.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uesm', 'esm.signed_by_id', '=', 'uesm.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as sq', 'qqd.sup_quality_id', '=', 'sq.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as sp', 'qqd.sup_process_id', '=', 'sp.id')
                            ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as supp', 'qqd.sup_production_id', '=', 'supp.id')
                            ->select(
                                    'qqd.id_document',
                                    'qqd.dt_document',
                                    'qqd.body_id',
                                    'qqd.title',
                                    'esa.signed AS b_argox',
                                    'esc.signed AS b_coding',
                                    'esm.signed AS b_mb',
                                    'esa.created_at AS creation_argox',
                                    'esc.created_at AS creation_coding',
                                    'esm.created_at AS creation_mb',
                                    'uesa.username AS usr_argox',
                                    'uesc.username AS usr_coding',
                                    'uesm.username AS usr_mb',
                                    'wl.lot',
                                    'wl.dt_expiry',
                                    'mpof.folio AS father_folio',
                                    'mpof.identifier AS father_idr',
                                    'mpof.date AS father_date',
                                    'mpos.folio AS son_folio',
                                    'mpos.identifier AS son_idr',
                                    'mpos.date AS son_date',
                                    'ei.name AS item',
                                    'ei.code AS item_code',
                                    'eu.name AS unit',
                                    'eu.code AS unit_code',
                                    'sq.username AS sup_quality',
                                    'sq.id AS sup_quality_id',
                                    'sp.username AS sup_process',
                                    'sp.id AS sup_process_id',
                                    'supp.username AS sup_production',
                                    'supp.id AS sup_production_id'
                            );
        
        $lData = $lData->where('qqd.id_document', $idQltyDoc)
                        ->first();

        $lUsers = User::where('is_deleted', false)
                            ->select('username', 'id')
                            ->orderBy('username', 'ASC')
                            ->whereNotIn('username', ['admin', 'adminswap', 
                                                        'saporis', 'contraloria',
                                                        'swap',
                                                        'consultas', 'manager'])
                            ->get();
        $oFatherPo = SProductionOrder::find($oQualityDocument->father_po_id);
        $oSonPo = SProductionOrder::find($oQualityDocument->son_po_id);

        $aResult = SQDocsCore::getConfigurations($oFatherPo, $oSonPo);

        /**
         * Creation of MongoDB Document
         */
        if (strlen($oQualityDocument->body_id) > 0) {
            $oMongoDocument = SQMongoDoc::find($oQualityDocument->body_id);
        }
        else {
            $oMongoDocument = null;
        }

        return view('qms.docs.index')
                    ->with('oQDocument', $oQualityDocument)
                    ->with('oMongoDocument', $oMongoDocument)
                    ->with('cfgZone', $cfgZone)
                    ->with('lSections', $aResult[0])
                    ->with('lConfigurations', $aResult[1])
                    ->with('lUsers', $lUsers)
                    ->with('aData', $lData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
