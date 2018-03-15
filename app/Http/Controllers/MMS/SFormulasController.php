<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\MMS\SFormulaRequest;

use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SBranch;
use App\SUtils\SProcess;
use App\ERP\SItem;
use App\ERP\SUnit;
use App\MMS\Formulas\SFormula;
use App\MMS\Formulas\SFormulaRow;
use App\MMS\Formulas\SFormulaSubstitute;
use App\MMS\Formulas\SFormulaNote;

class SFormulasController extends Controller {

  private $oCurrentUserPermission;
  private $iFilter;
  private $sClassNav;

  public function __construct()
  {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.MANUFACTURING'), \Config::get('scsys.MODULES.MMS'));

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
        $Formulas = SFormula::Search($request->name, $this->iFilter)->orderBy('id_formula', 'ASC')->paginate(10);

        $Formulas->each(function($Formulas) {
          $Formulas->rows;
          $Formulas->notes;
        });

        return view('mms.formulas.index')
            ->with('formulas', $Formulas)
            ->with('actualUserPermission', $this->oCurrentUserPermission)
            ->with('iFilter', $this->iFilter);
    }

    private function getProducts($value='')
    {
        $lProducts = SItem::select(\DB::raw("CONCAT(erpu_items.code, ' - ', erpu_items.name) as item"),
                                    \DB::raw("CONCAT(erpu_items.id_item, '-', erpu_items.unit_id) as id_id"))
                      ->join('erpu_item_genders as eig', 'erpu_items.item_gender_id', '=', 'eig.id_item_gender')
                      ->where(function ($q) {
                            $q->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'))
                            ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.FINISHED_PRODUCT'));
                        })
                      ->where('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.PRODUCT'))
                      ->where('eig.is_deleted', false)
                      ->where('erpu_items.is_deleted', false)
                      ->orderBy('eig.item_type_id', 'ASC')
                      ->lists('item','id_id');

