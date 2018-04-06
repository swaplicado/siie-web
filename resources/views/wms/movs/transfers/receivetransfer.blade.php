@extends('templates.basic_form')

@section('head')
	@include('templates.head')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)
@section('titlepanel', $sTitle)

@section('content')
  <div class="row">
		<div class="col-md-4">
			{!! Form::label(trans('wms.labels.SOURCE_BRANCH')) !!}
			{!! Form::label('lab_branch_src', $oMovRef->branch->name, ['class'=>'form-control input-sm']) !!}
		</div>
		<div class="col-md-4">
			{!! Form::label(trans('wms.labels.SOURCE_BRANCH')) !!}
			{!! Form::label('lab_branch_src', $oMovRef->dt_date, ['class'=>'form-control input-sm']) !!}
		</div>
		<div class="col-md-4">
			{!! Form::label(trans('wms.labels.SOURCE_BRANCH')) !!}
			{!! Form::label('lab_branch_src', $oMovRef->userCreation->username, ['class'=>'form-control input-sm']) !!}
		</div>
  </div>
  <div class="row">
		<div class="col-md-3">
			{!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE').'*') !!}
			{!! Form::label('mvt_whs_type_id', 'ENTRADA TRASPASO', ['class'=>'form-control input-sm']) !!}
		</div>
		<div class="col-md-3">
			{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
			{!! Form::date('dt_date', session('work_date'),
																								['class'=>'form-control input-sm',
																								'id' => 'dt_date']) !!}
		</div>
	  </div>
  <div class="row">

  </div>
  <div class="row">

  </div>
  <div class="row">

  </div>
@endsection


@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/transfers/STransfersCore.js')}}"></script>
@endsection
