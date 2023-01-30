@extends('templates.formmovs')

@section('head')
	@include('templates.headmovs')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

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
	@include('wms.movs.subviews.header')
	@include('wms.movs.subviews.documentrows')
	@include('wms.movs.subviews.po')
	@include('wms.movs.subviews.pomodal')
	@if (App\SUtils\SGuiUtils::showPallet($oMovement->mvt_whs_type_id))
		@include('wms.movs.pallet')
	@endif
	@if (App\SUtils\SGuiUtils::isProductionMovement($oMovement->mvt_whs_type_id))
		@include('wms.movs.subviews.printlot')
	@endif
{{-- AQUI VA EL DIV DE BÚSQUEDA --}}
 @include('wms.movs.subviews.searchpanel')
 @include('wms.movs.subviews.datapanel')
  <div class="row">
    <div class="col-xs-12">
			<div class="form-group">
					@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_TRA')
								|| App\SUtils\SGuiUtils::isProductionTransfer($oMovement->mvt_whs_type_id))
							@include('wms.movs.tables.whstransfers')
					@else
							@include('wms.movs.tables.adjustments')
					@endif
			</div>
    </div>
  </div>
	@if (is_object($oDocument))
			<div class="row">
				<div class="col-md-6  col-md-offset-5">
						Porcentaje de surtido
				</div>
			</div>
			<div class="progress">
			  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"></div>
			</div>
	@endif
	{!! Form::hidden('movement_object', null, ['id' => 'movement_object']) !!}
	<div class="form-group" align="right">
		<a id="idFreeze" style="display:none" class="btn btn-info" onclick="unfreeze()" role="button">{{ trans('actions.FREEZE') }}</a>
		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled',
													'onclick' => "this.disabled=true;this.form.submit();"]) !!}
		<input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="window.history.back();"/>
	</div>
{!! Form::close() !!}
@endsection

@section('js')
	@include('templates.scriptsmovs')
	@include('wms.movs.subviews.sectionjs')
@endsection

{{-- @include('wms.movs.lotrows') --}}
@include('wms.movs.search.itemsearch')
@include('wms.movs.search.items')
@include('wms.locs.locationsearch')
@include('wms.locs.locationsearchdes')
@include('wms.movs.lotsmodal')
@include('wms.movs.palletmodal')
@include('wms.movs.stockmodal')
@include('wms.movs.stockcompletemodal')
