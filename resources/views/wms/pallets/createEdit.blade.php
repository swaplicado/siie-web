@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($pallets))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'wms.pallets.store';
			}
			else
			{
				$sRoute = 'wms.pallets.update';
			}
			$aux = $pallets;
	?>
	@section('title', 'Modificar Tarima')
@else
	<?php
		$sRoute='wms.pallets.store';
	?>
	@section('title', 'Crear Tarima')
@endif
	<?php $sRoute2 = 'wms.pallets.index' ?>

@section('content')

			<div class="form-group">

				<div class="col-md-12">

					<div class="form-group row">

						{{-- {!! Form::label('pallet', 'Tarima'.'*',['class'=>'col-md-1 control-label']) !!}

						<div class="col-md-3">

							{!! Form::text('pallet',
					    	isset($pallets) ? $pallets->pallet : null , ['required', 'maxlength' => '50', 'class'=>'form-control', 'placeholder' => 'Tarima...']) !!}

						</div> --}}
						@if(isset($pallets))
							{!! Form::hidden('quantity','1') !!}
						@else
							{!! Form::label('quantity', 'Cantidad'.'*',['class'=>'col-md-1 control-label']) !!}
							<div class="col-md-1">


								{!! Form::number('quantity', 1, ['class'=>'form-control','placeholder'=> '0'])!!}

							</div>
						@endif

            {!! Form::label('item_id', trans('userinterface.labels.ITEM').'*',['class'=>'col-md-1 control-label']) !!}

						<div class="col-md-6">

							{!! Form::select('item_id', $items, isset($pallets) ? $pallets->item->id_item : null ,
																	['class'=>'form-control select-item',
																	'placeholder' => trans('userinterface.placeholders.SELECT_ITEM'),
																	isset($pallets) ? 'disabled' : '',
																	'required']) !!}

						</div>

					</div>

				</div>

			</div>

@endsection

@section('js')
	<script type="text/javascript">
		$('.select-item').chosen({
			placeholder_select_single: 'Seleccione un item...'
		});
		$('.select-unit').chosen({
			placeholder_select_single: 'Seleccione una unidad...'
		});
		$('.select-loc').chosen({
			placeholder_select_single: 'Seleccione localizacion...'
		});

	</script>

	<script>
	$('.datepicker').datepicker({
			format: "yyyy/mm/dd",
			language: "es",
			autoclose: true
	});
	</script>

	@endsection
