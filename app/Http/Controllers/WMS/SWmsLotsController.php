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

class SWmsLotsController extends Controller
{

    private $oCurrentUserPermission;
    private $iFilter;
    private $sClassNav;

    public function __construct()
    {
         $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CENTRAL_CONFIG'), \Config::get('scsys.MODULES.ERP'));

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
      $lLots = SWmsLot::Search($request->lot, $this->iFilter)->orderBy('id_lot', 'ASC')->paginate(10);

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

          $items = SItem::orderBy('name', 'ASC')->lists('name', 'id_item');
          $units = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');

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
        $lots = SWmsLot::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $lots);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($lots);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $lots->item;
        $lots->unit;
        $lots->userCreation;
        $lots->userUpdate;

        $items = SItem::orderBy('name', 'ASC')->lists('name', 'id_item');
        $units = SUnit::orderBy('name', 'ASC')->lists('name', 'id_unit');


        return view('wms.lots.createEdit')
                      ->with('lots',$lots)
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
         return redirect()->route('wms.lots.index')->withErrors($errors);
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

         session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $lot);

         $lot->fill($request->all());
         $lot->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
         $lot->updated_by_id = \Auth::user()->id;

         $errors = $lot->save();
         if (sizeof($errors) > 0)
         {
            return redirect()->route('wms.lots.index')->withErrors($errors);
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
            return redirect()->route('wms.lots.index')->withErrors($errors);
         }
         #$user->delete();

         Flash::error(trans('messages.REG_DELETED'))->important();
         return redirect()->route('wms.lots.index');
     }
}
