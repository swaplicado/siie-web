<?php namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;
use Carbon\Carbon;

use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\WMS\Segregation\SSegregation;
use App\WMS\Segregation\SSegregationRow;
use App\WMS\Segregation\SSegregationLotRow;
use App\WMS\SLocation;
use App\QMS\SStatus;

class SSegregationsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.SEGREGATIONS'), \Config::get('scsys.MODULES.QMS'));

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
    public function index(Request $request, $sTitle, $iSegregationType, $iQualityType)
    {
        $sFilterDate = $request->filterDate == null ? session('work_date')->format('Y-m-d') : $request->filterDate;
        $oFilterDate = Carbon::parse($sFilterDate);
        $iYearId = session('utils')->getYearId($oFilterDate->year);
        $segregated = session('segregation')->getSegregated($iSegregationType, $iQualityType);
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
                    ->with('data', $segregated);
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
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_DOCUMENT')] = $val[\Config::get('scwms.SEG_PARAM.ID_DOCUMENT')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')] = $val[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')];
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')] = $val[23];
        $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')] = $val[22];
        $aParameters[10] = $val[20];
        $aParameters[11] = $val[21];
        if(count($val)>=26){
        $aParameters[12] = $val[24];
        $aParameters[13] = $val[25];
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

}
