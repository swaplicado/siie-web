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
        $lQltyStatus = SStatus::where('is_deleted', false)
                                ->lists('name', 'id_status');

        return view('wms.segregations.index')
                    ->with('lStatus', $lQltyStatus)
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
        $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')] = $val[19];
        $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')] = $val[18];

        session('segregation')->classify($aParameters);

        return redirect()->route('wms.whs.index');
    }

}
