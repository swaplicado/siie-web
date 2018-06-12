@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_LOTS'))

@section('content')
	<?php $sRoute="wms.lots"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table id="cat_lots_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.EXPIRY') }}</th>
			<th>{{ trans('userinterface.labels.ITEM') }}</th>
			<th>{{ trans('userinterface.labels.UNIT') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
			<th>{{'Etiqueta'}}</th>
		</thead>
		<tbody>
			@foreach($lots as $lot)
				<tr>
					<td>{{ $lot->lot }}</td>
					<td>{{ $lot->dt_expiry }}</td>
					<td>{{ $lot->item_code.' - '.$lot->item }}</td>
					<td>{{ $lot->unit_code.' - '.$lot->unit }}</td>
					<td>
						@if (! $lot->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $lot;
								$iRegistryId = $lot->id_lot;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
					<td>
							<a href="{{ route('wms.lots.barcode', $lot->id_lot) }}" target="_blank" class="btn btn-success btn-xs">
								<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
							</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
@section('js')
	<script src="{{ asset('js/wms/lots_catalog.js')}}"></script>
@endsection
