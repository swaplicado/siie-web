@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_FOLIOS'))

@section('content')
	<?php $sRoute="wms.folios"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th>{{ trans('wms.labels.FOLIO_START') }}</th>
			<th>{{ trans('wms.labels.LOCATION') }}</th>
			<th>{{ trans('wms.labels.WAREHOUSE') }}</th>
			<th>{{ trans('wms.labels.BRANCH') }}</th>
			<th>{{ trans('wms.labels.COMPANY') }}</th>
			<th>{{ trans('wms.labels.MVT_CLASS') }}</th>
			<th>{{ trans('wms.labels.MVT_TYPE') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($folios as $folio)
				<tr>
					<td>{{ $folio->folio_start }}</td>
					<td>{{ $folio->getLocation()->name }}</td>
					<td>{{ $folio->getWarehouse()->name }}</td>
					<td>{{ $folio->getBranch()->name }}</td>
					<td>{{ $folio->getCompany()->name }}</td>
					<td>{{ $folio->mvtClass->name }}</td>
					<td>{{ $folio->mvtType->name }}</td>
					<td>
						@if (! $folio->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $folio;
								$iRegistryId = $folio->id_container_folio;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>

@endsection