        return $lProducts;
    }

    private function getMaterialsBaseQuery()
    {
        $lMaterials = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('erpu_items as ei')
                      ->join('erpu_item_genders as eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                      ->join('erps_item_types as eit', 'eit.id_item_type', '=', 'eig.item_type_id')
                      ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                      ->where(function ($q) {
                            $q->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_MATERIAL_MATERIAL'))
                            ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_PACKING_MATERIAL'))
                            ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'))
                            ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.INDIRECT_MATERIAL'));
                        })
                      ->where(function ($q) {
                        $q->where('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.MATERIAL'))
                        ->orWhere('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.PRODUCT'));
                      })
                      ->where('eig.is_deleted', false)
                      ->where('ei.is_deleted', false)
                      ->orderBy('eig.item_type_id', 'ASC')
                      ->orderBy('ei.code', 'ASC')
                      ->orderBy('ei.name', 'ASC');

        return $lMaterials;
    }

    /**
     * Show the form for creating a new folio.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($value='')
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
        {
          return redirect()->route('notauthorized');
        }

        $lProducts = $this->getProducts();

        $lMaterials = $this->getMaterialsBaseQuery();

        $lMaterialsList = clone $lMaterials;

        $lMaterials = $lMaterials->select('ei.id_item', 'ei.code', 'ei.name',
                                          'ei.unit_id', 'eu.code as unit_code',
                                          'eu.name as unit_name',
                                          'eit.name as item_type',
                                          'eit.id_item_type')
                                  ->get();

        $lMaterialsList = $lMaterialsList->select(\DB::raw("CONCAT(ei.code, ' - ', ei.name) as item"),
                                                  'id_item')
                                          ->lists('item', 'id_item');

        // foreach ($lMaterials as $material) {
        //     $material->unit;
        // }

        $lUnits = SUnit::orderBy('id_unit', 'ASC')->get();
        $lBranches = SBranch::where('partner_id', session('partner')->id_partner)
                              ->lists('name', 'id_branch');

        $oFormula = new SFormula();
        $oFormula->rows;
        $oFormula->notes;

        return view('mms.formulas.createEdit')
                      ->with('oFormula', $oFormula)
                      ->with('lUnits', $lUnits)
                      ->with('branches', $lBranches)
                      ->with('lMaterials', $lMaterials)
                      ->with('lMaterialsList', $lMaterialsList)
                      ->with('title', trans('userinterface.titles.CREATE_FORMULA'))
                      ->with('products', $lProducts);
    }

    /**
     * [store saves the formula in the database]
     * @param  SFormulaRequest $request [description]
     *
     * @return [redirect to mms.formulas.index]
     */
    public function store(SFormulaRequest $request)
    {
        $table = json_decode($request->input('formula_object'));
        $rows = $table->{'lFormulaRows'};
        $notes = $table->{'lNotes'};

        $oFormula = new SFormula($request->all());
        $oFormula->duration = 0;
        $oFormula->is_exploded = 0;
        $oFormula->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oFormula->created_by_id = \Auth::user()->id;
        $oFormula->updated_by_id = \Auth::user()->id;

        try {

          \DB::connection('company')->transaction(function() use ($oFormula, $rows, $notes) {
              $oFormula->save();
              $dDuration = 0;

              foreach ($rows as $row) {
                $oIngredient = new SFormulaRow();

                $oIngredient->id_formula_row = $row->{'iIdFormulaRow'};
                $oIngredient->dt_start = $row->{'tStart'};
                $oIngredient->dt_end = $row->{'tEnd'};
                $oIngredient->quantity = $row->{'dQuantity'};
                $oIngredient->cost = $row->{'dCost'};
                $oIngredient->duration = $row->{'dDuration'};
                $oIngredient->is_deleted = $row->{'bIsDeleted'};

                if (! $oIngredient->is_deleted) {
                    $dDuration += $row->{'dDuration'};
                }

                $oIngredient->item_id = $row->{'iIdItem'};
                $oIngredient->unit_id = $row->{'iIdUnit'};
                $oIngredient->item_formula_id = $row->{'iIdItemFormula'};
                $oIngredient->created_by_id = \Auth::user()->id;
                $oIngredient->updated_by_id = \Auth::user()->id;
                $oIngredient->formula_id = $oFormula->id_formula;

                $oIngredient->save();

                if ($row->{'iIdItemSubstitute'} != '0') {
                    $oSubstitute = new SFormulaSubstitute();

                    $oSubstitute->item_id = $row->{'iIdItemSubstitute'};
                    $oSubstitute->unit_id = $row->{'iIdUnitSubstitute'};
                    $oSubstitute->percentage = $row->{'dSuggested'};
                    $oSubstitute->percentage_max = $row->{'dMax'};
                    $oSubstitute->formula_row_id = $oIngredient->id_formula_row;
                    $oSubstitute->formula_id = $oFormula->id_formula;
                    $oSubstitute->created_by_id = \Auth::user()->id;
                    $oSubstitute->updated_by_id = \Auth::user()->id;

                    $oSubstitute->save();
                }
              }

              foreach ($notes as $note) {
                $oNote = new SFormulaNote();
                $oNote->note = $note->{'sNote'};
                $oNote->is_deleted = $note->{'bIsDeleted'};
                $oNote->formula_id = $oFormula->id_formula;
                $oNote->created_by_id = \Auth::user()->id;
                $oNote->updated_by_id = \Auth::user()->id;

                $oNote->save();
              }

              $oFormula->duration = $dDuration;
              $oFormula->save();
          });

          Flash::success(trans('messages.REG_CREATED'))->important();

          return redirect()->route('mms.formulas.index');
        }
        catch (\Exception $e) {
          dd($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $oFormula = SFormula::find($id);
        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oFormula);

        /*
          This method tries to get the lock, if not is obtained returns an array of errors
         */
        $error = session('utils')->validateLock($oFormula);
        if (sizeof($error) > 0)
        {
          return redirect()->back()->withErrors($error);
        }

        $lProducts = $this->getProducts();

        $lMaterials = $this->getMaterialsBaseQuery();

        $lMaterialsList = clone $lMaterials;

        $lMaterials = $lMaterials->select('ei.id_item', 'ei.code', 'ei.name',
                                          'ei.unit_id', 'eu.code as unit_code',
                                          'eu.name as unit_name',
                                          'eit.name as item_type',
                                          'eit.id_item_type')
                                  ->get();

        $lMaterialsList = $lMaterialsList->select(\DB::raw("CONCAT(ei.code, ' - ', ei.name) as item"),
                                                  'id_item')
                                          ->lists('item', 'id_item');

        $lUnits = SUnit::orderBy('id_unit', 'ASC')->get();
        $lBranches = SBranch::where('partner_id', session('partner')->id_partner)
                              ->lists('name', 'id_branch');

        $oFormula->rows;
        $oFormula->notes;

        foreach ($oFormula->rows as $row) {
            $row->substitute;
        }

        return view('mms.formulas.createEdit')
                    ->with('oFormula', $oFormula)
                    ->with('lUnits', $lUnits)
                    ->with('branches', $lBranches)
                    ->with('lMaterials', $lMaterials)
                    ->with('lMaterialsList', $lMaterialsList)
                    ->with('title', trans('userinterface.titles.EDIT_FORMULA'))
                    ->with('products', $lProducts);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SFormulaRequest $request, $id)
    {
        $oFormula = SFormula::find($id);
        $oFormula->fill($request->all());
        $oFormula->updated_by_id = \Auth::user()->id;

        $table = json_decode($request->input('formula_object'));
        $rows = $table->{'lFormulaRows'};
        $notes = $table->{'lNotes'};

        $aFormulaRows = array();
        $dDuration = 0;
        foreach ($rows as $row) {
          $oIngredient = SFormulaRow::find($row->{'iIdFormulaRow'});

          $oIngredient->dt_start = $row->{'tStart'};
          $oIngredient->dt_end = $row->{'tEnd'};
          $oIngredient->quantity = $row->{'dQuantity'};
          $oIngredient->cost = $row->{'dCost'};
          $oIngredient->duration = $row->{'dDuration'};
          $oIngredient->is_deleted = $row->{'bIsDeleted'};

          if (! $oIngredient->is_deleted) {
              $dDuration += $row->{'dDuration'};
          }

          $oIngredient->updated_by_id = \Auth::user()->id;

          array_push($aFormulaRows, $oIngredient);
        }

        $aFormulaNotes = array();
        foreach ($notes as $note) {
          if ($note->{'iIdNote'} != '0') {
            $oNote = SFormulaNote::find($note->{'iIdNote'});
            $oNote->note = $note->{'sNote'};
            $oNote->is_deleted = $note->{'bIsDeleted'};
          }
          else {
            $oNote = new SFormulaNote();
            $oNote->note = $note->{'sNote'};
            $oNote->is_deleted = $note->{'bIsDeleted'};
            $oNote->created_by_id = \Auth::user()->id;
          }

          $oNote->updated_by_id = \Auth::user()->id;

          array_push($aFormulaNotes, $oNote);
        }

        try {

          \DB::connection('company')->transaction(function() use ($oFormula,
                                                                  $aFormulaRows,
                                                                  $aFormulaNotes) {
              $errors = $oFormula->save();
              if (sizeof($errors) > 0)
              {
                 return redirect()->route('mms.formulas.index')->withErrors($errors);
              }

              $oFormula->rows()->saveMany($aFormulaRows);
              $oFormula->notes()->saveMany($aFormulaNotes);
          });

          Flash::warning(trans('messages.REG_EDITED'))->important();

          return redirect()->route('mms.formulas.index');
        }
        catch (\Exception $e) {
          dd($e);
        }
    }

    public function getItemFormulas(Request $request)
    {
      try {
        $oItem = SItem::find($request->id);
        $oResponse = $oItem->formulas;

        return response()->json($oResponse);
      }
      catch (\Exception $e) {
        return response()->json($e);
      }
      catch (Error $error) {
        return response()->json($error);
      }
    }
}