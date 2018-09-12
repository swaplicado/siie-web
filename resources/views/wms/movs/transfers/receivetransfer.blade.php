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
	@if ($iOperation == \Config::get('scwms.OPERATION_TYPE.CREATION'))
		{!! Form::open(
			['route' => 'wms.movs.store', 'method' => 'POST', 'id' => 'theForm']
			) !!}
		@elseif ($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION'))
			{!! Form::open(
				['route' => ['wms.movs.update', $oMovement->id_mvt], 'method' => 'POST', 'id' => 'theForm']
				) !!}
			@endif

		  @include('wms.movs.transfers.header')
			@include('wms.movs.transfers.table')
			@include('wms.movs.transfers.lotsmod')
			@include('wms.movs.transfers.lotsadd')
			@include('wms.movs.transfers.lotsinfomodal')
		  @include('wms.movs.transfers.searchpanel')
			{!! Form::label('to_move', 'En movimiento:') !!}
			<div class="row">
				<div class="col-md-2" id="div_delete" style="display: none;">
					<button id="delButton" onclick="onDeleteButton()" type="button" class="btn btn-danger">{{ trans('actions.QUIT') }}</button>
				</div>
			</div>
			@include('wms.movs.tables.whstransfers')
			@include('wms.locs.locationsearch')

			{!! Form::hidden('mvt_whs_class_id', $oMovement->mvt_whs_class_id, ['id' => 'mvt_whs_class_id']) !!}
			{!! Form::hidden('src_mvt_id', $oMovementSrc->id_mvt, ['id' => 'src_mvt_id']) !!}
			{!! Form::hidden('movement_object', null, ['id' => 'movement_object']) !!}

			<div class="form-group" align="right">
				<a id="idFreeze" class="btn btn-info" onclick="unfreezeTrans()" role="button">{{ trans('actions.FREEZE') }}</a>
				{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled']) !!}
				<input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="window.history.back();"/>
			</div>
		{!! Form::close() !!}
@endsection

@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/transfers/SExternalTransfersCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/transfers/STransLotsCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/transfers/SGuiTransfers.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/Movements.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/search/SLocations.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/SGuiValidations.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/SFunctions.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/transfers/tables.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/tables.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/validations.js')}}"></script>
		@include('wms.movs.transfers.codejs')
@endsection
