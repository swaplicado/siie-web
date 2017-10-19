@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', trans('userinterface.titles.LIST_GENDERS'))

@section('content')
	<?php $sRoute="siie.genders"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th>{{ trans('userinterface.labels.GENDER') }}</th>
			<th>{{ trans('userinterface.labels.GROUP') }}</th>
			<th>{{ trans('userinterface.labels.CLASS') }}</th>
			<th>{{ trans('userinterface.labels.TYPE') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($genders as $gender)
				<tr>
					<td>{{ $gender->name }}</td>
					<td>{{ $gender->group->name }}</td>
					<td>{{ $gender->class->name }}</td>
					<td>{{ $gender->type->name }}</td>
					<td>
						@if (! $gender->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $gender;
								$iRegistryId = $gender->id_item_gender;
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
	{!! $genders->render() !!}
@endsection
