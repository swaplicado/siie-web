<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\MMS\SFormulaRequest;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;
use App\SUtils\SMenu;
use App\Database\Config;
use App\SUtils\SUtil;
use App\SCore\SFormulasProcess;

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
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.MMS_FORMULAS'), \Config::get('scsys.MODULES.MMS'));

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

        $sSelect = '
                      id_formula,
                      version,
                      dt_date,
                      identifier,
                      mf.is_deleted,
                      ei.code AS item_code,
                      ei.name AS item,
                      eu.code AS unit_code,
                      eu.name AS unit,
                      mf.created_by_id,
                      mf.updated_by_id,
                      mf.created_at,
                      mf.updated_at,
                      uc.username AS creation_user_name,
                      uu.username AS mod_user_name
                    ';

        $Formulas = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('mms_formulas as mf')
                     ->join('erpu_items as ei', 'mf.item_id', '=', 'ei.id_item')
                     ->join('erpu_units as eu', 'mf.unit_id', '=', 'eu.id_unit')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'mf.created_by_id', '=', 'uc.id')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'mf.updated_by_id', '=', 'uu.id');

       switch ($this->iFilter) {
         case \Config::get('scsys.FILTER.ACTIVES'):
             $Formulas = $Formulas->where('mf.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
           break;

         case \Config::get('scsys.FILTER.DELETED'):
             $Formulas = $Formulas->where('mf.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
           break;

         default:
       }

       $Formulas = $Formulas->select(\DB::raw($sSelect))
                     ->where('identifier', 'LIKE', "%".$request->name."%")
                     ->get();

        return view('mms.formulas.index')
            ->with('formulas', $Formulas)
            ->with('actualUserPermission', $this->oCurrentUserPermission)
            ->with('iFilter', $this->iFilter);
    }

    /**
     * return the view of detail of formulas
     *
     * @param  Request $request
     *
     * @return view   mms.formulas.detail
     */
    public function getDetail(Request $request)
    {
        $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

        $sSelect = '
                      id_formula,
                      identifier,
                      version,
                      dt_date,
                      ei.code AS item_code,
                      ei.name AS item,
                      eu.code AS unit_code,
                      ei_row.code AS item_code_row,
                      ei_row.name AS item_row,
                      mfr.quantity,
                      eu_row.code AS unit_code_row,
                      mf.is_deleted,
                      eu.name AS unit,
                      mf.created_by_id,
                      mf.updated_by_id
                    ';

        $Formulas = \DB::connection(session('db_configuration')->getConnCompany())
                     ->table('mms_formulas as mf')
                     ->join('mms_formula_rows as mfr', 'mfr.formula_id', '=', 'mf.id_formula')
                     ->join('erpu_items as ei', 'mf.item_id', '=', 'ei.id_item')
                     ->join('erpu_units as eu', 'mf.unit_id', '=', 'eu.id_unit')
                     ->join('erpu_items as ei_row', 'mfr.item_id', '=', 'ei_row.id_item')
                     ->join('erpu_units as eu_row', 'mfr.unit_id', '=', 'eu_row.id_unit')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uc', 'mf.created_by_id', '=', 'uc.id')
                     ->join(\DB::connection(Config::getConnSys())->getDatabaseName().'.users as uu', 'mf.updated_by_id', '=', 'uu.id');

         switch ($this->iFilter) {
           case \Config::get('scsys.FILTER.ACTIVES'):
               $Formulas = $Formulas->where('mf.is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'));
             break;

           case \Config::get('scsys.FILTER.DELETED'):
               $Formulas = $Formulas->where('mf.is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'));
             break;

           default:
         }

         if ($request->name != null) {
            $Formulas = $Formulas->where(function ($query) use ($request) {
                                      $query->where('identifier', 'like', '%'.$request->name.'%')
                                            ->orWhere('ei.code', 'like', '%'.$request->name.'%')
                                            ->orWhere('ei.name', 'like', '%'.$request->name.'%')
                                            ->orWhere('ei_row.code', 'like', '%'.$request->name.'%')
                                            ->orWhere('ei_row.name', 'like', '%'.$request->name.'%');
                                  });
         }

         $Formulas = $Formulas->select(\DB::raw($sSelect))
                                ->where('mfr.is_deleted', false)
                                ->orderBy('item_code_row', 'ASC')
                                ->groupBy('mfr.id_formula_row');

         $Formulas = $Formulas->paginate(50);

         return view('mms.formulas.detail')
                 ->with('formulas', $Formulas)
                 ->with('actualUserPermission', $this->oCurrentUserPermission)
                 ->with('iFilter', $this->iFilter);
    }

    private function getProducts($value='')
    {
        $lProducts = SItem::select(\DB::raw("CONCAT(erpu_items.code, ' - ', erpu_items.name, '-', eu.code) as item"),
                                    \DB::raw("CONCAT(erpu_items.id_item, '-', erpu_items.unit_id) as id_id"))
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
                      // ->where(function ($q) {
                      //       $q->where('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_MATERIAL_MATERIAL'))
                      //       ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.DIRECT_PACKING_MATERIAL'))
                      //       ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.BASE_PRODUCT'))
                      //       ->orWhere('eig.item_type_id', \Config::get('scsiie.ITEM_TYPE.INDIRECT_MATERIAL'));
                      //   })
                      ->where(function ($q) {
                        $q->where('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.MATERIAL'))
                        ->orWhere('eig.item_class_id', \Config::get('scsiie.ITEM_CLS.PRODUCT'));
                      })
                      ->where('eig.item_class_id', '!=',\Config::get('scsiie.ITEM_CLS.SPENDING'))
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
    public function create($iFormula = 0)
    {
        if (! SValidation::canCreate($this->oCurrentUserPermission->privilege_id)) {
          return redirect()->route('notauthorized');
        }

        $lProducts = $this->getProducts();

        $lMaterials = $this->getMaterialsBaseQuery();

        $lMaterialsList = clone $lMaterials;

        $lMaterials = $lMaterials->select('ei.id_item', 'ei.code', 'ei.name',
                                          'ei.unit_id', 'eu.code as unit_code',
                                          'ei.is_bulk',
                                          'ei.mass',
                                          'eu.name as unit_name',
                                          'eit.name as item_type',
                                          'eit.item_class_id as item_class',
                                          'eit.id_item_type')
                                  ->get();

        $lMaterialsList = $lMaterialsList->select(\DB::raw("CONCAT(ei.code, ' - ', ei.name, '-', eu.code) as item"),
                                                  'id_item')
                                          ->lists('item', 'id_item');

        $lUnits = SUnit::orderBy('id_unit', 'ASC')->get();

        $oFormula = new SFormula();
        $lRows = [];
        $dTotalMass = 0;

        if ($iFormula != 0) {
           $oBaseFormula = SFormula::find($iFormula);
           $lRows = $oBaseFormula->rows;
           foreach ($lRows as $row) {
              $row->sItemRecipe = $row->getLastVersion()->identifier;
              $row->item->gender;
              $dTotalMass += $row->mass;
           }

           $oFormula->identifier = $oBaseFormula->identifier;
           $oFormula->item_id = $oBaseFormula->item_id;
           $oFormula->unit_id = $oBaseFormula->unit_id;
           $oFormula->recipe = $oBaseFormula->recipe;

           $iVersion = \DB::connection(session('db_configuration')->getConnCompany())
                         ->table('mms_formulas as mf')
                         ->where('item_id', $oBaseFormula->item_id)
                         ->where('unit_id', $oBaseFormula->unit_id)
                         ->where('recipe', $oBaseFormula->recipe)
                         ->select(\DB::raw("MAX(version) as max_version"))
                         ->take(1)
                         ->get();

           $oFormula->version = $iVersion[0]->max_version + 1;
        }

        // $oFormula->rows;
        // $oFormula->notes;

        return view('mms.formulas.createEdit')
                      ->with('oFormula', $oFormula)
                      ->with('lRows', $lRows)
                      ->with('dTotalMass', $dTotalMass)
                      ->with('lUnits', $lUnits)
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
        // $notes = $table->{'lNotes'};

        $oFormula = new SFormula($request->all());

        if ($oFormula->recipe > 0) {
          $iVersion = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('mms_formulas as mf')
                        ->where('item_id', $oFormula->item_id)
                        ->where('unit_id', $oFormula->unit_id)
                        ->where('recipe', $oFormula->recipe)
                        ->select(\DB::raw("MAX(version) as max_version"))
                        ->take(1)
                        ->get();

          $oFormula->version = $iVersion[0]->max_version + 1;
        }
        else {
          $iRecipe = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('mms_formulas as mf')
                        ->select(\DB::raw("MAX(recipe) as max_recipe"))
                        ->take(1)
                        ->get();
          $oFormula->recipe = $iRecipe[0]->max_recipe + 1;
        }

        // $iFolio = \DB::connection(session('db_configuration')->getConnCompany())
        //               ->table('mms_formulas as mf')
        //               ->select(\DB::raw("MAX(folio) as max_folio"))
        //               ->take(1)
        //               ->get();

        // $oFormula->folio = session('utils')->formatFolio(($iFolio[0]->max_folio + 1));

        $oFormula->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oFormula->created_by_id = \Auth::user()->id;
        $oFormula->updated_by_id = \Auth::user()->id;

        try {

          \DB::connection('company')->transaction(function() use ($oFormula, $rows) {
              $lFormulaRows = array();
              // $dDuration = 0;
              foreach ($rows as $row) {
                $oIngredient = new SFormulaRow();

                $oIngredient->id_formula_row = $row->{'iIdFormulaRow'};
                // $oIngredient->dt_start = $row->{'tStart'};
                // $oIngredient->dt_end = $row->{'tEnd'};
                $oIngredient->quantity = $row->{'dQuantity'};
                $oIngredient->mass = $row->{'dMass'};
                // $oIngredient->cost = $row->{'dCost'};
                // $oIngredient->duration = $row->{'dDuration'};
                $oIngredient->is_deleted = $row->{'bIsDeleted'};

                // if (! $oIngredient->is_deleted) {
                //     $dDuration += $row->{'dDuration'};
                // }

                $oIngredient->item_id = $row->{'iIdItem'};
                $oIngredient->unit_id = $row->{'iIdUnit'};
                $oItemFormula = SFormula::find($row->{'iIdItemFormula'});
                $oIngredient->item_recipe_id = $oItemFormula == null ? 1 : $oItemFormula->recipe;
                $oIngredient->created_by_id = \Auth::user()->id;
                $oIngredient->updated_by_id = \Auth::user()->id;
                // $oIngredient->formula_id = $oFormula->id_formula;

                array_push($lFormulaRows, $oIngredient);

                // if ($row->{'iIdItemSubstitute'} != '0') {
                //     $oSubstitute = new SFormulaSubstitute();
                //
                //     $oSubstitute->item_id = $row->{'iIdItemSubstitute'};
                //     $oSubstitute->unit_id = $row->{'iIdUnitSubstitute'};
                //     $oSubstitute->percentage = $row->{'dSuggested'};
                //     $oSubstitute->percentage_max = $row->{'dMax'};
                //     $oSubstitute->formula_row_id = $oIngredient->id_formula_row;
                //     $oSubstitute->formula_id = $oFormula->id_formula;
                //     $oSubstitute->created_by_id = \Auth::user()->id;
                //     $oSubstitute->updated_by_id = \Auth::user()->id;
                //
                //     $oSubstitute->save();
                // }
              }


              // foreach ($notes as $note) {
              //   $oNote = new SFormulaNote();
              //   $oNote->note = $note->{'sNote'};
              //   $oNote->is_deleted = $note->{'bIsDeleted'};
              //   $oNote->formula_id = $oFormula->id_formula;
              //   $oNote->created_by_id = \Auth::user()->id;
              //   $oNote->updated_by_id = \Auth::user()->id;
              //
              //   $oNote->save();
              // }

              // $oFormula->duration = $dDuration;
              // $oFormula->save();

              $oResult = SFormulasProcess::validateRecursion($oFormula, array(), $lFormulaRows);
              if (is_array($oResult) && sizeof($oResult) > 0) {
                 return redirect()->back()->withErrors($oResult);
              }

              $oFormula->save();
              $oFormula->rows()->saveMany($lFormulaRows);
          });

          Flash::success(trans('messages.REG_CREATED'))->important();
        }
        catch (\Exception $e) {
          Flash::error($e)->important();
        }
        finally {
          return redirect()->route('mms.formulas.index');
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
                                          'ei.is_bulk',
                                          'ei.mass',
                                          'eu.name as unit_name',
                                          'eit.name as item_type',
                                          'eit.item_class_id as item_class',
                                          'eit.id_item_type')
                                  ->get();

        $lMaterialsList = $lMaterialsList->select(\DB::raw("CONCAT(ei.code, ' - ', ei.name, '-', eu.code) as item"),
                                                  'id_item')
                                          ->lists('item', 'id_item');

        $lUnits = SUnit::orderBy('id_unit', 'ASC')->get();

        $oFormula->rows;
        // $oFormula->notes;

        $dTotalMass = 0;
        foreach ($oFormula->rows as $row) {
            $row->item->itemType;
            if (!$row->is_deleted) {
              $dTotalMass += $row->mass;
            }
        }

        return view('mms.formulas.createEdit')
                    ->with('oFormula', $oFormula)
                    ->with('lRows', [])
                    ->with('dTotalMass', $dTotalMass)
                    ->with('lUnits', $lUnits)
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
        // $notes = $table->{'lNotes'};

        $aFormulaRows = array();
        $dDuration = 0;
        foreach ($rows as $row) {
          if ($row->iIdFormulaRow > 0) {
            $oIngredient = SFormulaRow::find($row->iIdFormulaRow);
          }
          else {
            $oIngredient = new SFormulaRow();
            $oIngredient->item_id = $row->iIdItem;
            $oIngredient->unit_id = $row->iIdUnit;
            $oIngredient->item_recipe_id = $row->iIdItemRecipe;
            $oIngredient->created_by_id = \Auth::user()->id;
          }

          // $oIngredient->dt_start = $row->{'tStart'};
          // $oIngredient->dt_end = $row->{'tEnd'};
          $oIngredient->quantity = $row->dQuantity;
          $oIngredient->mass = $row->dMass;
          // $oIngredient->cost = $row->{'dCost'};
          // $oIngredient->duration = $row->{'dDuration'};
          $oIngredient->is_deleted = $row->bIsDeleted;

          // if (! $oIngredient->is_deleted) {
          //     $dDuration += $row->{'dDuration'};
          // }

          $oIngredient->updated_by_id = \Auth::user()->id;

          array_push($aFormulaRows, $oIngredient);
        }

        $aFormulaNotes = array();
        // foreach ($notes as $note) {
        //   if ($note->{'iIdNote'} != '0') {
        //     $oNote = SFormulaNote::find($note->{'iIdNote'});
        //     $oNote->note = $note->{'sNote'};
        //     $oNote->is_deleted = $note->{'bIsDeleted'};
        //   }
        //   else {
        //     $oNote = new SFormulaNote();
        //     $oNote->note = $note->{'sNote'};
        //     $oNote->is_deleted = $note->{'bIsDeleted'};
        //     $oNote->created_by_id = \Auth::user()->id;
        //   }
        //
        //   $oNote->updated_by_id = \Auth::user()->id;
        //
        //   array_push($aFormulaNotes, $oNote);
        // }

        $oResult = SFormulasProcess::validateRecursion($oFormula, array(), $aFormulaRows);
        if (is_array($oResult) && sizeof($oResult) > 0) {
           return redirect()->back()->withErrors($oResult);
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
              // $oFormula->notes()->saveMany($aFormulaNotes);
          });

          Flash::success(trans('messages.REG_EDITED'))->important();

          return redirect()->route('mms.formulas.index');
        }
        catch (\Exception $e) {
          dd($e);
        }
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

        $oFormula = SFormula::find($id);
        $oFormula->is_deleted = \Config::get('scsys.STATUS.DEL');
        $oFormula->updated_by_id = \Auth::user()->id;

        $errors = $oFormula->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }
        #$user->delete();

        Flash::success(trans('messages.REG_DELETED'))->important();

        return redirect()->route('mms.formulas.index');
    }

    /**
     * activate the registry
     *
     * @param  Request $request
     * @param  integer  $id  id of Formula
     *
     * @return redirect 'mms.formulas.index'
     */
    public function activate(Request $request, $id)
    {
        $oFormula = SFormula::find($id);

        session('utils')->validateEdition($this->oCurrentUserPermission->privilege_id, $oFormula);

        $oFormula->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $oFormula->updated_by_id = \Auth::user()->id;

        $errors = $oFormula->save();
        if (sizeof($errors) > 0)
        {
           return redirect()->back()->withErrors($errors);
        }

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('mms.formulas.index');
    }

    /**
     * Copy the registry
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

        $lProducts = $this->getProducts();

        $lMaterials = $this->getMaterialsBaseQuery();

        $lMaterialsList = clone $lMaterials;

        $lMaterials = $lMaterials->select('ei.id_item', 'ei.code', 'ei.name',
                                          'ei.unit_id', 'eu.code as unit_code',
                                          'ei.is_bulk',
                                          'ei.mass',
                                          'eu.name as unit_name',
                                          'eit.name as item_type',
                                          'eit.item_class_id as item_class',
                                          'eit.id_item_type')
                                  ->get();

        $lMaterialsList = $lMaterialsList->select(\DB::raw("CONCAT(ei.code, ' - ', ei.name) as item"),
                                                  'id_item')
                                          ->lists('item', 'id_item');

        $lUnits = SUnit::orderBy('id_unit', 'ASC')->get();

        $oFormula = SFormula::find($id);

        $oFormulaCopy = clone $oFormula;
        $oFormulaCopy->id_formula = 0;
        $oFormulaCopy->recipe = -1;
        $oFormulaCopy->version = 1;
        $oFormulaCopy->identifier = '';
        $oFormulaCopy->quantity = 1;

        unset ($oFormulaCopy->item_id);
        unset ($oFormulaCopy->unit_id);

        $dTotalMass = '0';
        $lRows = $oFormula->rows;
        foreach ($lRows as $row) {
           $row->sItemRecipe = $row->getLastVersion()->identifier;
           $row->item->gender;
           $dTotalMass += $row->mass;
        }

        return view('mms.formulas.createEdit')->with('oFormula', $oFormulaCopy)
                                    ->with('lRows', $lRows)
                                    ->with('lUnits', $lUnits)
                                    ->with('dTotalMass', $dTotalMass)
                                    ->with('lMaterials', $lMaterials)
                                    ->with('lMaterialsList', $lMaterialsList)
                                    ->with('title', trans('userinterface.titles.CREATE_FORMULA'))
                                    ->with('products', $lProducts)
                                    ->with('bIsCopy', true);
    }

    public function print($id = 0)
    {
      $oFormula = SFormula::find($id);
      $oFormula->rows;

      $view = view('mms.formulas.print', ['oFormula' => $oFormula])->render();
      // set ukuran kertas dan orientasi
      $pdf = \PDF::loadHTML($view)->setPaper('letter', 'potrait')->setWarnings(false);
      // cetak
      return $pdf->stream();
    }

    public function getItemFormulas(Request $request)
    {
      try {
        $oItem = SItem::find($request->id);
        $oResponse = \DB::connection(session('db_configuration')->getConnCompany())
                      ->table('mms_formulas as mf')
                      ->where('item_id', $oItem->id_item)
                      ->where('unit_id', $oItem->unit_id)
                      ->whereRaw('version = (SELECT MAX(version)
                                    FROM mms_formulas
                                    WHERE recipe = mf.recipe)')
                      ->get();

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
