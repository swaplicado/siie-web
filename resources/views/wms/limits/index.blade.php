@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_LIMITS'))

@section('content')
	<?php $sRoute="wms.limits"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" id="catalog_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('wms.labels.MAX') }}</th>
			<th>{{ trans('wms.labels.MIN') }}</th>
			<th>{{ trans('wms.labels.REORDER')}}</th>
			<th>{{ trans('wms.labels.LOCATION') }}</th>
			<th>{{ trans('wms.labels.WAREHOUSE') }}</th>
			<th>{{ trans('wms.labels.BRANCH') }}</th>
			<th>{{ trans('wms.labels.COMPANY') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($limits as $limit)
				<tr>
					<td>{{ $limit->max }}</td>
					<td>{{ $limit->min }}</td>
					<td>{{ $limit->reorder }}</td>
					<td>{{ $limit->getLocation()->name }}</td>
					<td>{{ $limit->getWarehouse()->name }}</td>
					<td>{{ $limit->getBranch()->name }}</td>
					<td>{{ $limit->getCompany()->name }}</td>
					<td>
						@if (! $limit->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $limit;
								$iRegistryId = $limit->id_container_max_min;
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
{!! $limits->render() !!}
@endsection
