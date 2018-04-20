<?php

namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\SUtils\SProcess;
use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\WMS\SLocation;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\SBarcode\SBarcode;
use App\WMS\SComponetBarcode;
use PDF;

class SPalletsController extends Controller
{

  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;

  public function __construct()
  {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CENTRAL_CONFIG'), \Config::get('scsys.MODULES.WMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
      $Pallets = SPallet::Search($request->pallet, $this->iFilter)->orderBy('id_pallet', 'ASC')->get();

      $Pallets->each(function($Pallets) {
        $Pallets->item;
        $Pallets->unit;
        $Pallets->userCreation;
        $Pallets->userUpdate;
      });

      return view('wms.pallets.index')
          ->with('pallets', $Pallets)
          ->with('actualUserPermission', $this->oCurrentUserPermission)
          ->with('iFilter', $this->iFilter);
    }

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

        $items = SItem::select('id_item', \DB::raw("CONCAT(code, ' - ', name)as item"))
                        ->where('is_deleted', false)
                        ->lists('item','id_item');
        $units = SUnit::select('id_unit', \DB::raw("CONCAT(code,' - ', name)as unit"))
                        ->where('is_deleted', false)
                        ->lists('unit','id_unit');

        return view('wms.pallets.createEdit')
                      ->with('items', $items)
                      ->with('units', $units);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pallets = new SPallet($request->all());

        $pallets->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $pallets->updated_by_id = \Auth::user()->id;
        $pallets->created_by_id = \Auth::user()->id;

        $pallets->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('wms.pallets.index');
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

        $items = SItem::orderBy('name', 'ASC')->lists('name', 'id_item');
        $units = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

        return view('wms.pallets.createEdit')
                      ->with('pallets',$pallets)
                      ->with('items', $items)
                      ->with('units', $units);
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
        $pallet->fill($request->all());
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
      $pdf = PDF::loadView('vista_pdf_1');
      $paper_size = array(0,0,431,287);
      $pdf->setPaper($paper_size);
      return $pdf->download('etiqueta.pdf');
    }
}
