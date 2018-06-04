@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
	<span class="input-group-btn">
		{!! Form::select('filterBulk', [
										 \Config::get('scsiie.FILTER_BULK.RETAIL') => trans('siie.FILTERS.RETAIL'),
										 \Config::get('scsiie.FILTER_BULK.BULK') => trans('siie.FILTERS.BULK'),
										 \Config::get('scsiie.FILTER_BULK.ALL') => trans('siie.FILTERS.ALL'),
											],
											$iFilterBulk, ['class' => 'form-control', 'required', 'title' => trans('userinterface.labels.IS_BULK')]) !!}
		{!! Form::select('filterLot', [
										 \Config::get('scsiie.FILTER_LOT.NLOT') => trans('siie.FILTERS.NLOT'),
										 \Config::get('scsiie.FILTER_LOT.LOT') => trans('siie.FILTERS.LOT'),
										 \Config::get('scsiie.FILTER_LOT.ALL') => trans('siie.FILTERS.ALL'),
											],
											$iFilterLot, ['class' => 'form-control', 'required', 'title' => trans('userinterface.labels.IS_LOT') ]) !!}
		{!! Form::select('filterGender', $genders, $iFilterGender, ['class' => 'form-control', 'required', 'title' => trans('userinterface.labels.GENDER')]) !!}
	</span>
@endsection

@section('title', $title)

@section('content')
	<?php $sRoute='siie.items' ?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('userinterface.labels.CODE') }}</th>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.UNIT') }}</th>
			<th>{{ trans('userinterface.labels.IS_LOT') }}</th>
			<th>{{ trans('userinterface.labels.IS_BULK') }}</th>
			<th>{{ trans('wms.labels.WITHOUT_ROTATION') }}</th>
			<th>{{ trans('userinterface.labels.GENDER') }}</th>
			<th>{{ trans('userinterface.labels.CLASS') }}</th>
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
					<td class="small">
						@if ($item->without_rotation)
								<span class="label label-default">Sin rotación</span>
						@else
								<span class="label label-primary">Rotación</span>
						@endif
					</td>
					<td class="small">{{ $item->gender->name }}</td>
					<td class="small">{{ $item->gender->itemClass->name }}</td>
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
