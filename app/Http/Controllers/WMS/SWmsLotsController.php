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
use App\ERP\SItem;
use App\ERP\SUnit;
use App\SBarcode\SBarcode;
use App\WMS\SComponetBarcode;
use PDF;

class SWmsLotsController extends Controller
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
      $lLots = SWmsLot::Search($request->name, $this->iFilter)->orderBy('id_lot', 'ASC')->get();

      $lLots->each(function($lLots) {
        $lLots->item;
        $lLots->unit;
        $lLots->userCreation;
        $lLots->userUpdate;
      });

      return view('wms.lots.index')
          ->with('lots', $lLots)
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

        return view('wms.lots.createEdit')
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
        $lot = new SWmsLot($request->all());

        $lot->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $lot->updated_by_id = \Auth::user()->id;
        $lot->created_by_id = \Auth::user()->id;

        $lot->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('wms.lots.index');
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
        $lot = SWmsLot::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $lot);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($lot);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $lot->item;
        $lot->unit;
        $lot->userCreation;
        $lot->userUpdate;

        $items = SItem::orderBy('name', 'ASC')
                        ->where('is_deleted', false)
                        ->lists('name', 'id_item');
        $units = SUnit::orderBy('name', 'ASC')
                          ->where('is_deleted', false)
                          ->lists('name', 'id_unit');


        return view('wms.lots.createEdit')
                      ->with('lots', $lot)
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
        $lot = SWmsLot::find($id);
        $lot->fill($request->all());
        $lot->updated_by_id = \Auth::user()->id;
        $lot->created_by_id = \Auth::user()->id;

        $errors = $lot->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('wms.lots.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function copy(Request $request, $id)
     {
         if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
         {
           return redirect()->route('notauthorized');
         }

         $lot = SWmsLot::find($id);

         $lotCopy = clone $lot;
         $lotCopy->id_lot = 0;
         $items = SItem::orderBy('name', 'ASC')->lists('name', 'id_item');
         $units = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

         return view('wms.lots.createEdit')
                       ->with('lots',$lots)
                       ->with('items', $items)
                       ->with('units', $units)
                       ->with('bIsCopy', true);
     }

     public function activate(Request $request, $id)
     {
         $lot = SWmsLot::find($id);

         session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $folio);

         $lot->fill($request->all());
         $lot->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
         $lot->updated_by_id = \Auth::user()->id;

         $errors = $lot->save();
         if (sizeof($errors) > 0)
         {
            return redirect()->back()->withErrors($error);
         }

         Flash::success(trans('messages.REG_ACTIVATED'))->important();

         return redirect()->route('wms.lots.index');
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

         $lot = SWmsLot::find($id);
         $lot->fill($request->all());
         $lot->is_deleted = \Config::get('scsys.STATUS.DEL');
         $lot->updated_by_id = \Auth::user()->id;

         $errors = $lot->save();
         if (sizeof($errors) > 0)
         {
            return redirect()->back()->withErrors($error);
         }
         #$user->delete();

         Flash::success(trans('messages.REG_DELETED'))->important();

         return redirect()->route('wms.lots.index');
     }

     public function barcode($id){
       $dataBarcode = SComponetBarcode::select('digits','id_component')
                                       ->where('type_barcode','Item')
                                       ->get()->lists('digits','id_component');

       $data = SwmsLot::find($id);
       $data->item;
       $data->unit;

       $barcode = SBarcode::generateItemBarcode($dataBarcode,$data);

       view()->share('barcode',$barcode);
       view()->share('data',$data);
       $pdf = PDF::loadView('vista_pdf');
       $paper_size = array(0,0,215,141);
       $pdf->setPaper($paper_size);

       return $pdf->stream();
     }
}
