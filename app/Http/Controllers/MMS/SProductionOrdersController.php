<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;
use App\SCore\SProductionCore;
use App\SCore\SProductionOrderCore;
use App\SCore\SExplosionCore;
use App\SUtils\SGuiUtils;
use App\Database\Config;

use App\MMS\SProductionOrder;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\MMS\STypeOrder;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\MMS\SProductionPlan;
use App\MMS\SStatusOrder;
use App\MMS\Formulas\SFormula;
use App\MMS\Formulas\SFormulaRow;
use App\MMS\Formulas\SFormulaSubstitute;
use App\MMS\Formulas\SFormulaNote;

class SProductionOrdersController extends Controller
{
  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;
  private $lOrderStatus;

  public function __construct()
  {
       $this->oCurrentUserPermission = SProcess::constructor($this,
                         \Config::get('scperm.PERMISSION.MMS_PRODUCTION_ORDERS'),
                         \Config::get('scsys.MODULES.MMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
       $this->lOrderStatus = SStatusOrder::where('is_deleted', false)
                                          ->orderBy('id_status', 'asc')
                                          ->lists('name', 'id_status');
       $this->lOrderStatus['0'] = 'TODAS';
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index(Request $request)
   {
       $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
       $iOrderStatus = $request->po_status == null ? \Config::get('scmms.PO_STATUS.ST_ALL') : $request->po_status;
       $sFilterDate = $request->filterDate == null ? SGuiUtils::getCurrentMonth() : $request->filterDate;

       $sSelect = '
                     mpo.id_order,
                     mpo.folio,
                     mpo.identifier,
                     mpo.date,
                     mpo.charges,
                     mpo.is_deleted,
                     mpo.plan_id,
                     mpo.branch_id,
                     mpo.floor_id,
                     mpo.type_id,
                     mpo.status_id,
                     mpo.item_id,
                     mpo.unit_id,
                     mpo.father_order_id,
                     mpo.formula_id,
                     mpo.created_by_id,
                     mpo.updated_by_id,
                     mpo.created_at,
                     mpo.updated_at,
                     ei.code AS item_code,
                     ei.name AS item,
                     eu.code AS unit_code,
                     eu.name AS unit,
                     mpp.folio AS plan_folio,
                     mpp.production_plan AS production_plan,
                     mfl.name AS floor_name,
                     mto.name AS type_name,
                     mso.name AS status_name,
                     mso.name AS status_name,
                     mf.identifier AS form_identifier,
                     mf.version AS form_version,
                     eb.name AS branch_name,
                     mpof.folio AS father_folio,
                     uc.username AS creation_user_name,
                     uu.username AS mod_user_name,
                     "0" AS rows
                   ';

       $oOrdersQuery = \DB::connection(session('db_configuration')->getConnCompany())
                    ->table('mms_production_orders as mpo')
                    ->join('erpu_items as ei', 'mpo.item_id', '=', 'ei.id_item')
                    ->join('erpu_units as eu', 'mpo.unit_id', '=', 'eu.id_unit')
                    ->join('mms_production_planes as mpp', 'mpo.plan_id', '=', 'mpp.id_production_plan')
                    ->join('mms_formulas as mf', 'mpo.formula_id', '=', 'mf.id_formula')
                    ->join('erpu_branches as eb', 'mpo.branch_id', '=', 'eb.id_branch')
                    ->join('mms_floor as mfl', 'mpo.floor_id', '=', 'mfl.id_floor')
                    ->join('mms_type_order as mto', 'mpo.type_id', '=', 'mto.id_type')
                    ->join('mms_status_order as mso', 'mpo.status_id', '=', 'mso.id_status')
                    ->join('mms_production_orders as mpof', 'mpo.father_order_id', '=', 'mpof.id_order')
                    ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'mpo.created_by_id', '=', 'uc.id')
                    ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'mpo.updated_by_id', '=', 'uu.id');

       if (\Config::get('scmms.PO_STATUS.ST_ALL') != $iOrderStatus) {
           $oOrdersQuery = $oOrdersQuery->where('status_id', $iOrderStatus);
       }

       $aDates = SGuiUtils::getDatesOfFilter($sFilterDate);
       $oOrdersQuery = $oOrdersQuery->whereBetween('mpo.date', [$aDates[0]->toDateString(), $aDates[1]->toDateString()]);

       switch ($this->iFilter) {
         case \Config::get('scsys.FILTER.ACTIVES'):
             $oOrdersQuery = $oOrdersQuery->where('mpo.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
           break;

         case \Config::get('scsys.FILTER.DELETED'):
             $oOrdersQuery = $oOrdersQuery->where('mpo.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
           break;

         default:
       }

       $lOrders = $oOrdersQuery->select(\DB::raw($sSelect))
                     ->where('mpo.identifier', 'LIKE', "%".$request->name."%")
                     ->get();

      $sTitle = trans('mms.PRODUCTION_ORDERS');

       return view('mms.orders.index')
               ->with('orders', $lOrders)
               ->with('lOrderStatus', $this->lOrderStatus)
               ->with('iOrderStatus', $iOrderStatus)
               ->with('sTitle', $sTitle)
               ->with('actualUserPermission', $this->oCurrentUserPermission)
               ->with('sFilterDate', $sFilterDate)
               ->with('iFilter', $this->iFilter);
   }

   public function getOrderDetail($iPO)
   {
       $oExplosion = new SExplosionCore();
       $oProductionOrder = SProductionOrder::find($iPO);

       $lRows = $oExplosion->getRowsFromFormula($oProductionOrder->formula_id);

       foreach ($lRows as $oRow) {
         $oRow->dRequired = $oRow->quantity * $oProductionOrder->charges;
         $oConsumption = SProductionCore::getConsumption($iPO, $oRow->item_id, $oRow->unit_id, true);
         $oRow->oConsumtion = $oConsumption;
       }

       return json_encode($lRows);
   }

    public function create()
    {
      if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
      {
        return redirect()->route('notauthorized');
      }

      $branch = session('utils')->getUserBranchesArrayWithName(\Auth::user()->id,
                                                      session('partner')->id_partner,
                                                      true);

      $type = STypeOrder::orderBy('name','ASC')->lists('name','id_type');

      $item = SItem::select(\DB::raw("CONCAT(erpu_items.code, ' - ', erpu_items.name, '-', eu.code) as item"),
                                  \DB::raw("erpu_items.id_item"))
                    ->join('erpu_item_genders as eig', 'erpu_items.item_gender_id', '=', 'eig.id_item_gender')
                    ->join('erpu_units as eu', 'erpu_items.unit_id', '=', 'eu.id_unit')
                    ->where(function ($q) {
                          $q->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'))
                          ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.FINISHED_PRODUCT'));
                      })
                    ->where('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.PRODUCT'))
                    ->where('eig.is_deleted', false)
                    ->where('erpu_items.is_deleted', false)
                    ->orderBy('eig.item_type_id', 'ASC')
                    ->orderBy('item', 'ASC')
                    ->lists('item','erpu_items.id_item');

      $plan = SProductionPlan::selectRaw('(CONCAT(LPAD(folio, '.session('long_folios').', "0"),
                                            "-", production_plan)) as plan,
                                            id_production_plan')
                              ->where('is_deleted', false)
                              ->orderBy('id_production_plan', 'ASC')
                              ->lists('plan','id_production_plan');

        $father = SProductionOrder::orderBy('folio', 'DESC')
                              ->selectRaw('(CONCAT(LPAD(folio, '.session('long_folios').', "0"),
                                                                    "-", identifier)) as prod_ord,
                                                                    id_order')
                              ->where(function ($query) {
                                  $query->where('is_deleted', false)
                                        ->orWhere('id_order', 1);
                              })
                              ->lists('prod_ord', 'id_order');

      return view('mms.orders.createEdit')
                    ->with('branches', $branch)
                    ->with('plans', $plan)
                    ->with('father', $father)
                    ->with('types', $type)
                    ->with('items', $item);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order = new SProductionOrder($request->all());

        $iLastFolio = SProductionOrder::max('folio');

        $order->folio = ($iLastFolio + 1);

        $plan = SProductionPlan::find($request->plan_id);

        $item = SItem::find($request->item_id);
        if ($order->father_order_id == "") {
          $order->father_order_id = 1;
        }
        $order->status_id = 1;
        $order->floor_id = $plan->floor_id;
        $order->branch_id = $plan->floor->branch_id;
        $order->unit_id = $item->unit_id;
        $order->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $order->updated_by_id = \Auth::user()->id;
        $order->created_by_id = \Auth::user()->id;

        $order->save();

        Flash::success(trans('messages.REG_CREATED'))->important();

        return redirect()->route('mms.orders.index');
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
        $order = SProductionOrder::find($id);
        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $order);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($order);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($order);
        }

        $branch = SBranch::where('partner_id', session('partner')->id_partner)
                    ->where('is_deleted', false)
                    ->orderBy('name', 'ASC')
                    ->lists('name', 'id_branch');

        $type = STypeOrder::orderBy('name','ASC')->lists('name','id_type');
        $item = SItem::select(\DB::raw("CONCAT(erpu_items.code, ' - ', erpu_items.name, '-', eu.code) as item"),
                                    \DB::raw("erpu_items.id_item"))
                      ->join('erpu_item_genders as eig', 'erpu_items.item_gender_id', '=', 'eig.id_item_gender')
                      ->join('erpu_units as eu', 'erpu_items.unit_id', '=', 'eu.id_unit')
                      ->where(function ($q) {
                            $q->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'))
                            ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.FINISHED_PRODUCT'));
                        })
                      ->where('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.PRODUCT'))
                      ->where('eig.is_deleted', false)
                      ->where('erpu_items.is_deleted', false)
                      ->orderBy('eig.item_type_id', 'ASC')
                      ->lists('item','erpu_items.id_item');

        $formulas = SFormula::orderBy('recipe','ASC')
                              ->orderBy('version', 'ASC')
                              ->where('item_id', $order->item_id)
                              ->selectRaw('id_formula, CONCAT(identifier, "-v", version) AS identifier')
                              ->lists('identifier','id_formula');

        $plan = SProductionPlan::orderBy('id_production_plan','ASC')->lists('production_plan','id_production_plan');
        $father = SProductionOrder::orderBy('folio', 'DESC')
                              ->selectRaw('(CONCAT(LPAD(folio, '.session('long_folios').', "0"),
                                                                    "-", identifier)) as prod_ord,
                                                                    id_order')
                              ->where(function ($query) {
                                  $query->where('is_deleted', false)
                                        ->orWhere('id_order', 1);
                              })
                              ->lists('prod_ord', 'id_order');

        return view('mms.orders.createEdit')
                      ->with('orders', $order)
                      ->with('branches', $branch)
                      ->with('plans', $plan)
                      ->with('father', $father)
                      ->with('types', $type)
                      ->with('items', $item)
                      ->with('formulas', $formulas);
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
        $oProductionOrder = SProductionOrder::find($id);
        $oProductionOrder->fill($request->all());

        $plan = SProductionOrder::find($request->plan_id);

        $oProductionOrder->floor_id = $plan->floor_id;
        $oProductionOrder->branch_id = $plan->floor->branch_id;
        $oProductionOrder->updated_by_id = \Auth::user()->id;

        $errors = $oProductionOrder->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        Flash::success(trans('messages.REG_EDITED'))->important();

        return redirect()->route('mms.orders.index', 0);
    }

    /**
     * Set the is_deleted flag to true.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        session('utils')->validateDestroy($this->oCurrentUserPermission->privilege_id);

        $oProductionOrder = SProductionOrder::find($id);

        $oProductionOrder->is_deleted = \Config::get('scsys.STATUS.DEL');
        $oProductionOrder->updated_by_id = \Auth::user()->id;

        $errors = $oProductionOrder->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->route('mms.orders.index')->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('mms.orders.index');
    }

    /**
     * set the is_deleted flag to false
     *
     * @param  Request $request
     * @param  integer  $id  id of SProductionOrder
     *
     * @return redirect()->route('mms.orders.index')
     */
    public function activate(Request $request, $id)
    {
        $oProductionOrder = SProductionOrder::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oProductionOrder);

        $oProductionOrder->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oProductionOrder->updated_by_id = \Auth::user()->id;

        $errors = $oProductionOrder->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withInput($request->input())->withErrors($error);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('mms.orders.index');
    }

    /**
     * [findFormulas description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function findFormulas(Request $request) {
      $data= SFormula::selectRaw('id_formula, CONCAT(identifier, "-v", version) AS identifier')
                      ->where('item_id', $request->id)
                      ->orderBy('recipe', 'ASC')
                      ->orderBy('version', 'ASC')
                      ->get();

      return response()->json($data);
    }

    public function print($iPO = 0)
    {
        $oExplosion = new SExplosionCore();
        $oProductionOrder = SProductionOrder::find($iPO);

        $lRows = $oExplosion->getRowsFromFormula($oProductionOrder->formula_id);

        foreach ($lRows as $oRow) {
          $oRow->dRequired = $oRow->quantity * $oProductionOrder->charges;

          $oConsumption = SProductionCore::getConsumption($iPO, $oRow->item_id, $oRow->unit_id);
          $dCharged = 0;
          $dConsumed = 0;
          $dReturned = 0;
          $sLots = '';

          if (sizeof($oConsumption) > 0) {
              foreach ($oConsumption as $oConsum) {
                $dCharged += $oConsum->delivered;
                $dConsumed += $oConsum->consumed;
                $dReturned += $oConsum->returned;

                if ($oConsum->delivered > 0 || $oConsum->consumed > 0) {
                  $sLots = $sLots.$oConsum->lot.'; ';
                }
              }
          }

          $oRow->dCharged = $dCharged - $dReturned;
          $oRow->dConsumed = $dConsumed;
          $oRow->dReturned = $dReturned;
          $oRow->sLots = $sLots;
        }

        $oCore = new SExplosionCore();
        $lIngredients = $oCore->explode($oProductionOrder, [], session('work_date'), false);

        $view = view('mms.orders.printorder', ['oProductionOrder' => $oProductionOrder,
                                          'lIngredients' => $lRows])->render();
        // set ukuran kertas dan orientasi
        $pdf = \PDF::loadHTML($view)->setPaper('letter', 'potrait')->setWarnings(false);
        // cetak
        return $pdf->stream();
    }

    /**
     * redirect to method change
     *
     * @param  integer   $iProductionOrder id of SProductionOrder
     *
     */
    public function next($iProductionOrder)
    {
        $oRes = $this->changeStatus($iProductionOrder, \Config::get('scmms.NEXT_ST'));

        // if (!is_array($oRes) && $oRes) {
        //     Flash::success(trans('messages.STATUS_CHANGED'))->important();
        //
        //     return redirect()->route('mms.orders.index');
        // }
        // else {
        //     return redirect()->back()->withErrors($oRes);
        // }

        return json_encode($oRes);
    }

    /**
     * redirect to method change
     *
     * @param  integer   $iProductionOrder id of SProductionOrder
     *
     */
    public function previous($iProductionOrder)
    {
        $oRes = $this->changeStatus($iProductionOrder, \Config::get('scmms.PREVIOUS_ST'));

        // if (!is_array($oRes) && $oRes) {
        //     Flash::success(trans('messages.STATUS_CHANGED'))->important();
        //
        //     return redirect()->route('mms.orders.index');
        // }
        // else {
        //     return redirect()->back()->withErrors($oRes);
        // }

        return json_encode($oRes);
    }

    /**
     * changes the status of registry
     *
     * @param  integer $iProductionOrder id of SProductionOrder
     * @param  integer $iOperation       \Config::get('scmms.NEXT_ST')
     *                                   \Config::get('scmms.PREVIOUS_ST')
     * @return function
     */
    private function changeStatus($iProductionOrder, $iOperation)
    {
       $oProductionOrder = SProductionOrder::find($iProductionOrder);

       session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oProductionOrder);

       $rValid = false;
       if ($iOperation == \Config::get('scmms.NEXT_ST')) {
         $rValid = SProductionOrderCore::validateNextStatus($oProductionOrder->id_order,
                                                            $oProductionOrder->status_id);
       }
       else {
         $rValid = SProductionOrderCore::validatePreviousStatus($oProductionOrder->id_order,
                                                            $oProductionOrder->status_id);
       }

       if (!is_array($rValid) && $rValid) {
           if ($iOperation == \Config::get('scmms.NEXT_ST')) {
              $oProcessResult = SProductionOrderCore::toChangeStatus($oProductionOrder, $oProductionOrder->status_id + 1);
              $oProductionOrder->status_id = $oProductionOrder->status_id + 1;
           }
           else {
              $oProcessResult = SProductionOrderCore::toChangeStatus($oProductionOrder, $oProductionOrder->status_id - 1);
              $oProductionOrder->status_id = $oProductionOrder->status_id - 1;
           }

           $oProductionOrder->updated_by_id = \Auth::user()->id;

           $errors = $oProductionOrder->save();
           if (sizeof($errors) > 0)
           {
              return $errors;
           }

           return true;
       }
       else {
         return $rValid;
       }
    }

    public function getKardex($iPO = 0)
    {
       $oData = new \App\MMS\data\SData();
       $oPO = SProductionOrder::find($iPO);
       $oPO->plan;
       $oPO->formula;
       $oPO->item;
       $oPO->unit;
       $oPO->type;

       $sJanuary = session('work_date')->year.'-01-01';
       $sCutoffDate = session('work_date')->toDateString();

       $sSelect = 'wm.dt_date,
                   CONCAT(wmt.code, "-", wm.folio) AS folio,
                   wmt.code AS mvt_code,
                   wmt.name AS mvt_name,
                   CONCAT(ei.code, "-", ei.name) AS item,
                   wmr.pallet_id AS pallet,
                   IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_IN').', wmr.quantity, 0) AS inputs,
                   IF(wm.mvt_whs_class_id = '.\Config::get('scwms.MVT_CLS_OUT').', wmr.quantity, 0) AS outputs,
                   wl.lot,
                   wl.dt_expiry,
                   eb.code AS branch_code,
                   ww.code AS whs_code,
                   wwl.code AS loc_code,
                   eu.code AS unit_code

                  ';

       $query = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('wms_mvts as wm')
                     ->join('wms_mvt_rows as wmr', 'wm.id_mvt', '=', 'wmr.mvt_id')
                     ->leftjoin('wms_mvt_row_lots as wmrl', 'wmr.id_mvt_row', '=', 'wmrl.mvt_row_id')
                     ->join('wmss_mvt_types as wmt', 'wm.mvt_whs_type_id', '=', 'wmt.id_mvt_type')
                     ->join('erpu_items as ei', 'wmr.item_id', '=', 'ei.id_item')
                     ->join('erpu_units as eu', 'wmr.unit_id', '=', 'eu.id_unit')
                     ->join('wms_pallets as wp', 'wmr.pallet_id', '=', 'wp.id_pallet')
                     ->leftjoin('wms_lots as wl', 'wmrl.lot_id', '=', 'wl.id_lot')
                     ->join('wmsu_whs_locations as wwl', 'wmr.location_id', '=', 'wwl.id_whs_location')
                     ->join('erpu_branches as eb', 'wm.branch_id', '=', 'eb.id_branch')
                     ->join('wmsu_whs as ww', 'wm.whs_id', '=', 'ww.id_whs')
                     ->whereBetween('wm.dt_date', [$sJanuary, $sCutoffDate])
                     ->where('wm.prod_ord_id', $iPO)
                     ->where('wm.is_deleted', false)
                     ->where('wmr.is_deleted', false);

       $query = $query->select(\DB::raw($sSelect))
                       ->orderBy('wm.dt_date', 'ASC')
                       ->orderBy('wm.created_at', 'ASC')
                       ->orderBy('id_mvt', 'ASC')
                       ->get();

       $oData->oProductionOrder = $oPO;
       $oData->lKardexRows = $query;

       return json_encode($oData);
    }

    public function getConsumptions($iProductionOrder)
    {
        $oResult = SProductionCore::getConsumption($iProductionOrder);

        return json_encode($oResult);
    }

    public function consume(Request $request, $iProductionOrder)
    {
        $oResult = SProductionCore::getConsumption($iProductionOrder);// warehouse??

        $aResult = SProductionCore::processConsumption($request, $oResult, $iProductionOrder);

        return json_encode($aResult);
    }
}
