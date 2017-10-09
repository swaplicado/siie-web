@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_PERMISSIONS'))

@section('content')
	<?php $sRoute='admin.permissions' ?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-striped">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.NAME') }}</th>
			<th data-sortable="true">{{ trans('userinterface.labels.TYPE') }}</th>
			<th data-sortable="true">{{ trans('userinterface.labels.STATUS') }}</th>
		</thead>
		<tbody>
			@foreach($permissions as $permission)
				<tr>
					<td>{{ $permission->name }}</td>
					<td>{{ $permission->permissionType->name }}</td>
					<td>
						@if (! $permission->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $permissions->render() !!}
@endsection
