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
					isset($item) ? $item->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('name', trans('userinterface.labels.NAME').'*') !!}
				{!! Form::text('name',
					isset($item) ? $item->name : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.NAME'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('unit_id', trans('userinterface.labels.UNIT')) !!}
				{!! Form::select('unit_id', $units, isset($item) ?  $item->unit_id : null, ['class'=>'form-control',
											'placeholder' => trans('userinterface.placeholders.SELECT_UNIT'), 'required']) !!}
			</div>

		</div>
	  <div class="col-md-6">

			<div class="form-group">
				{!! Form::label('gender_id', trans('userinterface.labels.GENDER')) !!}
				{!! Form::select('gender_id', $genders, isset($item) ?  $item->gender_id : null, ['class'=>'form-control',
											'placeholder' => trans('userinterface.placeholders.SELECT_GENDER'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('is_lot', trans('userinterface.labels.IS_LOT')) !!}
				{!! Form::checkbox('is_lot', 1, isset($item) ? $item->is_lot : false) !!}
			</div>

			<div class="form-group">
				{!! Form::label('is_bulk', trans('userinterface.labels.IS_BULK')) !!}
				{!! Form::checkbox('is_bulk', 1, isset($item) ? $item->is_bulk : false) !!}
				{!! Form::hidden('length', $item->length, array('id' => 'length')) !!}
				{!! Form::hidden('surface', $item->surface, array('id' => 'surface')) !!}
				{!! Form::hidden('volume', $item->volume, array('id' => 'volume')) !!}
				{!! Form::hidden('mass', $item->mass, array('id' => 'mass')) !!}
				{!! Form::hidden('external_id', $item->external_id, array('id' => 'external_id')) !!}
			</div>

		</div>
	</div>


@endsection
