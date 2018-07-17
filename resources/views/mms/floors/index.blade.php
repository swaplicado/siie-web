@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_FLOORS'))

@section('content')
	<?php $sRoute="mms.floors"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table id="cat_lots_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
      <th>{{ trans('userinterface.labels.CODE') }}</th>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($floors as $floor)
				<tr>
					<td>{{ $floor->code }}</td>
					<td>{{ $floor->name }}</td>
					<td>
						@if (! $floor->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $floor;
								$iRegistryId = $floor->id_floor;
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
@section('js')
	<script src="{{ asset('js/wms/lots_catalog.js')}}"></script>
@endsection
