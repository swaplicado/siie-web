@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_FAMILIES'))

@section('content')
	<?php $sRoute="siie.families"?>
	@section('create')
		{{-- @include('templates.form.create') --}}
	@endsection
	<table data-toggle="table" id="catalog_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('userinterface.labels.FAMILY') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($families as $family)
				<tr>
					<td>{{ $family->name }}</td>
					<td>
						@if (! $family->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $family;
								$iRegistryId = $family->id_item_family;
								$loptions = [
									// \Config::get('scsys.OPTIONS.EDIT'),
									// \Config::get('scsys.OPTIONS.DESTROY'),
									// \Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
