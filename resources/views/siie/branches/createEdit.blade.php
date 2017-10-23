@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($branch))
	<?php
			$aux = $branch;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.branches.store';
			}
			else
			{
				$sRoute = 'siie.branches.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_BRANCH'))
@else
	<?php
		$sRoute='siie.branches.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_BRANCH'))
@endif
	<?php $sRoute2 = 'siie.bps.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('partnername', trans('userinterface.labels.BP')) !!}
			{!! Form::text('partnername',
				isset($partner) ? $partner->name : null , ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																										'required', 'readonly']) !!}
			{!! Form::hidden('partner_id', $partner->id_partner) !!}
		</div>

		<div class="form-group">
			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
			{!! Form::text('code',
				isset($branch) ? $branch->code : null , ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																								'required', 'placeholder' => trans('userinterface.placeholders.CODE')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('name', trans('userinterface.labels.BRANCH').'*') !!}
			{!! Form::text('name',
				isset($branch) ? $branch->name : null , ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																									'placeholder' => trans('userinterface.placeholders.BRANCH'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('is_headquarters', trans('userinterface.labels.HEAD_QUARTERS').'*') !!}
			{!! Form::checkbox('is_headquarters', 1, isset($branch) ? $branch->is_headquarters : false) !!}
		</div>

@endsection
