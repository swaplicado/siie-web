@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', $title)

@section('content')
	<?php $sRoute='siie.items' ?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th>{{ trans('userinterface.labels.CODE') }}</th>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.UNIT') }}</th>
			<th>{{ trans('userinterface.labels.IS_LOT') }}</th>
			<th>{{ trans('userinterface.labels.IS_BULK') }}</th>
			<th>{{ trans('userinterface.labels.GENDER') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($items as $item)
				<tr>
					<td class="small">{{ $item->code }}</td>
					<td class="small">{{ $item->name }}</td>
					<td class="small">{{ $item->unit->name }}</td>
					<td class="small">
						@if ($item->is_lot)
								<span class="label label-success">Con lote</span>
						@else
								<span class="label label-default">Sin lote</span>
						@endif
					</td>
					<td class="small">
						@if ($item->is_bulk)
								<span class="label label-info">A granel</span>
						@else
								<span class="label label-warning">Entero</span>
						@endif
					</td>
					<td class="small">{{ $item->gender->name }}</td>
					<td class="small">
						@if (! $item->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td class="small">
						<?php
								$oRegistry = $item;
								$iRegistryId = $item->id_item;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
									\Config::get('scsys.OPTIONS.COPY'),
								];
						?>
						@include('templates.list.options')
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $items->render() !!}
@endsection
