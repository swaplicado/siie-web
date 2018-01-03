@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', 'Listado Tarimas')

@section('content')
	<?php $sRoute="wms.pallets"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.ITEM') }}</th>
			<th>{{ trans('userinterface.labels.UNIT') }}</th>
      <th>{{ 'Cantidad' }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
			<th>{{'Etiqueta'}}</th>
		</thead>
		<tbody>
			@foreach($pallets as $pallet)
				<tr>
					<td>{{ $pallet->pallet }}</td>
					<td>{{ $pallet->item->name }}</td>
					<td>{{ $pallet->unit->name }}</td>
          <td>{{ $pallet->quantity }}</td>
					<td>
						@if (! $pallet->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $pallet;
								$iRegistryId = $pallet->id_pallet;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
					<td>
							<a href="{{ route('wms.pallets.barcode', $pallet->id_pallet) }}" class="btn btn-success"><span class="glyphicon glyphicon-save" aria-hidden="true"></span></a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $pallets->render() !!}
@endsection
