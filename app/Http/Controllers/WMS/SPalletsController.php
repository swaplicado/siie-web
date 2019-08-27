<?php

namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;
use App\Database\Config;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Laracasts\Flash\Flash;
use PDF;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\SUtils\SGuiUtils;
use App\ERP\SBranch;
use App\SUtils\SProcess;
use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\WMS\SLocation;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\SBarcode\SBarcode;
use App\WMS\SComponetBarcode;

class SPalletsController extends Controller
{

  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;

  public function __construct()
  {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CONFIG_WHS_STD'), \Config::get('scsys.MODULES.WMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $iId = 0, $sItem = '')
    {
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
      $sFilterDate = $request->filterDate == null ? SGuiUtils::getRangeFromDate(Carbon::now(), 40) : $request->filterDate;
      // $Pallets = SPallet::Search($request->pallet, $this->iFilter)->orderBy('id_pallet', 'ASC')->get();

      $sSelect = '
                    id_pallet,
                    wp.is_deleted,
                    ei.code AS item_code,
                    ei.name AS item,
                    eu.code AS unit_code,
                    eu.name AS unit,
                    wp.created_by_id,
                    wp.updated_by_id,
                    wp.created_at,
                    wp.updated_at
                  ';

      $Pallets = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('wms_pallets as wp')
                      ->join('erpu_items as ei', 'wp.item_id', '=', 'ei.id_item')
                      ->join('erpu_units as eu', 'wp.unit_id', '=', 'eu.id_unit')
                      ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'wp.created_by_id', '=', 'uc.id')
                      ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'wp.updated_by_id', '=', 'uu.id');

      switch ($this->iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
            $Pallets = $Pallets->where('wp.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
          break;

        case \Config::get('scsys.FILTER.DELETED'):
            $Pallets = $Pallets->where('wp.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
          break;

        default:
      }

      $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);

      $Pallets = $Pallets->select(\DB::raw($sSelect))
                    ->where(function ($query) use ($request) {
                        $query->where('id_pallet', 'LIKE', "%".$request->name."%")
                              ->orWhere('ei.code', 'LIKE', "%".$request->name."%")
                              ->orWhere('ei.name', 'LIKE', "%".$request->name."%");
                    })
                    ->whereBetween('wp.created_at', [$aDates[0]->toDateString(), $aDates[1]->toDateString()])
                    ->orderBy('id_pallet', 'DESC')
                    ->get();

      return view('wms.pallets.index')
              ->with('sTitle', 'Tarimas')
              ->with('pallets', $Pallets)
              ->with('iId', $iId)
              ->with('sItem', str_replace("/", "_", $sItem))
              ->with('actualUserPermission', $this->oCurrentUserPermission)
              ->with('sFilterDate', $sFilterDate)
              ->with('iFilter', $this->iFilter);
    }

    // public function getFromServer()
    // {
    //    $lPallets = SPallet::select(['id_pallet']);

    //    return Datatables::of($lPallets)->make(true);
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $items = SItem::select('id_item', \DB::raw("CONCAT(erpu_items.code, '_', erpu_items.name, '-', eu.code) as item"))
                        ->join('erpu_units AS eu', 'unit_id', '=', 'eu.id_unit')
                        ->where('erpu_items.is_deleted', false)
                        ->lists('item','id_item');

        return view('wms.pallets.createEdit')
                      ->with('items', $items);
    }

    /**
     * Store a newpy created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $listId="";
        $arrayPallets=array();

        for($i=0; $i < $request->quantity; $i++) {
          $pallets = new SPallet($request->all());
          $iLastId =  \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('wms_pallets')
                            ->select(\DB::raw("(select max(id_pallet) from wms_pallets) AS id_max"))
                            ->take(1)
                            ->get();

          $pallets->pallet = ($iLastId[0]->id_max) + 1;
          $pallets->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
          $pallets->unit_id = $pallets->item->unit_id;
          $pallets->updated_by_id = \Auth::user()->id;
          $pallets->created_by_id = \Auth::user()->id;

          if($listId == "") {
            $listId = $pallets->pallet;
          }
          else {
            $listId = $listId.",".$pallets->pallet;
          }
          
          $pallets->save();
        }

        Flash::success('Se han creado las siguientes tarimas '.$listId)->important();

        $print = 1;

        return redirect()->route('wms.pallets.index',
                            [$listId,  str_replace("/", "_", $pallets->item->name).'-'.$pallets->unit->code]);
    }

    public function print($sId){
      $arrayPallets=array();
      $arrayBarcodes=array();
      $arrayIds= explode(",",$sId);

      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Tarima')
                                      ->get()->lists('digits','id_component');
      for($j=0;$j<sizeof($arrayIds);$j++){
        $arrayPallets[$j] = SPallet::find($arrayIds[$j]);
        $arrayPallets[$j]->item;
        $arrayPallets[$j]->unit;
        $arrayBarcodes[$j] = SBarcode::generatePalletBarcode($dataBarcode,$arrayPallets[$j]);
      }

      view()->share('barcode',$arrayBarcodes);
      view()->share('data',$arrayPallets);
      $pdf = PDF::loadView('massive_pdf');
      $paper_size = array(0,0,287,431);
      $pdf->setPaper($paper_size,'portrait');
      return $pdf->stream();
      // Flash::success('Se han creado las siguientes tarimas '.$listId)->important();
      //
      // return redirect()->route('wms.pallets.index',
      //                     [$listId, $pallets->item->name.'-'.$pallets->unit->code]);

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
        $pallets = SPallet::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $pallets);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($pallets);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $pallets->item;
        $pallets->unit;
        $pallets->userCreation;
        $pallets->userUpdate;

        $items = SItem::select('id_item', \DB::raw("CONCAT(erpu_items.code, '_', erpu_items.name, '-', eu.code) as item"))
                        ->join('erpu_units AS eu', 'unit_id', '=', 'eu.id_unit')
                        ->where('erpu_items.is_deleted', false)
                        ->lists('item','id_item');

        return view('wms.pallets.createEdit')
                      ->with('pallets', $pallets)
                      ->with('items', $items);
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
      $pallet = SPallet::find($id);
      $pallet->fill($request->all());
      $pallets->unit_id = $pallets->item->unit_id;
      $pallet->updated_by_id = \Auth::user()->id;
      $pallet->created_by_id = \Auth::user()->id;

      $errors = $pallet->save();
      if (sizeof($errors) > 0)
      {
         return redirect()->back()->withInput($request->input())->withErrors($errors);
      }

      Flash::success(trans('messages.REG_EDITED'))->important();

      return redirect()->route('wms.pallets.index');
    }

    public function copy(Request $request, $id)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $pallet = SPallet::find($id);

        $palletCopy = clone $pallet;
        $palletCopy->id_pallet = 0;
        $items = SItem::orderBy('name', 'ASC')->lists('name', 'id_item');
        $units = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

        return view('wms.pallets.createEdit')
                      ->with('pallets',$pallets)
                      ->with('items', $items)
                      ->with('units', $units)
                      ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $pallet = SPallet::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $pallet);

        $pallet->fill($request->all());
        $pallet->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $pallet->updated_by_id = \Auth::user()->id;

        $errors = $pallet->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('wms.pallets.index')->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('wms.pallets.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        session('utils')->validateDestroy($this->oCurrentUserPermission->privilege_id);

        $pallet = SPallet::find($id);
        // $pallet->fill($request->all());
        $pallet->is_deleted = \Config::get('scsys.STATUS.DEL');
        $pallet->updated_by_id = \Auth::user()->id;

        $errors = $pallet->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('wms.pallets.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();
        return redirect()->route('wms.pallets.index');
    }

    public function barcode($id){
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Tarima')
                                      ->get()->lists('digits','id_component');

      $data = SPallet::find($id);
      $data->item;
      $data->unit;

      $barcode = SBarcode::generatePalletBarcode($dataBarcode,$data);

      view()->share('barcode',$barcode);
      view()->share('data',$data);
      view()->share('contador',2);
      $pdf = PDF::loadView('pallet_pdf');
      $paper_size = array(0,0,287,431);
      $pdf->setPaper($paper_size,'portrait');
      return $pdf->stream();
      //   view()->share('barcode',$barcode);
      //   view()->share('data',$data);
      //   $pdf = PDF::loadView('vista_pdf_aux');
      //   $paper_size = array(0,0,215,130);
      //   $pdf->setPaper($paper_size);
      //   return $pdf->stream();
    }
}
