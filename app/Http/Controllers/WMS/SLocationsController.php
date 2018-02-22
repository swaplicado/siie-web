<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\WMS\SLocRequest;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\SUtils\SProcess;
use App\SBarcode\SBarcode;
use App\WMS\SComponetBarcode;
use PDF;


class SLocationsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CONTAINERS'), \Config::get('scsys.MODULES.WMS'));

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

        $lLocations = SLocation::Search($request->name, $this->iFilter)->orderBy('name', 'ASC')->paginate(20);
        $lLocations->each(function($lLocations) {
          $lLocations->warehouse;
        });

        return view('wms.locs.index')
            ->with('locations', $lLocations)
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

        $lWarehouses = SWarehouse::orderBy('name', 'ASC')->lists('name', 'id_whs');

        return view('wms.locs.createEdit')
                      ->with('warehouses', $lWarehouses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SLocRequest $request)
    {
      $location = new SLocation($request->all());

      $location->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $location->updated_by_id = \Auth::user()->id;
      $location->created_by_id = \Auth::user()->id;

      $location->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('wms.locs.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $location = SLocation::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $location);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($location);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $lWarehouses = SWarehouse::orderBy('name', 'ASC')->lists('name', 'id_whs');

        return view('wms.locs.createEdit')
                    ->with('location', $location)
                    ->with('warehouses', $lWarehouses);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SLocRequest $request, $id)
    {
        $location = SLocation::find($id);
        $location->fill($request->all());
        $location->updated_by_id = \Auth::user()->id;

        $errors = $location->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('wms.locs.index')->withErrors($errors);
        }

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('wms.locs.index');
    }

    /**
     * Inactive the registry setting the flag is_deleted to true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function copy(Request $request, $id)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $location = SLocation::find($id);

        $locationCopy = clone $location;
        $locationCopy->id_whs_location = 0;

        $lWarehouses = SWarehouse::orderBy('name', 'ASC')->lists('name', 'id_whs');

        return view('wms.locs.createEdit')->with('location', $locationCopy)
                                        ->with('warehouses', $lWarehouses)
                                      ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $location = SLocation::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $location);

        $location->fill($request->all());
        $location->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $location->updated_by_id = \Auth::user()->id;

        $errors = $folio->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('wms.locs.index')->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('wms.locs.index');
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

        $location = SLocation::find($id);
        $location->fill($request->all());
        $location->is_deleted = \Config::get('scsys.STATUS.DEL');
        $location->updated_by_id = \Auth::user()->id;

        $errors = $location->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('wms.locs.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::error(trans('messages.REG_DELETED'))->important();
        return redirect()->route('wms.locs.index');
    }

    public function barcode($id){
      $dataBarcode = SComponetBarcode::select('digits','id_component')
                                      ->where('type_barcode','Ubicacion')
                                      ->get()->lists('digits','id_component');

      $data = SLocation::find($id);
      $data->warehouse;

      $barcode = SBarcode::generateLocationBarcode($dataBarcode,$data);

      view()->share('barcode',$barcode);
      view()->share('data',$data);
      $pdf = PDF::loadView('vista_pdf_2');
      $paper_size = array(0,0,612,750);
      $pdf->setPaper($paper_size);
      return $pdf->download('etiqueta.pdf');
    }
}
