@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($group))
	<?php
			$aux = $group;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.groups.store';
			}
			else
			{
				$sRoute = 'siie.groups.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_GROUP'))
@else
	<?php
		$sRoute='siie.groups.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_GROUP'))
@endif
	<?php $sRoute2 = 'siie.groups.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('name', trans('userinterface.labels.GROUP').'*') !!}
			{!! Form::text('name',
				isset($group) ? $group->name : null , ['class'=>'form-control', 'maxlength' => '100',
				 																				'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																								'placeholder' => trans('userinterface.placeholders.GROUP'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('item_family_id', trans('userinterface.labels.FAMILY').'*') !!}
			{!! Form::select('item_family_id', $families, isset($group) ?  $group->item_family_id : null ,
																					['class'=>'form-control select-one',
																					'placeholder' => trans('userinterface.placeholders.FAMILY'),
																					'required']) !!}
		</div>

@endsection
