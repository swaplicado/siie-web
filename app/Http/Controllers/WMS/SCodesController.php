<?php

namespace App\Http\Controllers\WMS;

use App\ERP\SBranch;
use App\ERP\SItem;
use App\Http\Controllers\Controller;
use App\SBarcode\SBarcode;
use App\SCore\SStockManagment;
use App\SUtils\SProcess;
use App\WMS\SComponetBarcode;
use App\WMS\SLocation;
use App\WMS\SPallet;
use App\WMS\SWarehouse;
use App\WMS\SWmsLot;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
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

    public function findProductName(Request $request)
    {
        if ($request->id == 0) {
            $data = SPallet::select('id_pallet', 'pallet')
                ->where('is_deleted', 0)
                ->get();
            return response()->json($data);
        }
        if ($request->id == 1) {
            $data = SItem::select('wms_lots.id_lot', 'erpu_items.name')
                ->join('wms_lots', 'wms_lots.item_id', '=', 'erpu_items.id_item')
                ->where('wms_lots.is_deleted', 0)
                ->get();
            //$data=SItem::select('name', 'id_item')->take(100)->get();
            //$data=SItem::orderBy('name','ASC')->lists('name','id_item');
            return response()->json($data);
        }
        if ($request->id == 2) {
            $data = SLocation::select('id_whs_location', 'code')
                ->where('is_deleted', 0)
                ->where('whs_id', session('whs')->id_whs)
                ->get();

            return response()->json($data);
        }
    }

    public function prodfunct()
    {

        return view('productlist');
    }

    public function generate(Request $request)
    {
        if ($request->etiqueta == 0) {
            $dataBarcode = SComponetBarcode::select('digits', 'id_component')
                ->where('type_barcode', 'Tarima')
                ->get()->lists('digits', 'id_component');

            $data = SPallet::find($request->productos);
            $data->item;
            $data->unit;

            $barcode = SBarcode::generatePalletBarcode($dataBarcode, $data);

            view()->share('barcode', $barcode);
            view()->share('data', $data);
            $pdf = PDF::loadView('vista_pdf_aux');
            $paper_size = array(0, 0, 287, 431);
            $pdf->setPaper($paper_size, 'portrait');
            return $pdf->stream();
        }
        if ($request->etiqueta == 1) {
            $dataBarcode = SComponetBarcode::select('digits', 'id_component')
                ->where('type_barcode', 'Item')
                ->get()->lists('digits', 'id_component');

            $data = SwmsLot::find($request->productos);
            $data->item;
            $data->unit;

            $barcode = SBarcode::generateItemBarcode($dataBarcode, $data);

            view()->share('barcode', $barcode);
            view()->share('data', $data);
            $pdf = PDF::loadView('vista_pdf');
            $paper_size = array(0, 0, 215, 130);
            $pdf->setPaper($paper_size);
            return $pdf->stream();
        }

        if ($request->etiqueta == 2) {
            $dataBarcode = SComponetBarcode::select('digits', 'id_component')
                ->where('type_barcode', 'Ubicacion')
                ->get()->lists('digits', 'id_component');
            $data = SLocation::find($request->productos);
            $data->warehouse;

            $barcode = SBarcode::generateLocationBarcode($dataBarcode, $data);

            view()->share('barcode', $barcode);
            view()->share('data', $data);
            $pdf = PDF::loadView('vista_pdf_2');
            $paper_size = array(0, 0, 431, 287);
            $pdf->setPaper($paper_size);
            return $pdf->stream();
        }

    }

    public function decode(Request $request)
    {
        $data = SBarcode::decodeBarcode($request->codigo);
        if ($data == null || $data->id_item != null) {
            Flash::error('No existe el producto');
            return redirect()->route('wms.codes.consult');
        }
        $data->item;
        $data->unit;
        $type = substr($request->codigo, 0, 1);

        if ($type == 1) {
            $string = ' ';
            $prms = [];
            // $prms[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 
            // $prms[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 
            $prms[\Config::get('scwms.STOCK_PARAMS.LOT')] = $data->id_lot;
            // $prms[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 
            // $prms[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 
            $prms[\Config::get('scwms.STOCK_PARAMS.WHS')] = session('whs')->id_whs;
            $prms[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = session('branch')->id_branch;
            $prms[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');
            $prms[\Config::get('scwms.STOCK_PARAMS.DATE')] = session('work_date')->toDateString();
            // $prms[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = 

            $stock = SStockManagment::getStock($prms);
            $lotStock = null;
        }

        if ($type == 2) {
            $prms = [];
            // $prms[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 
            // $prms[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 
            // $prms[\Config::get('scwms.STOCK_PARAMS.LOT')] = 
            $prms[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $data->id_pallet;
            // $prms[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 
            $prms[\Config::get('scwms.STOCK_PARAMS.WHS')] = session('whs')->id_whs;
            $prms[\Config::get('scwms.STOCK_PARAMS.BRANCH')] = session('branch')->id_branch;
            $prms[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');
            $prms[\Config::get('scwms.STOCK_PARAMS.DATE')] = session('work_date')->toDateString();
            // $prms[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = 

            $stock = SStockManagment::getStock($prms);
            $lotStock = SStockManagment::getLotsOfPallet($data->id_pallet, session('whs')->id_whs);
        }

        return view('wms.codes.info')
            ->with('info', $data)
            ->with('type', $type)
            ->with('stock', $stock)
            ->with('lotStock', $lotStock);
    }

    public function decodeWith(Request $request)
    {
        $data = SBarcode::decodeBarcode($request->codigo);
        if ($data == null) {
            Flash::error('No existe el producto');
            return redirect()->route('wms.codes.consult');
        }
        $data->item;
        $data->unit;
        $type = substr($request->codigo, 0, 1);

        if ($type == 1) {

            $stock = SStockManagment::getStock($data->item, $data->unit, $data->id_lot, 0, 0, $request->branch, $request->whs);

        }

        if ($type == 2) {

            $stock = SStockManagment::getStock($data->item, $data->unit, 0, $data->id_pallet, 0, $request->branch, $request->whs);
            //$lotStock = SStockManagment::getLotsOfPallet($data->id_pallet,0);

        }

        return view('wms.codes.info')
            ->with('info', $data)
            ->with('type', $type)
            ->with('stock', $stock);
        //->with('lotStock',$lotStock);

    }

    public function consultBarcode()
    {
        return view('wms.codes.consult');
    }

    public function consultwithbranch()
    {
        $branch = SBranch::orderBy('name', 'ASC')
            ->where('partner_id', session('partner')->id_partner)
            ->lists('name', 'id_branch');

        return view('wms.codes.withbranch')
            ->with('branch', $branch);
    }

    public function findWhs(Request $request)
    {
        $data = SWarehouse::select('id_whs', 'name')
            ->where('branch_id', $request->id)
            ->get();

        return response()->json($data);
    }

}
