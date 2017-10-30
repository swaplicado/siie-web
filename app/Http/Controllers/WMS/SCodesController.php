<?php

namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\SUtils\SProcess;
use App\ERP\SItem;
use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\SBarcode\SBarcode;
use App\WMS\SComponetBarcode;
use PDF;

class SCodesController extends Controller
{

    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.STK_MOVS'), \Config::get('scsys.MODULES.WMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function start()
    {
        return view('wms.codes.start');
    }

    public function findProductName(Request $request){
      if($request->id==0){
        $data = SPallet::select('id_pallet','pallet')
                        ->get();
        return response()->json($data);
      }
      if($request->id==1){
        $data = SItem::select('wms_lots.id_lot','erpu_items.name')
                      ->join('wms_lots','wms_lots.item_id','=','erpu_items.id_item')
                      ->get();
        //$data=SItem::select('name', 'id_item')->take(100)->get();
        //$data=SItem::orderBy('name','ASC')->lists('name','id_item');
        return response()->json($data);
      }
    }

    public function prodfunct(){


      return view('productlist');
    }

    public function generate(Request $request){
      if($request->etiqueta==0){
        $dataBarcode = SComponetBarcode::select('digits','id_component')
                                        ->where('type_bacode','Tarima')
                                        ->get()->lists('digits','id_component');

        $dataLot = SPallet::find($request->productos);
        $dataLot->item;
        $dataLot->unit;

        $barcode = SBarcode::generatePalletBarcode($dataBarcode,$dataLot);

        view()->share('barcode',$barcode);
        view()->share('dataLot',$dataLot);
        $pdf = PDF::loadView('vista_pdf_1');
        return $pdf->download('etiqueta.pdf');
      }
      if($request->etiqueta==1){
        $dataBarcode = SComponetBarcode::select('digits','id_component')
                                        ->where('type_bacode','Item')
                                        ->get()->lists('digits','id_component');

        $dataLot = SwmsLot::find($request->productos);
        $dataLot->item;
        $dataLot->unit;

        $barcode = SBarcode::generateItemBarcode($dataBarcode,$dataLot);

        view()->share('barcode',$barcode);
        view()->share('dataLot',$dataLot);
        $pdf = PDF::loadView('vista_pdf');
        return $pdf->download('etiqueta.pdf');
      }


    }

}
