@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($gender))
	<?php
			$aux = $gender;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.genders.store';
			}
			else
			{
				$sRoute = 'siie.genders.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_GENDER'))
@else
	<?php
		$sRoute='siie.genders.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_GENDER'))
@endif
	<?php $sRoute2 = 'siie.genders.index' ?>

@section('content')

	<div class="row">
	  <div class="col-md-6">
			<div class="form-group">
				{!! Form::label('name', trans('userinterface.labels.GENDER').'*') !!}
				{!! Form::text('name',
					isset($gender) ? $gender->name : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.GENDER'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('item_group_id', trans('userinterface.labels.GROUP')) !!}
				{!! Form::select('item_group_id', $groups, isset($gender) ?  $gender->item_group_id : null , ['class'=>'form-control',
					'placeholder' => trans('userinterface.placeholders.SELECT_GROUP'), 'required']) !!}
			</div>
		</div>
	  <div class="col-md-6">
			<div class="form-group">
				{!! Form::label('item_class_id', trans('userinterface.labels.CLASS')) !!}
				{!! Form::select('item_class_id', $classes, isset($gender) ?  $gender->item_class_id : null, ['id' => 'itm_class', 'class'=>'form-control',
											'placeholder' => trans('userinterface.placeholders.SELECT_CLASS'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('item_type_id', trans('userinterface.labels.TYPE')) !!}
				{!! Form::select('item_type_id', ['placeholder' => trans('userinterface.placeholders.SELECT_TYPE')], null, ['class'=>'form-control', 'id' => 'itm_type']) !!}
			</div>
		</div>
	</div>


@endsection