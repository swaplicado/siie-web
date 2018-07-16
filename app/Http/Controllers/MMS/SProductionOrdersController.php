<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;
use App\MMS\SProductionOrder;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\MMS\STypeOrder;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\MMS\SProductionPlan;
use App\MMS\Formulas\SFormula;
use App\MMS\Formulas\SFormulaRow;
use App\MMS\Formulas\SFormulaSubstitute;
use App\MMS\Formulas\SFormulaNote;

class SProductionOrdersController extends Controller
{
  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;

  public function __construct()
  {
       $this->oCurrentUserPermission = SProcess::constructor($this,
       \Config::get('scperm.PERMISSION.MMS_PRODUCTION_PLANES'),
       \Config::get('scsys.MODULES.MMS'));

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
         $order = SProductionOrder::Search($request->name, $this->iFilter)->orderBy('id_order','ASC')->paginate(10);

         $order->each(function($order){
           $order->branch;
           $order->plan;
           $order->floor;
           $order->formula;
           $order->type;
           $order->status;
         });


        $sTitle = 'Orden de producción';

         return view('mms.orders.index')
             ->with('orders', $order)
             ->with('sTitle', $sTitle)
             ->with('actualUserPermission', $this->oCurrentUserPermission)
             ->with('iFilter', $this->iFilter);
     }

    public function create()
    {
      if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
      {
        return redirect()->route('notauthorized');
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
      $formula = SFormula::orderBy('id_formula','ASC')->lists('identifier','id_formula');
      $plan = SProductionPlan::orderBy('id_production_plan','ASC')->lists('production_plan','id_production_plan');
      $order = SProductionOrder::orderBy('id_order','ASC')->lists('folio','id_order');
      return view('mms.orders.createEdit')
                    ->with('branches', $branch)
                    ->with('plans', $plan)
                    ->with('father', $order)
                    ->with('types', $type)
                    ->with('items',$item)
                    ->with('formulas',$formula);
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

      $oOrder = SProductionOrder::max('folio');

      $order->folio = ($oOrder + 1);

      $plan = SProductionPlan::find($request->plan_id);
      $item = SItem::find($request->item_id);
      if($order->father_order == "");{
        $order->father_order = 0;
      }
      $order->status_id = 1;
      $order->floor_id = $plan->floor_id;
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
      $formula = SFormula::orderBy('id_formula','ASC')->lists('identifier','id_formula');
      $plan = SProductionPlan::orderBy('id_production_plan','ASC')->lists('production_plan','id_production_plan');
      $order = SProductionOrder::orderBy('id_order','ASC')->lists('folio','id_order');
      return view('mms.orders.createEdit')
                    ->with('orders',$order)
                    ->with('branches', $branch)
                    ->with('plans', $plan)
                    ->with('father', $order)
                    ->with('types', $type)
                    ->with('items',$item)
                    ->with('formulas',$formula);
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

    }

    public function activate(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

    }

    public function findFormulas(Request $request){
      $data= SFormula::select('id_formula','identifier')
                      ->where('item_id',$request->id)
                      ->get();
      return response()->json($data);
    }
}
