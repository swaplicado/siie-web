@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_BRANCHES'))

@section('content')

	<?php $sRoute='siie.branches' ?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-striped table-condensed">
		<thead>
			<th>{{ trans('userinterface.labels.BP') }}</th>
			<th>{{ trans('userinterface.labels.CODE') }}</th>
			<th>{{ trans('userinterface.labels.NAME') }}</th>
			<th>{{ trans('userinterface.labels.BRANCH') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($branches as $branch)
				<tr>
					<td>{{ $branch->partner->name }}</td>
					<td>{{ $branch->code }}</td>
					<td>{{ $branch->name }}</td>
					<td>
						@if ($branch->is_headquarters)
								<span class="label label-success">{{ trans('userinterface.labels.HEAD_QUARTERS') }}</span>
						@else
								<span class="label label-default">{{ $branch->name }}</span>
						@endif
					</td>
					<td>
						@if (! $branch->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $branch;
								$iRegistryId = $branch->id_branch;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
									\Config::get('scsys.OPTIONS.ADDRESS'),
								];
						?>
						@include('templates.list.options')
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $branches->render() !!}
@endsection
