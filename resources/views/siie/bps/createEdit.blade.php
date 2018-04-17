@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($bpartner))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.bps.store';
			}
			else
			{
				$sRoute = 'siie.bps.update';
			}
			$aux = $bpartner;
	?>
	@section('title', trans('userinterface.titles.EDIT_BRANCH'))
@else
	<?php
		$sRoute='siie.bps.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_BRANCH'))
@endif
	<?php $sRoute2 = 'siie.bps.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('name', trans('userinterface.labels.BP').'*') !!}
			{!! Form::text('name',
				isset($bpartner) ? $bpartner->name : null , ['class'=>'form-control', 'maxlength' => '200',
																											'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																											'placeholder' => trans('userinterface.placeholders.BP'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('last_name', trans('userinterface.labels.LAST_NAME')) !!}
			{!! Form::text('last_name',
				isset($bpartner) ? $bpartner->last_name : null , ['class'=>'form-control', 'maxlength' => '100',
				 																									'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																													'placeholder' => trans('userinterface.placeholders.LAST_NAME')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('first_name', trans('userinterface.labels.NAME')) !!}
			{!! Form::text('first_name',
				isset($bpartner) ? $bpartner->first_name : null , ['class'=>'form-control', 'maxlength' => '100',
				 																										'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																														'placeholder' => trans('userinterface.placeholders.NAME')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('fiscal_id', trans('userinterface.labels.RFC').'*') !!}
			{!! Form::text('fiscal_id',
				isset($bpartner) ? $bpartner->fiscal_id : null , ['class'=>'form-control', 'maxlength' => '50',
																													'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																													'placeholder' => trans('userinterface.placeholders.RFC'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('person_id', trans('userinterface.labels.CURP')) !!}
			{!! Form::text('person_id',
				isset($bpartner) ? $bpartner->person_id : null , ['class'=>'form-control', 'maxlength' => '50',
																														'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																														'placeholder' => trans('userinterface.placeholders.CURP')]) !!}
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-1">
					<div class="row">
						{!! Form::label('is_company', trans('userinterface.labels.IS_COMP')) !!}
					</div>
					<div class="row">
						{!! Form::checkbox('is_company', 1, isset($bpartner) ? $bpartner->is_company : false) !!}
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">
						{!! Form::label('is_supplier', trans('userinterface.labels.IS_SUPP')) !!}
					</div>
					<div class="row">
						{!! Form::checkbox('is_supplier', 1, isset($bpartner) ? $bpartner->is_supplier : false) !!}
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">
						{!! Form::label('is_customer', trans('userinterface.labels.IS_CUST')) !!}
					</div>
					<div class="row">
						{!! Form::checkbox('is_customer', 1, isset($bpartner) ? $bpartner->is_customer : false) !!}
					</div>
				</div>
				<div class="col-md-1">
					<div class="row">
						{!! Form::label('is_related_party', trans('userinterface.labels.IS_PART')) !!}
					</div>
					<div class="row">
						{!! Form::checkbox('is_related_party', 1, isset($bpartner) ? $bpartner->is_related_party : false) !!}
					</div>
				</div>
			</div>
		</div>

@endsection
