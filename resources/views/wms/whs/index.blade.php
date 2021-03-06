@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_WAREHOUSES'))

@section('content')
	<?php $sRoute="wms.whs"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" id="catalog_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('userinterface.labels.CODE') }}</th>
			<th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
			<th>{{ trans('userinterface.labels.TYPE') }}</th>
			<th>{{ trans('userinterface.labels.BRANCH') }}</th>
			<th>{{ trans('userinterface.labels.IS_QUALITY') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($warehouses as $whs)
				<tr>
					<td>{{ $whs->code }}</td>
					<td>{{ $whs->name }}</td>
					<td>{{ $whs->whsType->name }}</td>
					<td>{{ $whs->branch->name }}</td>
					<td>
						@if ($whs->is_quality)
								<span class="label label-warning">{{ trans('userinterface.labels.IS_QUALITY') }}</span>
						@else
								<span class="label label-default">{{ trans('userinterface.labels.IS_NOT_QUALITY') }}</span>
						@endif
					</td>
					<td>
						@if (! $whs->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $whs;
								$iRegistryId = $whs->id_whs;
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
	{!! $warehouses->render() !!}
@endsection
