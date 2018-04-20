@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($item))
	<?php
			$aux = $item;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.items.store';
			}
			else
			{
				$sRoute = 'siie.items.update';
			}
	?>
@else
	<?php
		$sRoute='siie.items.store';
	?>
@endif

	@section('title', $title)
	<?php $sRoute2 = 'siie.items.index' ?>

@section('content')

	<div class="row">
		  <div class="col-md-6">

			<div class="form-group">
				{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
				{!! Form::text('code',
					isset($item) ? $item->code : null , ['class'=>'form-control',  'maxlength' => '50', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																																				'placeholder' => trans('userinterface.placeholders.CODE'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('name', trans('userinterface.labels.NAME').'*') !!}
				{!! Form::text('name',
					isset($item) ? $item->name : null , ['class'=>'form-control', 'maxlength' => '255', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																																				'placeholder' => trans('userinterface.placeholders.NAME'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('unit_id', trans('userinterface.labels.UNIT').'*') !!}
				{!! Form::select('unit_id', $units, isset($item) ?  $item->unit_id : null, ['class'=>'form-control select-one',
											'placeholder' => trans('userinterface.placeholders.SELECT_UNIT'), 'required']) !!}
			</div>

		</div>
	  <div class="col-md-6">

			<div class="form-group">
				{!! Form::label('item_gender_id', trans('userinterface.labels.GENDER').'*') !!}
				{!! Form::select('item_gender_id', $genders, isset($item) ?  $item->item_gender_id : null, ['class'=>'form-control select-one',
											'placeholder' => trans('userinterface.placeholders.SELECT_GENDER'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('is_lot', trans('userinterface.labels.IS_LOT')) !!}
				{!! Form::checkbox('is_lot', 1, isset($item) ? $item->is_lot : false, ['class' => 'form-control']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('is_bulk', trans('userinterface.labels.IS_BULK')) !!}
				{!! Form::checkbox('is_bulk', 1, isset($item) ? $item->is_bulk : false, ['class' => 'form-control']) !!}
			</div>

		</div>
	</div>

@endsection
