@extends('templates.basic_form')

@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.LIST_WAREHOUSES'))

@section('content')
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
  			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
  			{!! Form::text('code',
  				isset($whs) ? $whs->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
  			{!! Form::text('code',
  				isset($whs) ? $whs->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
  			{!! Form::text('code',
  				isset($whs) ? $whs->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
  			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
  			{!! Form::text('code',
  				isset($whs) ? $whs->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
  			{!! Form::text('code',
  				isset($whs) ? $whs->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('code', trans('userinterface.labels.CODE').'*') !!}
  			{!! Form::text('code',
  				isset($whs) ? $whs->code : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
        tabla
    </div>
  </div>
@endsection
