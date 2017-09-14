@extends('front.mainListado')
@section('menu')
	@include('front.templates.menumodules')
@endsection

@section('title', trans('userinterface.titles.LIST_COMPANIES'))

@section('content')
	<?php $sRoute="mrp.companies"?>
	<table data-toggle="table" class="table table-striped">
		<thead>
			<th>{{ trans('userinterface.labels.COMPANY') }}</th>
			<th>{{ trans('userinterface.labels.RFC') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
			<th>{{ trans('userinterface.labels.CREATED_BY') }}</th>
			<th>{{ trans('userinterface.labels.CREATED') }}</th>
			<th>{{ trans('userinterface.labels.UPDATED_BY') }}</th>
			<th>{{ trans('userinterface.labels.UPDATED') }}</th>
		</thead>
		<tbody>
			@foreach($companies as $company)
				<tr>
					<td>{{ $company->name }}</td>
					<td>{{ $company->rfc }}</td>
					<td>
						@if (! $company->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $company;
								$sRoute = 'mrp.companies';
								$iRegistryId = $company->id_company;
						?>
						@include('front.listed.options')
					</td>
					<td>
						@include('front.listed.createdUs')
					</td>
					<td>
						@include('front.listed.created')
					</td>
					<td>
						@include('front.listed.updatedUs')
					</td>
					<td>
						@include('front.listed.updated')
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $companies->render() !!}
@endsection
