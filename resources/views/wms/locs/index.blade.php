@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_LOCATIONS'))

@section('content')
	<?php $sRoute="wms.locs"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" id="catalog_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('userinterface.labels.CODE') }}</th>
			<th>{{ trans('userinterface.labels.LOCATION') }}</th>
			<th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
			<th>{{ trans('userinterface.labels.BRANCH') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
			<th>{{'Etiqueta'}}</th>
		</thead>
		<tbody>
			@foreach($locations as $location)
				<tr>
					<td>{{ $location->code }}</td>
					<td>{{ $location->name }}</td>
					<td>{{ $location->warehouse->name }}</td>
					<td>{{ $location->warehouse->branch->name }}</td>
					<td>
						@if (! $location->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $location;
								$iRegistryId = $location->id_whs_location;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
					<td>
							<a href="{{ route('wms.locations.barcode', $location->id_whs_location) }}" target="_blank" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-save" aria-hidden="true"></span></a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
