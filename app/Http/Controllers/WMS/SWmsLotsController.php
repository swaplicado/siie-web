<?php

namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Database\Config;

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
         $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CONFIG_WHS_STD'), \Config::get('scsys.MODULES.WMS'));

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
      // $lLots = SWmsLot::Search($request->name, $this->iFilter)->orderBy('id_lot', 'ASC')->get();

      $sSelect = '
                    id_lot,
                    lot,
                    dt_expiry,
                    wl.is_deleted,
                    ei.code AS item_code,
                    ei.name AS item,
                    eu.code AS unit_code,
                    eu.code AS unit,
                    wl.created_by_id,
                    wl.updated_by_id,
                    wl.created_at,
                    wl.updated_at
                  ';

      $lLots = \DB::connection(session('db_configuration')->getConnCompany())
                   ->table('wms_lots as wl')
                   ->join('erpu_items as ei', 'wl.item_id', '=', 'ei.id_item')
                   ->join('erpu_units as eu', 'wl.unit_id', '=', 'eu.id_unit')
                   ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'wl.created_by_id', '=', 'uc.id')
                   ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'wl.updated_by_id', '=', 'uu.id');

     switch ($this->iFilter) {
       case \Config::get('scsys.FILTER.ACTIVES'):
           $lLots = $lLots->where('wl.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
         break;

       case \Config::get('scsys.FILTER.DELETED'):
           $lLots = $lLots->where('wl.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
         break;

       default:
     }

     $lLots = $lLots->select(\DB::raw($sSelect))
                    ->where(function ($query) use ($request) {
                        $query->where('lot', 'LIKE', "%".$request->name."%")
                              ->orWhere('dt_expiry', 'LIKE', "%".$request->name."%")
                              ->orWhere('ei.code', 'LIKE', "%".$request->name."%")
                              ->orWhere('ei.name', 'LIKE', "%".$request->name."%");
                    })
                    ->orderBy('created_at', 'DESC')
                    ->paginate(50);

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

        $items = SItem::select('id_item', \DB::raw("CONCAT(erpu_items.code, '_', erpu_items.name, '-', eu.code) as item"))
                        ->join('erpu_units AS eu', 'unit_id', '=', 'eu.id_unit')
                        ->where('erpu_items.is_deleted', false)
                        ->lists('item','id_item');

        return view('wms.lots.createEdit')
                      ->with('items', $items);
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
        $lot->unit_id = $lot->item->unit_id;
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

        $items = SItem::select('id_item', \DB::raw("CONCAT(erpu_items.code, '_', erpu_items.name, '-', eu.code) as item"))
                        ->join('erpu_units AS eu', 'unit_id', '=', 'eu.id_unit')
                        ->where('erpu_items.is_deleted', false)
                        ->lists('item','id_item');


        return view('wms.lots.createEdit')
                      ->with('lots', $lot)
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
        $lot = SWmsLot::find($id);
        $lot->fill($request->all());
        $lot->unit_id = $lot->item->unit_id;
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
                       ->with('lots', [])
                       ->with('items', $items)
                       ->with('units', $units)
                       ->with('bIsCopy', true);
     }

     public function activate(Request $request, $id)
     {
         $lot = SWmsLot::find($id);

         session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $lot);

         $lot->fill($request->all());
         $lot->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
         $lot->updated_by_id = \Auth::user()->id;

         $errors = $lot->save();
         if (sizeof($errors) > 0)
         {
            return redirect()->back()->withErrors($errors);
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
            return redirect()->back()->withErrors($errors);
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
       $paper_size = array(0,0,215,130);
       $pdf->setPaper($paper_size);

       return $pdf->stream();
     }
}
