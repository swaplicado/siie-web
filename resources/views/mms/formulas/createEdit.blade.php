@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', $title)
@section('titlepanel', $title)

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($oFormula->id_formula))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'mms.formulas.store';
        $method = 'POST';
        $oSend = $sRoute;
			}
			else
			{
				$sRoute = 'mms.formulas.update';
        $method = 'PUT';
        $oSend = [$sRoute, $oFormula];
			}
			$aux = $oFormula;
	?>
@else
	<?php
		$sRoute='mms.formulas.store';
    $method = 'POST';
    $oSend = $sRoute;
	?>
	@section('title', trans('userinterface.titles.CREATE_FORMULA'))
@endif
	<?php $sRoute2 = 'mms.formulas.index' ?>

@section('content')
  @include('mms.formulas.ingredient')
  {{-- @include('mms.formulas.notes') --}}
  {!! Form::open(['route' => $oSend, 'method' => $method]) !!}
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-5">
          <div class="form-group">
            {!! Form::label('product', trans('wms.labels.MAT_PROD').'*'	) !!}
            <div class="tps">
              {!! Form::select('product', $products, isset($oFormula->item_id) ?
                                                      $oFormula->item_id.'-'.$oFormula->unit_id : null,
                        ['class'=>'form-control select-one', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD'),
                         'onChange' => "setFormulaData(this)", (isset($oFormula->item_id) ? 'disabled' : '')]) !!}
            </div>
            {!! Form::hidden('item_id', isset($oFormula->item_id) ?
                                        $oFormula->item_id : -1, ['id' => 'item_id']) !!}
            {!! Form::hidden('unit_id',  isset($oFormula->unit_id) ?
                                        $oFormula->unit_id : -1, ['id' => 'unit_id']) !!}
            {!! Form::hidden('recipe', isset($oFormula->recipe) ?
                                        $oFormula->recipe : -1, ['id' => 'recipe']) !!}
            {!! Form::hidden('formula_object', -1, ['id' => 'formula_object']) !!}
          </div>
        </div>
        <div class="col-md-1">
          <div class="form-group">
            {!! Form::label('un', trans('wms.labels.UNIT')	) !!}
            {!! Form::label('unit', isset($oFormula->unit_id) ?
                                        $oFormula->unit->code : '-', ['id' => 'unit', 'class' => 'form-control']) !!}
          </div>
        </div>
        <div class="col-md-5">

          <div class="form-group">
            {!! Form::label('identifier', trans('mms.labels.NAME_FORMULA').'*') !!}
            {!! Form::text('identifier',
              isset($oFormula->identifier) ?  $oFormula->identifier : '', ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
                                                          'placeholder' => trans('mms.placeholders.NAME_FORMULA'), 'required']) !!}
          </div>
        </div>
        <div class="col-md-1">

          <div class="form-group">
            {!! Form::label('version', trans('mms.labels.VERSION')) !!}
            {!! Form::number('version',
                            isset($oFormula->version) ?  $oFormula->version : 1,
                                          ['class'=>'form-control', 'readonly']) !!}
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('dt_date', trans('userinterface.labels.DATE').'*') !!}
            {!! Form::date('dt_date',
                isset($oFormula->dt_date) ? $oFormula->dt_date : session('work_date'),
                                                      ['class'=>'form-control input-sm',
                                                      'id' => 'dt_date']) !!}
          </div>
        </div>
        <div class="col-md-6 col-md-offset-3">
          <div class="form-group">
            {!! Form::label('notes', trans('userinterface.labels.NOTES').'*') !!}
            {!! Form::textarea('notes',
                              isset($oFormula->notes) ? $oFormula->notes : '', ['size' => '30x5',
                              'maxlength' => '250',
                              'class'=>'form-control input-sm',
                              'id' => 'notes']) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
        <br />
        <br />
        <button type="button" id='btnAdd' class="btn btn-success"
                    data-toggle="modal" {{ isset($oFormula->id_formula) ? '' : 'disabled' }}
                    onclick="cleanModal()"
                    data-target="#modalIngredient">Agregar ingrediente</button>
        <button type="button" id='btnEdit' class="btn btn-info"
                    data-toggle="modal"
                    onclick="setIngredient()"
                    data-target="#modalIngredient">Modificar ingrediente</button>
        <button type="button" id='btnDel' class="btn btn-danger">Borrar ingrediente</button>
        {{-- <button type="button" id='btnNote' data-toggle="modal"
                class="btn btn-primary"
                data-target="#modalNote">Notas</button> --}}

    </div>
  </div>
	<div class="row">
    <br />
		<table id="ingredients_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
				<thead>
						<tr class="titlerow">
								<th>idRow</th>
								<th>idFormulaRow</th>
								<th data-priority="1" style="text-align: center;">Clave</th>
								<th data-priority="1">Material/Producto</th>
								<th data-priority="1" style="text-align: center;">Cantidad</th>
								<th data-priority="1" style="text-align: center;">Un.</th>
                <th data-priority="1" style="text-align: center;">Masa</th>
								<th data-priority="1" style="text-align: center;">%</th>
                <th data-priority="1">Tipo</th>
						</tr>
				</thead>
				<tbody>
          <?php
            $i = 0;
            $dSum = 0;
          ?>
					@foreach ($oFormula->rows as $ingredient)
            @if (! $ingredient->is_deleted)
  						<tr>
  								<td>{{ $i++ }}</td>
  								<td>{{ $ingredient->id_formula_row }}</td>
  								<td>{{ $ingredient->item->code }}</td>
  								<td>{{ $ingredient->item->name }}</td>
                  <td align="right">{{ session('utils')->formatNumber($ingredient->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
                  <td>{{ $ingredient->unit->code }}</td>
                  <td align="right">{{ session('utils')->formatNumber($ingredient->item->mass, \Config::get('scsiie.FRMT.QTY')) }}</td>
                  <td align="right">{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
                  <td>{{ $ingredient->item->gender->type->name }}</td>
  						</tr>
              {!! $dSum += $ingredient->item->mass !!}
            @endif
					@endforeach
				</tbody>
        <tfoot align="right">
          <tr>
            <th></th>
            <th></th>
            <th>{{ 'TOTALES' }}</th>
            <th></th>
            <th></th>
            <th></th>
            <th align="right">{{ session('utils')->formatNumber($dSum, \Config::get('scsiie.FRMT.QTY')) }}</th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
		</table>
	</div>
  <br />
  <div class="form-group" align="right">
		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
		<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}"
            class="btn btn-danger" onClick="location.href='{{ route('mms.formulas.index') }}'"/>
	</div>
{!! Form::close() !!}
@endsection

@section('js')
		<script src="{{ asset('js/formulas/table.js')}}"></script>
		<script src="{{ asset('js/formulas/core.js')}}"></script>
		<script src="{{ asset('js/formulas/notes.js')}}"></script>
		<script src="{{ asset('js/formulas/validation.js')}}"></script>
		<script src="{{ asset('js/formulas/SIngredientCore.js')}}"></script>
		<script>
			function Data() {
        this.oFormula = <?php echo json_encode($oFormula); ?>;
        this.lIngredients = <?php echo json_encode($lRows); ?>;
        this.lUnits = <?php echo json_encode($lUnits); ?>;
        this.lMaterials = <?php echo json_encode($lMaterials); ?>;
        this.lMaterialsList = <?php echo json_encode($lMaterialsList); ?>;
        this.lItemTypes = <?php echo json_encode(\Config::get('scsiie.ITEM_TYPE')) ?>;
        this.scsiie = <?php echo json_encode(\Config::get('scsiie')) ?>;
        var qty = <?php echo json_encode(session('decimals_qty')) ?>;
        var amt = <?php echo json_encode(session('decimals_amt')) ?>;
        this.DEC_QTY = parseInt(qty);
        this.DEC_AMT = parseInt(amt);

        this.jsFormula = new FormulaJs();
        this.jsFormula.iRecipe = this.oFormula.recipe;
			}

			var oData = new Data();

      if (oData.oFormula.id_formula != undefined && oData.oFormula.id_formula != 0) {

          setData(oData.oFormula.item_id, oData.oFormula.unit_id, oData.oFormula.identifier);

          oData.oFormula.rows.forEach(function(oFormulaRow) {
            if (! oFormulaRow.is_deleted) {
              var oRow = new Ingredient();

              oRow.iIdFormulaRow = oFormulaRow.id_formula_row;
              oRow.iIdItem = oFormulaRow.item_id;
              oRow.iIdUnit = oFormulaRow.unit_id;
              oRow.iIdItemRecipe = oFormulaRow.item_recipe_id;
              // oRow.tStart = oFormulaRow.dt_start;
              // oRow.tEnd = oFormulaRow.dt_end;
              oRow.dQuantity = oFormulaRow.quantity;
              // oRow.dCost = oFormulaRow.cost;
              // oRow.dDuration = oFormulaRow.duration;

              // if (oFormulaRow.substitute != null) {
              //   oRow.iIdItemSubstitute = oFormulaRow.substitute.item_id;
              //   oRow.iIdUnitSubstitute = oFormulaRow.substitute.unit_id;
              //   oRow.iIdItemRecipeSubs = oFormulaRow.substitute.item_recipe_id;
              //   oRow.dSuggested = oFormulaRow.substitute.percentage;
              //   oRow.dMax = oFormulaRow.substitute.percentage_max;
              // }

              oRow.bIsDeleted = oFormulaRow.is_deleted;
              oRow.iFormulaId = oFormulaRow.formula_id;

              oData.jsFormula.addRow(oRow);
            }
          });

          // oData.oFormula.notes.forEach(function(oFormulaNote) {
          //   if (! oFormulaNote.is_deleted) {
          //     var oNote = new Note();
          //
          //     oNote.iIdNote = oFormulaNote.id_note;
          //     oNote.sNote = oFormulaNote.note;
          //     oNote.bIsDeleted = oFormulaNote.is_deleted;
          //     oNote.iFormulaId = oFormulaNote.formula_id;
          //
          //     oData.jsFormula.addNote(oNote);
          //   }
          // });

          document.getElementById('formula_object').value = JSON.stringify(oData.jsFormula);
      }
      else {
        if (oData.lIngredients.length > 0) {
           oIngredientCore.loadIngredients(oData.lIngredients);
        }
      }

		</script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
