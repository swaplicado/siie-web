@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($family))
	<?php
			$aux = $family;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.families.store';
			}
			else
			{
				$sRoute = 'siie.families.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_FAMILY'))
@else
	<?php
		$sRoute='siie.families.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_FAMILY'))
@endif
	<?php $sRoute2 = 'siie.families.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('name', trans('userinterface.labels.FAMILY').'*') !!}
			{!! Form::text('name',
				isset($family) ? $family->name : null , ['class'=>'form-control', 'maxlength' => '100',
				 																						'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																										'placeholder' => trans('userinterface.placeholders.FAMILY'), 'required']) !!}
		</div>

@endsection
