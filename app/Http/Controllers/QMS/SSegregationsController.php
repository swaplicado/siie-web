<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;
use Carbon\Carbon;
use App\SUtils\SGuiUtils;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\WMS\Segregation\SSegregation;
use App\WMS\Segregation\SSegregationRow;
use App\WMS\Segregation\SSegregationLotRow;
use App\WMS\SLocation;
use App\QMS\SStatus;
use Laracasts\Flash\Flash;
use App\SBarcode\SBarcode;

class SSegregationsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.QUALITY'), \Config::get('scsys.MODULES.QMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the segregations resource.
     *
     * @param  Request $request
     * @param  int  $sTitle Label that will appear on the view of segregations
     * @param  int  $iSegregationType can be quality, production or shipment
     *
     */
    public function index(Request $request, $sTitle, $iSegregationType, $iQualityType,$typeView)
    {
        $sFilterDate = $request->filterDate == null ? session('work_date')->format('Y-m-d') : $request->filterDate;
        $oFilterDate = Carbon::parse($sFilterDate);
        $iYearId = session('utils')->getYearId($oFilterDate->year);
        $segregated = session('segregation')->getSegregated($iSegregationType, $iQualityType,$typeView);
        $lStatusSeg = SStatus::where('is_deleted', false)
                                ->where('id_segregation_event',3)
                                ->orwhere('id_segregation_event',4)
                                ->lists('name', 'id_segregation_event');

        $lStatusRec = SStatus::where('is_deleted', false)
                                ->where('id_segregation_event',5)
                                ->orwhere('id_segregation_event',6)
                                ->orwhere('id_segregation_event',7)
                                ->lists('name', 'id_segregation_event');
        $lStatusLib = SStatus::where('is_deleted', false)
                                ->where('id_segregation_event',8)
                                ->orwhere('id_segregation_event',9)
                                ->orwhere('id_segregation_event',10)
                                ->lists('name', 'id_segregation_event');
        $warehouses = SLocation::join('wmsu_whs','whs_id','=','wmsu_whs.id_whs')
                                  ->where('branch_id',session('branch')->id_branch)
                                  ->where(function ($query){
                                      $query->where('is_recondition',1)
                                            ->orWhere('is_reprocess',1)
                                            ->orWhere('is_destruction',1);
                                  })
                                  ->get();

        return view('wms.segregations.index')
                    ->with('lStatusSeg', $lStatusSeg)
                    ->with('lStatusRec', $lStatusRec)
                    ->with('lStatusLib', $lStatusLib)
                    ->with('tFilterDate', session('work_date'))
                    ->with('sTitle', $sTitle)
                    ->with('iQualityType', $iQualityType)
                    ->with('typeView', $typeView)
                    ->with('data', $segregated);
    }

    public function binnacle(Request $request){
      $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;

      //$oFilterDate = Carbon::parse($sFilterDate);
      $sFilterLot = $request->filterLot == null ? 0 : $request->filterLot;
      $sFilterPallet = $request->filterPallet == null ? 0 : $request->filterPallet;
      $sFilterItem = $request->filterItem == null ? 0 : $request->filterItem;
      $sFilterUser = $request->filterUser == null ? 0 : $request->filterUser;
      $sFilterEvent = $request->filterEvent == null ? 0 : $request->filterEvent;
      $segregated = session('segregation')->segregatebinnacle($sFilterDate,$sFilterLot,$sFilterPallet,$sFilterItem,$sFilterUser,$sFilterEvent);
      $lItem = session('segregation')->binnacleItem();
      $lLot = session('segregation')->binnacleLot();
      $lPallet = session('segregation')->binnaclePallet();
      $lEvent = session('segregation')->binnacleEvent();
      $lUser = session('segregation')->binnacleUser();
      return view('wms.segregations.binnacle')
                ->with('oFilterDate',$sFilterDate)
                ->with('sFilterItem',$sFilterItem)
                ->with('sFilterLot',$sFilterLot)
                ->with('sFilterPallet',$sFilterPallet)
                ->with('sFilterEvent',$sFilterEvent)
                ->with('sFilterUser',$sFilterUser)
                ->with('data', $segregated)
                ->with('lItem', $lItem)
                ->with('lLot',$lLot)
                ->with('lPallet',$lPallet)
                ->with('lEvent',$lEvent)
                ->with('lUser',$lUser);
    }

    /**
     * set the value of data from client to session('data')
     * get the data from the view to classify in segregations context
     *
     * @param  Request $request
     */
    public function process(Request $request)
    {
        $val = $request->value;
        $aParameters = array();
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_ITEM')] = $val[\Config::get('scwms.SEG_PARAM.ID_ITEM')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_UNIT')] = $val[\Config::get('scwms.SEG_PARAM.ID_UNIT')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_LOT')] = $val[\Config::get('scwms.SEG_PARAM.ID_LOT')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_PALLET')] = $val[\Config::get('scwms.SEG_PARAM.ID_PALLET')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_WHS')] = $val[\Config::get('scwms.SEG_PARAM.ID_WHS')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_BRANCH')] = $val[\Config::get('scwms.SEG_PARAM.ID_BRANCH')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_REFERENCE')] = $val[\Config::get('scwms.SEG_PARAM.ID_REFERENCE')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')] = $val[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')] = $val[23];
        $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')] = $val[22];
        $aParameters[\Config::get('scwms.SEG_PARAM.EVENT')] = $val[21];
        if(count($val)>=25){
          $aParameters[\Config::get('scwms.SEG_PARAM.NOTE')] = $val[24];
          if(count($val)>=27){
            $aParameters[\Config::get('scwms.SEG_PARAM.WAREHOUSE')] = $val[25];
            $aParameters[\Config::get('scwms.SEG_PARAM.LOCATION')] = $val[26];
          }
        }
        session('segregation')->classify($aParameters);


        return redirect()->route('wms.whs.index');
    }

    public function findWarehouse(Request $request){
      if($request->id==8){
        $data = SLocation::select('id_whs','wmsu_whs.name')
                          ->join('wmsu_whs','whs_id','=','wmsu_whs.id_whs')
                          ->where('branch_id',session('branch')->id_branch)
                          ->where('is_recondition',1)
                          ->groupBy('wmsu_whs.name')
                          ->get();
        return response()->json($data);
      }
      if($request->id==9){
        $data = SLocation::select('id_whs','wmsu_whs.name')
                          ->join('wmsu_whs','whs_id','=','wmsu_whs.id_whs')
                          ->where('branch_id',session('branch')->id_branch)
                          ->where('is_reprocess',1)
                          ->groupBy('wmsu_whs.name')
                          ->get();
        return response()->json($data);
      }
      if($request->id==10){
        $data = SLocation::select('id_whs','wmsu_whs.name')
                          ->join('wmsu_whs','whs_id','=','wmsu_whs.id_whs')
                          ->where('branch_id',session('branch')->id_branch)
                          ->where('is_destruction',1)
                          ->groupBy('wmsu_whs.name')
                          ->get();
        return response()->json($data);
      }
    }
    public function findLocations(Request $request){
      if($request->status==8){
        $data = SLocation::select('id_whs_location','name')
                          ->where('whs_id',$request->id)
                          ->where('is_recondition',1)
                          ->get();
        return response()->json($data);
      }
      if($request->status==9){
        $data = SLocation::select('id_whs_location','name')
                          ->where('whs_id',$request->id)
                          ->where('is_reprocess',1)
                          ->get();
        return response()->json($data);
      }
      if($request->status==10){
        $data = SLocation::select('id_whs_location','name')
                          ->where('whs_id',$request->id)
                          ->where('is_destruction',1)
                          ->get();
        return response()->json($data);
      }
    }

    public function consult($title,$type){
        return view('wms.segregations.consult')
                  ->with('title',$title)
                  ->with('type',$type);

    }

    public static function toQuarentine(Request $request){

      $type = substr($request->codigo, 0 , 1 );
      if($type != 2)
      {
        Flash::error('No es una tarima');
            return redirect()->route('qms.segregations.consult',[
                                      trans('qms.VIEW_INS_QUA'),
                                      $request->type
                                    ]);
      }

      $data = SBarcode::decodeBarcode($request->codigo);
      $segregated = session('segregation')->segregatePalletRow($data->id_pallet,1);
      if($segregated == NULL)
      {
        Flash::error('La tarima no esta en almacen de calidad');
            return redirect()->route('qms.segregations.consult',[
                                      trans('qms.VIEW_INS_QUA'),
                                      $request->type
                                    ]);
      }
      return view('wms.segregations.info')
                ->with('data',$segregated)
                ->with('title','Movimiento de Por inspeccionar a Cuarentena')
                ->with('newQ',4)
                ->with('type',$request->type);

    }


    public static function toRelease(Request $request){

      $type = substr($request->codigo, 0 , 1 );
      if($type != 2)
      {
        Flash::error('No es una tarima');
            return redirect()->route('qms.segregations.consult',[
                                      trans('qms.VIEW_REL'),
                                      $request->type
                                    ]);
      }
      $data = SBarcode::decodeBarcode($request->codigo);
      $segregated = session('segregation')->segregatePalletRow($data->id_pallet,0);
      if($segregated == NULL)
      {
        Flash::error('No es una tarima');
            return redirect()->route('qms.segregations.consult',[
                                      trans('qms.VIEW_REL'),
                                      $request->type
                                    ]);
      }
      $lStatusRec = SStatus::where('is_deleted', false)
                              ->where('id_segregation_event',5)
                              ->orwhere('id_segregation_event',6)
                              ->orwhere('id_segregation_event',7)
                              ->lists('name', 'id_segregation_event');
      return view('wms.segregations.info')
                ->with('data',$segregated)
                ->with('title','Movimiento de Liberación')
                ->with('newQ',0)
                ->with('type',$request->type)
                ->with('lStatusRec',$lStatusRec);
    }

    public static function toRefuse(Request $request){

      $type = substr($request->codigo, 0 , 1 );
      if($type != 2)
      {
        Flash::error('No es una tarima');
            return redirect()->route('qms.segregations.consult',[
                                      trans('qms.VIEW_REF'),
                                      $request->type
                                    ]);
      }
      $data = SBarcode::decodeBarcode($request->codigo);
      $segregated = session('segregation')->segregatePalletRow($data->id_pallet,0);
      if($segregated == NULL)
      {
        Flash::error('No es una tarima');
            return redirect()->route('qms.segregations.consult',[
                                      trans('qms.VIEW_REF'),
                                      $request->type
                                    ]);
      }

      $lStatusLib = SStatus::where('is_deleted', false)
                              ->where('id_segregation_event',8)
                              ->orwhere('id_segregation_event',9)
                              ->orwhere('id_segregation_event',10)
                              ->lists('name', 'id_segregation_event');
      return view('wms.segregations.info')
                ->with('data',$segregated)
                ->with('title','Movimiento de Rechazo')
                ->with('newQ',1)
                ->with('type',$request->type)
                ->with('lStatusLib',$lStatusLib);
    }

    public static function prepareData(Request $request){

      $aParameters = array();
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_ITEM')] = $request->id_item;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_UNIT')] = $request->id_unit;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_LOT')] = $request->id_lot;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_PALLET')] = $request->id_pallet;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_WHS')] = $request->id_whs;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_BRANCH')] = $request->branch_id;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_REFERENCE')] =$request->id_reference;
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')] = $request->segregation_type_id;
      if($request->newQ == 0){
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')] = $request->statusRlP;
      }else if($request->newQ == 1){
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')] = $request->statusRFP;
        $aParameters[\Config::get('scwms.SEG_PARAM.WAREHOUSE')] = $request->almacen;
        $aParameters[\Config::get('scwms.SEG_PARAM.LOCATION')] = $request->ubicacion;
      } else{
      $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')] = $request->newQ;
      }
      $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')] = 0;
      $aParameters[\Config::get('scwms.SEG_PARAM.EVENT')] = $request->id_segregation_event;


      session('segregation')->classify($aParameters);

      Flash::success('Se inserto correctamente');
          return redirect()->route('qms.segregations.consult',[
                                    trans('qms.VIEW_INS_QUA'),
                                    $request->type
                                  ]);

    }



}
