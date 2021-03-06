@extends('templates.list.mainlist')
@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.LIST_MONTHS').' '.$iYear)

@section('content')
	<?php $sRoute="siie.months"?>
	<table data-toggle="table" id="catalog_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>{{ trans('userinterface.labels.MONTH') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($months as $month)
				<tr>
					<td>{{ App\SUtils\SGuiUtils::getNameOfMonth($month->month) }}</td>
					<td>
						@if (!$month->is_closed)
								<span class="label label-success">{{ trans('userinterface.labels.OPENED') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.CLOSED') }}</span>
						@endif
					</td>
					<td>
						<a href="{{ route('siie.months.edit', $month->id_month) }}" data-toggle = "modificar" title="{{ trans('userinterface.tooltips.EDIT') }}"
																												style="visibility: {{ App\SUtils\SValidation::isRendered(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $month->created_by_id) }};"
																												class="btn btn-info">
							<span class="glyphicon glyphicon-pencil" aria-hidden = "true"/>
						</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
