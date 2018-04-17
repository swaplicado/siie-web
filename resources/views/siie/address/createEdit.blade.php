@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($domicile))
	<?php
			$aux = $domicile;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.address.store';
			}
			else
			{
				$sRoute = 'siie.address.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_ADDRESS'))
@else
	<?php
		$sRoute='siie.address.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_ADDRESS'))
@endif
	<?php $sRoute2 = 'siie.address.index' ?>

@section('content')

		<div class="row">
			<div class="col-md-4">

				<div class="form-group">
					{!! Form::label('name', trans('userinterface.labels.ADDRESS').'*') !!}
					{!! Form::text('name',
						isset($domicile) ? $domicile->name : null , ['class'=>'form-control', 'maxlength' => '100',
																													'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																													'placeholder' => trans('userinterface.placeholders.ADDRESS'), 'required' => 'required']) !!}
				</div>

				<div class="form-group">
					{!! Form::label('street', trans('userinterface.labels.STREET').'*') !!}
					{!! Form::text('street',
						isset($domicile) ? $domicile->street : null , ['class'=>'form-control', 'maxlength' => '100', 'required',
																															'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																														'placeholder' => trans('userinterface.placeholders.STREET')]) !!}
				</div>

				<div class="form-group">
					{!! Form::label('num_ext', trans('userinterface.labels.NUM_EXT').'*') !!}
					{!! Form::text('num_ext',
						isset($domicile) ? $domicile->num_ext : null , ['class'=>'form-control', 'maxlength' => '50', 'placeholder' => trans('userinterface.placeholders.NUM_EXT'), 'required']) !!}
				</div>

				<div class="form-group">
					{!! Form::label('num_int', trans('userinterface.labels.NUM_INT')) !!}
					{!! Form::text('num_int',
						isset($domicile) ? $domicile->num_int : null , ['class'=>'form-control', 'maxlength' => '50', 'placeholder' => trans('userinterface.placeholders.NUM_INT')]) !!}
				</div>

			</div>
			<div class="col-md-4">

				<div class="form-group">
					{!! Form::label('neighborhood', trans('userinterface.labels.NEIGHBORHOOD')) !!}
					{!! Form::text('neighborhood',
						isset($domicile) ? $domicile->neighborhood : null , ['class'=>'form-control', 'maxlength' => '100',
																																	'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																																	'placeholder' => trans('userinterface.placeholders.NEIGHBORHOOD')]) !!}
				</div>

				<div class="form-group">
					{!! Form::label('reference', trans('userinterface.labels.REFERENCE')) !!}
					{!! Form::text('reference',
						isset($domicile) ? $domicile->reference : null , ['class'=>'form-control', 'maxlength' => '100',
																																'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																																'placeholder' => trans('userinterface.placeholders.REFERENCE')]) !!}
				</div>

				<div class="form-group">
					{!! Form::label('locality', trans('userinterface.labels.LOCALITY')) !!}
					{!! Form::text('locality',
						isset($domicile) ? $domicile->locality : null , ['class'=>'form-control', 'maxlength' => '100',
						 																									'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																															'placeholder' => trans('userinterface.placeholders.LOCALITY')]) !!}
				</div>

				<div class="form-group">
					{!! Form::label('county', trans('userinterface.labels.COUNTY')) !!}
					{!! Form::text('county',
						isset($domicile) ? $domicile->county : null , ['class'=>'form-control', 'maxlength' => '100',
																															'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																															'placeholder' => trans('userinterface.placeholders.COUNTY')]) !!}
				</div>

			</div>
			<div class="col-md-4">

				<div class="form-group">
					{!! Form::label('zip_code', trans('userinterface.labels.ZIP_CODE').'*') !!}
					{!! Form::text('zip_code',
						isset($domicile) ? $domicile->zip_code : null , ['class'=>'form-control', 'maxlength' => '15',
						 																								'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																														'placeholder' => trans('userinterface.placeholders.ZIP_CODE'), 'required']) !!}
				</div>

				<div class="form-group">
					{!! Form::label('country_id', trans('userinterface.labels.COUNTRY').'*') !!}
					{!! Form::select('country_id', $countries, isset($domicile) ?  $domicile->country_id : null ,
																['class'=>'form-control select-one',
																	'placeholder' => trans('userinterface.placeholders.COUNTRY'),
																	'required']) !!}
				</div>

				<div class="form-group">
	       {!! Form::label('state', trans('userinterface.labels.STATE').'*') !!}
	       <select class='form-control' name="state_id" id="state_id">
					 @if (isset($domicile))
						 <option value="{{ $domicile->state_id }}">{{ $domicile->state->name }}</option>
					 @else
							 <option value="">{{ trans('userinterface.placeholders.STATE') }}</option>
					 @endif
	       </select>
			</div>

				<div class="form-group">
					{!! Form::label('is_main', trans('userinterface.labels.HEAD_QUARTERS')) !!}
					{!! Form::checkbox('is_main', 1, isset($domicile) ? $domicile->is_main : false) !!}
				</div>

			</div>
		</div>
@endsection
