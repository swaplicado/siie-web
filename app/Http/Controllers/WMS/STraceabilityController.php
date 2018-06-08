<?php

namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SUtil;
use Laracasts\Flash\Flash;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\SUtils\SProcess;
use App\ERP\SItem;
use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\WMS\SLocation;
use App\SBarcode\SBarcode;
use App\SCore\SStockManagment;
use App\WMS\SComponetBarcode;

class STraceabilityController extends Controller
{

    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.STK_MOVS'), \Config::get('scsys.MODULES.WMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    public function consult()
    {
       return view('wms.lots.consultTraceability');
    }

    public function getTraceability(Request $request)
    {
      $data = SBarcode::decodeBarcode($request->codigo);
      if($data == null || $data->id_item!=null)
      {
        Flash::error('No existe el lote');
        return redirect()->route('wms.traceability.consult');
      }
      $type = substr($request->codigo, 0 , 1 );
      if($type == 2 || $type == 3)
      {
        Flash::error('Lo ingresado no es un lote');
        return redirect()->route('wms.traceability.consult');
      }
      $query = SStockManagment::getMovementsLots($data);
      $iIndex = 1;
      $dInputs = 0;
      $dOutputs = 0;
      $dStock = 0;
      $dBalance = 0;
      foreach ($query as $row) {
         $dInputs += $row->inputs;
         $dOutputs += $row->outputs;
         $dStock += $row->inputs - $row->outputs;
         $row->stock = $dStock;

         $dBalance += $row->debit - $row->credit;
         $row->balance = $dBalance;

         $row->index = $iIndex++;
      }
      $data->item;
      $data->unit;

      return view('wms.lots.traceability')
              ->with('sTitle',"Trazabilidad de lote")
              ->with('query',$query)
              ->with('data',$data)
              ->with('dInputs',$dInputs)
              ->with('dOutputs',$dOutputs)
              ->with('dStock',$dStock);

    }


}
?>
