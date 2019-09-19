<?php

namespace App\Http\Controllers\QMS;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Database\Config;
use App\SUtils\SConnectionUtils;
use App\SUtils\SProcess;
use App\QMS\SQDocument;
use App\QMS\SQMongoDoc;
use App\MMS\SProductionOrder;
use App\User;
use App\QMS\core\SQDocsCore;

class SQDocumentsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QUALITY'), \Config::get('scsys.MODULES.QMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $fathero
     * @param [type] $sono
     * @param [type] $lot
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $fathero, $sono, $lot)
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
            $oDoc->created_by_id = \Auth::user()->id;
            $oDoc->updated_by_id = \Auth::user()->id;

            $oDoc->save();
        }
        else {
            $bFlag = false;
            if ($oDoc->son_po_id == 1 && $sono > 1) {
                $oDoc = clone $oDoc;

                $oDoc->id_document = 0;
                $oDoc->son_po_id = $oSonPo->id_order;
                $oDoc->item_id = $oSonPo->item_id;
                $oDoc->unit_id = $oSonPo->unit_id;

                $bFlag = true;
            }
            if ($oDoc->lot_id == 1 && $lot > 1) {
                $oDoc->lot_id = $lot;
                $bFlag = true;
            }

            if ($bFlag) {
                $oDoc->save();
            }

            if (strlen($oDoc->body_id) > 0) {
                $oMongoDocument = SQMongoDoc::find($oDoc->body_id);
            }

        }

        $lData = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('qms_quality_documents as qqd')
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

        $aResult = SQDocsCore::getConfigurations($oFatherPo, $oSonPo);

        return view('qms.docs.index')
                    ->with('oQDocument', $oDoc)
                    ->with('oMongoDocument', $oMongoDocument)
                    ->with('lSections', $aResult[0])
                    ->with('lConfigurations', $aResult[1])
                    ->with('lUsers', $lUsers)
                    ->with('aData', $lData);
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

        $oDoc = SQDocument::find($vDoc->id_document);

        $oDoc->sup_quality_id = $vDoc->sup_quality_id;
        $oDoc->sup_process_id = $vDoc->sup_process_id;
        $oDoc->sup_production_id = $vDoc->sup_production_id;
        $oDoc->updated_by_id = \Auth::user()->id;

        if (strlen($oDoc->body_id) > 0) {
            $oMongoDoc = SQMongoDoc::find($oDoc->body_id);

            $oMongoDoc->qlty_document = $oDoc->toArray();
            $oMongoDoc->configurations = $lConfigs;
            $oMongoDoc->results = $lResults;

            $oMongoDoc->save();
        }
        else {
            $oMongoDoc = new SQMongoDoc();

            $oMongoDoc->qlty_document = $oDoc->toArray();
            $oMongoDoc->configurations = $lConfigs;
            $oMongoDoc->results = $lResults;

            $oMongoDoc->save();

            $oDoc->body_id = $oMongoDoc->id;
        }

        $oDoc->save();

        return json_encode($oDoc);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
