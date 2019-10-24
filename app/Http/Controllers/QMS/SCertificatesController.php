<?php

namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SConnectionUtils;
use App\SUtils\SProcess;

use App\QMS\SQDocument;
use App\QMS\SQMongoDoc;
use App\QMS\SCertConfig;
use App\QMS\core\SQDocsCore;

use App\WMS\SWmsLot;
use App\ERP\SItemLinkType;
use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\ERP\SItemFamily;
use App\ERP\SItemGroup;
use App\ERP\SItemGender;
use App\ERP\SItem;

class SCertificatesController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QMS_CERTIFICATES'), \Config::get('scsys.MODULES.QMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($idDoc)
    {
        SConnectionUtils::reconnectCompany();

        $oDoc = SQDocument::find($idDoc);

        $aItemData = SQDocsCore::getItemData($oDoc->item_id);

        $lConfigs = SCertConfig::where('is_deleted', false)
                            ->where(function ($query) use ($aItemData) {

                                $query->where(function ($query) use ($aItemData) {
                                            $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.CLASS'))
                                            ->where('item_link_id', '=', $aItemData->item_class_id);
                                        })
                                    ->orWhere(function ($query) use ($aItemData) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.TYPE'))
                                            ->where('item_link_id', '=', $aItemData->item_type_id);
                                    })
                                    ->orWhere(function ($query) use ($aItemData) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.FAMILY'))
                                            ->where('item_link_id', '=', $aItemData->item_family_id);
                                    })
                                    ->orWhere(function ($query) use ($aItemData) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.GROUP'))
                                            ->where('item_link_id', '=', $aItemData->item_group_id);
                                    })
                                    ->orWhere(function ($query) use ($aItemData) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.GENDER'))
                                            ->where('item_link_id', '=', $aItemData->id_item_gender);
                                    })
                                    ->orWhere(function ($query) use ($aItemData) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.ITEM'))
                                            ->where('item_link_id', '=', $aItemData->id_item);
                                    })
                                    ->orWhere(function ($query) {
                                        $query->where('item_link_type_id', '=', \Config::get('scsiie.ITEM_LINK.ALL'))
                                            ->where('item_link_id', '=', 1);
                                    });

                                });

        $lConfigsAnalysis = $lConfigs->select('analysis_id')->get()->toArray();

        $oMongoDoc = SQMongoDoc::where('lot_id', $oDoc->lot_id)->first();

        $lResults = array();
        foreach ($oMongoDoc->results as $result) {
            if ($result['analysis_id'] == 0) {
                continue;
            }

            if (in_array($result['analysis_id'], $lConfigsAnalysis)) {
                $lResults[] = $result;
            }
        }

        $oLot = SWmsLot::find($oDoc->lot_id);

        $view = view('qms.certificates.certificatepdf', [
                'oBranch' => session('branch'),
                'oLot' => $oLot,
                'sDate' => '2019-10-25',
                'lFQResults' => $lFQResults,
                'lOLResults' => $lOLResults,
                'lMBResults' => $lMBResults,
                'sSupervisor' => "Supervisor",
                'sManager' => "Gerente"
                ])
                ->render();

        // set ukuran kertas dan orientasi
        $pdf = \PDF::loadHTML($view)->setPaper('letter', 'potrait')->setWarnings(false);
        // cetak
        return $pdf->stream();
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function config(Request $request)
    {
        $lTypes = SItemLinkType::where('is_deleted', false)
                                ->lists('name', 'id_item_link_type');

        $lItems = SItem::where('is_deleted', false)->selectRaw('CONCAT(code, "-", name) AS _item')->orderBy('code', 'ASC')->lists('_item', 'id_item');
        $lGenders = SItemGender::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_gender');
        $lGroups = SItemGroup::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_group');
        $lFamilies = SItemFamily::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_family');
        $lItemTypes = SItemType::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_type');
        $lItemClass = SItemClass::where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_class');

        return view('qms.certificates.indexc')
                    ->with('lItemClass', $lItemClass)
                    ->with('lItemTypes', $lItemTypes)
                    ->with('lFamilies', $lFamilies)
                    ->with('lGroups', $lGroups)
                    ->with('lGenders', $lGenders)
                    ->with('lItems', $lItems)
                    ->with('lTypes', $lTypes);
    }
}
