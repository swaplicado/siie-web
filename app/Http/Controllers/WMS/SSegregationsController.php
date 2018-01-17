<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

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
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.SEGREGATIONS'), \Config::get('scsys.MODULES.WMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sTitle, $iSegregationType)
    {
        $segregated = session('segregation')->getSegregated($iSegregationType);
        $lQltyStatus = SStatus::where('is_deleted', false)
                                ->lists('name', 'id_status');

        return view('wms.segregations.index')
                    ->with('lStatus', $lQltyStatus)
                    ->with('sTitle', $sTitle)
                    ->with('data', $segregated);
    }

    /**
     * [getTable set the value of data from client to session('data')]
     *
     * @param  Request $request [$request->value]
     */
    public function process(Request $request)
    {
        \Debugbar::info($request);
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

        $this->classify($request, $aParameters);

        return redirect()->route('wms.whs.index');
    }


    public function segregate(Request $request, $oMovement, $iSegregationType)
    {
        \DB::connection('company')->transaction(function() use ($oMovement, $request, $iSegregationType) {
          $oSegregation = new SSegregation();

          $oSegregation->is_deleted = false;
          $oSegregation->segregation_type_id = $iSegregationType; // Pendiente constantes

          $iReference = 1;
          if ($oMovement->doc_order_id > 1){
            $iReference = $oMovement->doc_order_id;
          }
          elseif ($oMovement->doc_invoice_id > 1) {
            $iReference = $oMovement->doc_invoice_id;
          }
          elseif ($oMovement->doc_debit_note_id > 1) {
            $iReference = $oMovement->doc_debit_note_id;
          }
          elseif ($oMovement->doc_credit_note_id > 1) {
            $iReference = $oMovement->doc_credit_note_id;
          }

          $oSegregation->reference_id = $iReference;
          $oSegregation->created_by_id = \Auth::user()->id;
          $oSegregation->updated_by_id = \Auth::user()->id;
          $oSegregation->save();

          foreach ($oMovement->rows as $movRow)
          {
            $oSegRow = new SSegregationRow();

            $oSegRow->quantity = sizeof($movRow->lotRows) > 0 ? 0 : $movRow->quantity;
            $oSegRow->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
            $oSegRow->pallet_id = $movRow->pallet_id;
            $oSegRow->whs_id = $oMovement->whs_id;
            $oSegRow->branch_id = $oMovement->branch_id;
            $oSegRow->year_id = 1;
            $oSegRow->item_id = $movRow->item_id;
            $oSegRow->unit_id = $movRow->unit_id;
            $oSegRow->quality_status_id = \Config::get('scqms.TO_EVALUATE');

            $oSegregation->rows()->save($oSegRow);

            foreach ($movRow->lotRows as $lotRow)
            {
              $oSegLotRow = new SSegregationLotRow();

              $oSegLotRow->quantity = $lotRow->quantity;
              $oSegLotRow->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
              $oSegLotRow->lot_id = $lotRow->lot_id;
              $oSegLotRow->year_id = 1;
              $oSegLotRow->item_id = $movRow->item_id;
              $oSegLotRow->unit_id = $movRow->unit_id;
              $oSegLotRow->quality_status_id = \Config::get('scqms.TO_EVALUATE');

              $oSegRow->lotRows()->save($oSegLotRow);
            }
          }
        });
    }

    public function classify(Request $request, $aParameters)
    {
        \DB::connection('company')->transaction(function() use ($aParameters, $request) {

          $iIdItem = $aParameters[\Config::get('scwms.SEG_PARAM.ID_ITEM')];
          $iIdUnit = $aParameters[\Config::get('scwms.SEG_PARAM.ID_UNIT')];
          $iIdLot = $aParameters[\Config::get('scwms.SEG_PARAM.ID_LOT')];
          $iIdPallet = $aParameters[\Config::get('scwms.SEG_PARAM.ID_PALLET')];
          $iIdWhs = $aParameters[\Config::get('scwms.SEG_PARAM.ID_WHS')];
          $iIdBranch = $aParameters[\Config::get('scwms.SEG_PARAM.ID_BRANCH')];
          $iIdDocument = $aParameters[\Config::get('scwms.SEG_PARAM.ID_DOCUMENT')];
          $iIdQltyPrev = $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_PREV')];
          $iIdQltyNew = $aParameters[\Config::get('scwms.SEG_PARAM.ID_STATUS_QLTY_NEW')];
          $dQuantity = $aParameters[\Config::get('scwms.SEG_PARAM.QUANTITY')];

          $oSegregation = new SSegregation();

          $oSegregation->is_deleted = false;
          $oSegregation->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.QUALITY');
          $oSegregation->reference_id = $iIdDocument;
          $oSegregation->created_by_id = \Auth::user()->id;
          $oSegregation->updated_by_id = \Auth::user()->id;

          $oSegregation->save();

          $oSegRow = new SSegregationRow();

          $oSegRow->quantity = $iIdLot > 1 ? 0 : $dQuantity;
          $oSegRow->move_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
          $oSegRow->pallet_id = $iIdPallet;
          $oSegRow->whs_id = $iIdWhs;
          $oSegRow->branch_id = $iIdBranch;
          $oSegRow->year_id = 1;
          $oSegRow->item_id = $iIdItem;
          $oSegRow->unit_id = $iIdUnit;
          $oSegRow->quality_status_id = $iIdQltyPrev;

          $oSegRowMirror = clone $oSegRow;
          $oSegRowMirror->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
          $oSegRowMirror->quality_status_id = $iIdQltyNew;

          $oSegregation->rows()->save($oSegRow);
          $oSegregation->rows()->save($oSegRowMirror);

          $oSegLotRow = new SSegregationLotRow();

          if ($iIdLot > 1) {
            $oSegLotRow->quantity = $dQuantity;
            $oSegLotRow->move_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
            $oSegLotRow->lot_id = $iIdLot;
            $oSegLotRow->year_id = 1;
            $oSegLotRow->item_id = $iIdItem;
            $oSegLotRow->unit_id = $iIdUnit;
            $oSegLotRow->quality_status_id = $iIdQltyPrev;

            $oSegLotRowMirror = clone $oSegLotRow;
            $oSegLotRowMirror->move_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
            $oSegLotRowMirror->quality_status_id = $iIdQltyNew;

            $oSegRow->lotRows()->save($oSegLotRow);
            $oSegRowMirror->lotRows()->save($oSegLotRowMirror);
          }
        });
    }

}
