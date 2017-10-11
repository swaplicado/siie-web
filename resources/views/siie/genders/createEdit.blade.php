@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($gender))
	<?php
			$aux = $gender;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.groups.store';
			}
			else
			{
				$sRoute = 'siie.groups.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_FAMILY'))
@else
	<?php
		$sRoute='siie.groups.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_FAMILY'))
@endif
	<?php $sRoute2 = 'siie.groups.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('name', trans('userinterface.labels.GROUP').'*') !!}
			{!! Form::text('name',
				isset($gender) ? $gender->name : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.GENDER'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('item_group_id', trans('userinterface.labels.GROUP')) !!}
			{!! Form::select('item_group_id', $groups, isset($gender) ?  $gender->item_group_id : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.GROUP'), 'required']) !!}
		</div>

@endsection
