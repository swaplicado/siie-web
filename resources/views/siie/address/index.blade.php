@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_ADDRESS'))

@section('content')

	<?php $sRoute="siie.address"?>

	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.STREET') }}</th>
			<th>{{ trans('userinterface.labels.NUM_EXT') }}</th>
			<th>{{ trans('userinterface.labels.NEIGHBORHOOD') }}</th>
			<th>{{ trans('userinterface.labels.LOCALITY') }}</th>
			<th>{{ trans('userinterface.labels.STATE') }}</th>
			<th>{{ trans('userinterface.labels.COUNTRY') }}</th>
			<th>{{ trans('userinterface.labels.BRANCH') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($address as $domicile)
				<tr>
					<td>{{ $domicile->name }}</td>
					<td>{{ $domicile->street }}</td>
					<td>{{ $domicile->num_ext }}</td>
					<td>{{ $domicile->neighborhood }}</td>
					<td>{{ $domicile->locality }}</td>
					<td>{{ $domicile->state }}</td>
					<td>{{ $domicile->county }}</td>
					<td>{{ $domicile->branch->name }}</td>
					<td>
						@if (! $domicile->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $domicile;
								$iRegistryId = $domicile->id_branch_address;
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
	{!! $address->render() !!}
@endsection
