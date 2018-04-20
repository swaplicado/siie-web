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
  @include('mms.formulas.notes')
  {!! Form::open(['route' => $oSend, 'method' => $method]) !!}
    <div class="row">
      <div class="col-md-6">
    			<div class="form-group">
    				{!! Form::label('branch_id', trans('userinterface.labels.BRANCH').'*') !!}
            {!! Form::select('branch_id', $branches, isset($oFormula->branch_id) ?  $oFormula->branch_id : null,
                      ['class'=>'form-control select-one', 'placeholder' => trans('userinterface.placeholders.SELECT_BRANCH'),
                       'onChange' => "", (isset($oFormula->id_formula) ? 'disabled' : '')]) !!}
    			</div>

    			<div class="form-group">
    				{!! Form::label('dt_start', trans('userinterface.labels.DATE_START').'*') !!}
    				{!! Form::date('dt_start', isset($oFormula->dt_start) ? $oFormula->dt_start : session('work_date'),
                            ['class'=>'form-control']) !!}
    			</div>

    			<div class="form-group">
    				{!! Form::label('dt_end', trans('userinterface.labels.DATE_END').'*') !!}
    				{!! Form::date('dt_end', isset($oFormula->dt_end) ? $oFormula->dt_end : session('work_date'),
                            ['class'=>'form-control']) !!}
    			</div>

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
          <button type="button" id='btnNote' data-toggle="modal"
                  class="btn btn-primary"
                  data-target="#modalNote">Notas</button>

      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
      				{!! Form::label('product', trans('wms.labels.MAT_PROD').'*'	) !!}
      				<div class="tps">
      					{!! Form::select('product', $products, isset($oFormula->item_id) ?
                                                        $oFormula->item_id.'-'.$oFormula->unit_id : null,
      										['class'=>'form-control select-one', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD'),
                           'onChange' => "setFormulaData(this)", (isset($oFormula->id_formula) ? 'disabled' : '')]) !!}
      				</div>
              {!! Form::hidden('item_id', -1, ['id' => 'item_id']) !!}
              {!! Form::hidden('unit_id', -1, ['id' => 'unit_id']) !!}
      			</div>

            <div class="form-group">
      				{!! Form::label('name', trans('mms.labels.NAME_FORMULA').'*') !!}
      				{!! Form::text('name',
      					isset($oFormula->id_formula) ?  $oFormula->name : null, ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
      					 																						'placeholder' => trans('mms.placeholders.NAME_FORMULA'), 'required']) !!}
      			</div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8 col-md-offset-4">
      			<div class="form-group">
      				{!! Form::label('quantity', trans('userinterface.labels.QUANTITY').'*') !!}
              <div class="row">
                <div class="col-md-8">
                  {!! Form::number('quantity', isset($oFormula) ?  $oFormula->quantity : 1,
                                                        ['class'=>'form-control', 'id' => 'quantity',
                                                        'style' => 'text-align:right;',
                                                        'step' => '0.01',
                                                        'placeholder' => trans('userinterface.placeholders.QUANTITY'),
                                                        'required']) !!}
                </div>
                <div class="col-md-4">
                  {!! Form::label('unit', '-', ['id' => 'unit', 'class' => 'form-control']) !!}
                </div>
              </div>
      			</div>
      			<div class="form-group">
      				{!! Form::label('cost', trans('userinterface.labels.COST').' $ *') !!}
              <div class="row">
                <div class="col-md-8">
      				        {!! Form::number('cost',  isset($oFormula) ?  $oFormula->cost : 1,
                                                    ['class'=>'form-control', 'id' => 'cost',
                                                    'style' => 'text-align:right;',
      																							'placeholder' => trans('userinterface.placeholders.COST'),
                                                    'step' => '0.01', 'required']) !!}
                      {!! Form::hidden('formula_object', null, ['id' => 'formula_object']) !!}
                </div>
              </div>
      			</div>
          </div>
        </div>
      </div>
    </div>
  	<div class="row">
      <br />
  		<table id="formulas_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
  				<thead>
  						<tr class="titlerow">
  								<th>idRow</th>
  								<th>idFormulaRow</th>
  								<th data-priority="1">Cve</th>
  								<th data-priority="1">Mat/Prod</th>
  								<th data-priority="1">Cant.</th>
  								<th data-priority="1">Un.</th>
  								<th>%</th>
                  <th>Costo</th>
  								<th>Inicio</th>
  								<th>Fin</th>
  						</tr>
  				</thead>
  				<tbody>
            <?php
              $i = 0;
            ?>
  					@foreach ($oFormula->rows as $ingredient)
              @if (! $ingredient->is_deleted)
    						<tr>
    								<td>{{ $i++ }}</td>
    								<td>{{ $ingredient->id_formula_row }}</td>
    								<td>{{ $ingredient->item->code }}</td>
    								<td>{{ $ingredient->item->name }}</td>
                    <td align="right">{{ $ingredient->quantity }}</td>
                    <td>{{ $ingredient->unit->code }}</td>
                    <td align="right">{{ 0 }}</td>
                    <td align="right">{{ $ingredient->cost }}</td>
    								<td>{{ \Carbon\Carbon::parse($ingredient->dt_start)->format('d-m-Y') }}</td>
    								<td>{{ \Carbon\Carbon::parse($ingredient->dt_end)->format('d-m-Y') }}</td>
    						</tr>
              @endif
  					@endforeach
  				</tbody>
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
		<script>
			function Data() {
        this.oFormula = <?php echo json_encode($oFormula); ?>;
        this.lUnits = <?php echo json_encode($lUnits); ?>;
        this.lMaterials = <?php echo json_encode($lMaterials); ?>;
        this.lMaterialsList = <?php echo json_encode($lMaterialsList); ?>;
        this.lItemTypes = <?php echo json_encode(\Config::get('scsiie.ITEM_TYPE')) ?>

        this.jsFormula = new FormulaJs();
			}

			var oData = new Data();

      if (oData.oFormula.id_formula != undefined) {

          setData(oData.oFormula.item_id, oData.oFormula.unit_id, oData.oFormula.name);

          oData.oFormula.rows.forEach(function(oFormulaRow) {
            if (! oFormulaRow.is_deleted) {
              var oRow = new Ingredient();

              oRow.iIdFormulaRow = oFormulaRow.id_formula_row;
              oRow.iIdItem = oFormulaRow.item_id;
              oRow.iIdUnit = oFormulaRow.unit_id;
              oRow.iIdItemFormula = oFormulaRow.item_formula_id;
              oRow.tStart = oFormulaRow.dt_start;
              oRow.tEnd = oFormulaRow.dt_end;
              oRow.dQuantity = oFormulaRow.quantity;
              oRow.dCost = oFormulaRow.cost;
              oRow.dDuration = oFormulaRow.duration;

              if (oFormulaRow.substitute != null) {
                oRow.iIdItemSubstitute = oFormulaRow.substitute.item_id;
                oRow.iIdUnitSubstitute = oFormulaRow.substitute.unit_id;
                oRow.iIdItemFormulaSubs = oFormulaRow.substitute.item_formula_id;
                oRow.dSuggested = oFormulaRow.substitute.percentage;
                oRow.dMax = oFormulaRow.substitute.percentage_max;
              }

              oRow.bIsDeleted = oFormulaRow.is_deleted;
              oRow.iFormulaId = oFormulaRow.formula_id;

              oData.jsFormula.addRow(oRow);
            }
          });

          oData.oFormula.notes.forEach(function(oFormulaNote) {
            if (! oFormulaNote.is_deleted) {
              var oNote = new Note();

              oNote.iIdNote = oFormulaNote.id_note;
              oNote.sNote = oFormulaNote.note;
              oNote.bIsDeleted = oFormulaNote.is_deleted;
              oNote.iFormulaId = oFormulaNote.formula_id;

              oData.jsFormula.addNote(oNote);
            }
          });

          document.getElementById('formula_object').value = JSON.stringify(oData.jsFormula);
      }

		</script>
@endsection
