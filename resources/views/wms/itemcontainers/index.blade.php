@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_ITEM_CONTAINERS'))

@section('content')
	<?php $sRoute="wms.itemcontainers"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th>{{ trans('wms.labels.REFERENCE') }}</th>
			<th>{{ trans('wms.labels.LEVEL') }}</th>
			<th>{{ trans('wms.labels.LOCATION') }}</th>
			<th>{{ trans('wms.labels.WAREHOUSE') }}</th>
			<th>{{ trans('wms.labels.BRANCH') }}</th>
			<th>{{ trans('wms.labels.COMPANY') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($itemcontainers as $itemcontainer)
				<tr>
					<td>{{ App\SUtils\SGuiUtils::getTextOfLinkId($itemcontainer->item_link_type_id, $itemcontainer->item_link_id) }}</td>
					<td>{{ App\SUtils\SGuiUtils::getTextOfLinkTypeId($itemcontainer->item_link_type_id) }}</td>
					<td>{{ $itemcontainer->getLocation()->name }}</td>
					<td>{{ $itemcontainer->getWarehouse()->name }}</td>
					<td>{{ $itemcontainer->getBranch()->name }}</td>
					<td>{{ $itemcontainer->getCompany()->name }}</td>
					<td>
						@if (! $itemcontainer->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $itemcontainer;
								$iRegistryId = $itemcontainer->id_container_item;
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
{!! $itemcontainers->render() !!}
@endsection
