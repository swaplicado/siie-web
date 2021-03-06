<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\ERP\SGenderRequest;
use App\Http\Requests\ERP\SItemRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SItemStatus;
use App\ERP\SItemGender;
use App\ERP\SItemGroup;
use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\SUtils\SProcess;

class SItemsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;
    private $iFilterBulk;

    public function __construct()
    {
        $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ITEM_CONFIG'), \Config::get('scsys.MODULES.ERP'));

        $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
        $this->iFilterBulk = \Config::get('scsiie.FILTER_BULK.ALL');
        $this->iFilterLot = \Config::get('scsiie.FILTER_LOT.ALL');
        $this->iFilterGender = \Config::get('scsiie.FILTER_GENDER.ALL');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $iClassId = 0)
    {
        if ($iClassId == 0)
        {
            $iClassId = (session()->has('classIdAux') ? session('classIdAux') : 1);
        }
        session(['classIdAux' => $iClassId]);
        $lGenders = SItemGender::where('item_class_id', $iClassId)
                                ->where('is_deleted', false)
                                ->orderBy('name', 'ASC')
                                ->lists('name', 'id_item_gender');
        $lGenders[\Config::get('scsiie.FILTER_GENDER.ALL')] = 'Todos';
        // array_push($lGenders,'TODOS');

        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
        $this->iFilterBulk = $request->filterBulk == null ? \Config::get('scsiie.FILTER_BULK.ALL') : $request->filterBulk;
        $this->iFilterLot = $request->filterLot == null ? \Config::get('scsiie.FILTER_LOT.ALL') : $request->filterLot;
        $this->iFilterGender = $request->filterGender == null ? \Config::get('scsiie.FILTER_GENDER.ALL') : $request->filterGender;
        $lItems = SItem::Search($request->name, $this->iFilter, $this->iFilterLot, $this->iFilterBulk, $this->iFilterGender, $iClassId)
                        ->orderBy('code', 'ASC')->paginate(50);

        $sTitle = '';

        switch ($iClassId) {
          case \Config::get('scsiie.ITEM_CLS.MATERIAL'):
            $sTitle = trans('userinterface.titles.LIST_MATERIALS');
            break;
          case \Config::get('scsiie.ITEM_CLS.PRODUCT'):
            $sTitle = trans('userinterface.titles.LIST_PRODUCTS');
            break;
          case \Config::get('scsiie.ITEM_CLS.SPENDING'):
            $sTitle = trans('userinterface.titles.LIST_SPENDING');
            break;

          default:
            # code...
            break;
        }

        return view('siie.items.index')
            ->with('items', $lItems)
            ->with('actualUserPermission', $this->oCurrentUserPermission)
            ->with('title', $sTitle)
            ->with('iFilter', $this->iFilter)
            ->with('iFilterLot', $this->iFilterLot)
            ->with('iFilterBulk', $this->iFilterBulk)
            ->with('iFilterGender', $this->iFilterGender)
            ->with('genders', $lGenders);
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

        $itemClass = session('classIdAux');

        $lGenders = SItemGender::where('item_class_id', $itemClass)->where('is_deleted', false)->orderBy('name', 'ASC')->lists('name', 'id_item_gender');
        $lUnits = SUnit::orderBy('name', 'ASC')
                        ->where('is_deleted', false)
                        ->select('id_unit', \DB::raw("CONCAT(code, '-', name) as name"))
                        ->lists('name', 'id_unit');
        $lStatus = SItemStatus::orderBy('name', 'ASC')
                        ->select('id_item_status', \DB::raw("CONCAT(code, '-', name) as name"))
                        ->where('is_deleted', false)
                        ->lists('name', 'id_item_status');

        $sTitle = '';

        switch ($itemClass) {
          case \Config::get('scsiie.ITEM_CLS.MATERIAL'):
            $sTitle = trans('userinterface.titles.CREATE_MATERIAL');
            break;
          case \Config::get('scsiie.ITEM_CLS.PRODUCTS'):
            $sTitle = trans('userinterface.titles.CREATE_PRODUCTS');
            break;
          case \Config::get('scsiie.ITEM_CLS.SPENDING'):
            $sTitle = trans('userinterface.titles.CREATE_SPENDING');
            break;

          default:
            # code...
            break;
        }

        return view('siie.items.createEdit')
                                ->with('title', $sTitle)
                                ->with('lStatus', $lStatus)
                                ->with('genders', $lGenders)
                                ->with('units', $lUnits);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SItemRequest $request)
    {
        $item = new SItem($request->all());

        $item->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $item->updated_by_id = \Auth::user()->id;
        $item->created_by_id = \Auth::user()->id;

        $item->save(); // this method doesn't implements the locks control

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('siie.items.index', session('classIdAux'));
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
        $item = SItem::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $item);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($item);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $itemClass = session('classIdAux');

        $lGenders = SItemGender::where('item_class_id', $itemClass)
                                ->where('is_deleted', false)
                                ->orderBy('name', 'ASC')
                                ->lists('name', 'id_item_gender');
        $lUnits = SUnit::orderBy('name', 'ASC')
                        ->select('id_unit', \DB::raw("CONCAT(code, '-', name) as name"))
                        ->where('is_deleted', false)
                        ->lists('name', 'id_unit');
        $lStatus = SItemStatus::orderBy('name', 'ASC')
                        ->select('id_item_status', \DB::raw("CONCAT(code, '-', name) as name"))
                        ->where('is_deleted', false)
                        ->lists('name', 'id_item_status');

        $sTitle = '';

        switch ($itemClass) {
          case \Config::get('scsiie.ITEM_CLS.MATERIAL'):
            $sTitle = trans('userinterface.titles.EDIT_MATERIAL');
            break;
          case \Config::get('scsiie.ITEM_CLS.PRODUCTS'):
            $sTitle = trans('userinterface.titles.EDIT_PRODUCT');
            break;
          case \Config::get('scsiie.ITEM_CLS.SPENDING'):
            $sTitle = trans('userinterface.titles.EDIT_SPENDING');
            break;

          default:
            # code...
            break;
        }

        return view('siie.items.createEdit')
                                ->with('title', $sTitle)
                                ->with('lStatus', $lStatus)
                                ->with('genders', $lGenders)
                                ->with('units', $lUnits)
                                ->with('item', $item);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SItemRequest $request, $id)
    {
        $item = SItem::find($id);
        $item->fill($request->all());
        $item->is_lot = $request->is_lot == true;
        $item->is_bulk = $request->is_bulk == true;
        $item->updated_by_id = \Auth::user()->id;

        $errors = $item->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('siie.items.index', session('classIdAux'));
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

        $item = SItem::find($id);

        $itemClass = $item->gender->item_class_id;

        $lGenders = SItemGender::where('item_class_id', $itemClass)
                                ->where('is_deleted', false)
                                ->orderBy('name', 'ASC')
                                ->lists('name', 'id_item_gender');
        $lUnits = SUnit::orderBy('name', 'ASC')
                        ->select('id_unit', \DB::raw("CONCAT(code, '-', name) as name"))
                        ->where('is_deleted', false)
                        ->lists('name', 'id_unit');
        $lStatus = SItemStatus::orderBy('name', 'ASC')
                        ->select('id_item_status', \DB::raw("CONCAT(code, '-', name) as name"))
                        ->where('is_deleted', false)
                        ->lists('name', 'id_item_status');

        $sTitle = '';

        switch ($itemClass) {
          case \Config::get('scsiie.ITEM_CLS.MATERIAL'):
            $sTitle = trans('userinterface.titles.EDIT_MATERIAL');
            break;
          case \Config::get('scsiie.ITEM_CLS.PRODUCTS'):
            $sTitle = trans('userinterface.titles.EDIT_PRODUCT');
            break;
          case \Config::get('scsiie.ITEM_CLS.SPENDING'):
            $sTitle = trans('userinterface.titles.EDIT_SPENDING');
            break;

          default:
            # code...
            break;
        }

        $itemCopy = clone $item;
        $itemCopy->id_item = 0;

        return view('siie.items.createEdit')->with('item', $itemCopy)
                                            ->with('title', $sTitle)
                                            ->with('lStatus', $lStatus)
                                            ->with('genders', $lGenders)
                                            ->with('units', $lUnits)
                                            ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $item = SItem::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $item);

        $item->fill($request->all());
        $item->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $item->updated_by_id = \Auth::user()->id;

        $errors = $item->save($item->toArray());
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('siie.items.index', session('classIdAux'));
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

        $item = SItem::find($id);
        $item->fill($request->all());
        $item->is_deleted = \Config::get('scsys.STATUS.DEL');
        $item->updated_by_id = \Auth::user()->id;

        $errors = $item->save($item->toArray());
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();
        return redirect()->route('siie.items.index', session('classIdAux'));
    }

    public function children(Request $request)
    {
      return SItemType::where('class_id', '=', $request->parent)->get();
    }
}
