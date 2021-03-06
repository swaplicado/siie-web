@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($lots))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'wms.lots.store';
			}
			else
			{
				$sRoute = 'wms.lots.update';
			}
			$aux = $lots;
	?>
	@section('title', trans('userinterface.titles.EDIT_LOTS'))
@else
	<?php
		$sRoute='wms.lots.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_LOTS'))
@endif
	<?php $sRoute2 = 'wms.lots.index' ?>

@section('content')

			<div class="form-group">

				<div class="col-md-12">

					<div class="form-group row">

						{!! Form::label('name', trans('userinterface.labels.NAME').'*',
										['class'=>'col-md-1 control-label']) !!}

						<div class="col-md-4">

							{!! Form::text('lot',
					    	isset($lots) ? $lots->lot : null , ['required','class'=>'form-control',
									'maxlength' => '50', 'placeholder' => trans('userinterface.placeholders.NAME'),
									'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
									'required']) !!}

						</div>

						{!! Form::label('dt_expiry', trans('userinterface.labels.EXPIRY').'*',
															['class'=>'col-md-2 control-label']) !!}

						<div class="col-md-4">

						 {!! Form::text('dt_expiry',
						     isset($lots) ? $lots->dt_expiry : null , ['class'=>'form-control datepicker',
								 'placeholder' => trans('userinterface.placeholders.EXPERY'),
								 'required']) !!}

					 </div>

					</div>

				</div>

			</div>

			<div class="form-group">

				<div class="col-md-12">

					<div class="form-group row">

						{!! Form::label('item_id', trans('userinterface.labels.ITEM').'*',
									['class'=>'col-md-1 control-label']) !!}

						<div class="col-md-8">

							{!! Form::select('item_id', $items, isset($lots) ? $lots->item->id_item : null ,
											['class'=>'form-control select-item',
											isset($lots) ? 'disabled' : '',
											'placeholder' => trans('userinterface.placeholders.SELECT_ITEM'),
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

	</script>

	<script>
	$('.datepicker').datepicker({
			format: "yyyy/mm/dd",
			language: "es",
			autoclose: true
	});
	</script>

	@endsection
