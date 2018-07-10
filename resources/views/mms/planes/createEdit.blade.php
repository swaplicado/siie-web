@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', $sTitle)
@section('titlepanel', $sTitle)

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($oPlan->id_production_plan))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'mms.planes.store';
        $method = 'POST';
        $oSend = $sRoute;
			}
			else
			{
				$sRoute = 'mms.planes.update';
        $method = 'PUT';
        $oSend = [$sRoute, $oPlan];
			}
			$aux = $oPlan;
	?>
@else
	<?php
		$sRoute='mms.planes.store';
    $method = 'POST';
    $oSend = $sRoute;
	?>
	@section('title', trans('userinterface.titles.CREATE_FORMULA'))
@endif
	<?php $sRoute2 = 'mms.planes.index' ?>

@section('content')
  {!! Form::open(['route' => $oSend, 'method' => $method]) !!}
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('folio', trans('wms.labels.FOLIO')	) !!}
            {!! Form::text('folio', isset($oPlan->folio) ?
                               str_pad($oPlan->folio, 5, "0", STR_PAD_LEFT) : null,
                            ['id' => 'folio', 'class' => 'form-control input-sm',
                              'readonly']) !!}
          </div>

          <div class="form-group">
            {!! Form::label('production_plan', trans('userinterface.labels.NAME').'*'	) !!}
            {!! Form::text('production_plan', isset($oPlan->production_plan) ?
                                $oPlan->production_plan : null,
                            ['id' => 'production_plan', 'class' => 'form-control input-sm', 'required']) !!}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('dt_start', trans('mms.labels.DATE_START').'*') !!}
            {!! Form::date('dt_start',
                isset($oPlan->dt_start) ? $oPlan->dt_start : session('work_date'),
                                                      ['class'=>'form-control input-sm',
                                                      'id' => 'dt_start', 'required']) !!}
          </div>
          <div class="form-group">
            {!! Form::label('dt_end', trans('mms.labels.DATE_END').'*') !!}
            {!! Form::date('dt_end',
                isset($oPlan->dt_end) ? $oPlan->dt_end : session('work_date'),
                                                      ['class'=>'form-control input-sm',
                                                      'id' => 'dt_end', 'required']) !!}
          </div>
          <div class="form-group">
            {!! Form::label('floor_id', trans('mms.labels.FLOOR').'*'	) !!}
            {!! Form::select('floor_id', $lFloors, isset($oPlan->floor_id) ?
                                                    $oPlan->floor_id : null,
                      ['class'=>'form-control select-one',
                      'placeholder' => trans('mms.placeholders.SELECT_FLOOR'),
                      'required']) !!}
          </div>
        </div>
      </div>
      <br />
      <div class="row">
        <div class="form-group" align="right">
      		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
      		<input type="button" value="{{ trans('actions.CANCEL') }}"
                  class="btn btn-danger" onClick="window.history.back();"/>
      	</div>
      </div>
  {!! Form::close() !!}
@endsection

@section('js')

@endsection

@section('footer')
    @include('templates.footer')
@endsection
