@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
	<span class="input-group-btn">
		{!! Form::select('filterBp', [
										 \Config::get('scsiie.ATT.ALL') => trans('userinterface.labels.ALL'),
										 \Config::get('scsiie.ATT.IS_COMP') => trans('userinterface.labels.IS_COMP'),
										 \Config::get('scsiie.ATT.IS_SUPP') => trans('userinterface.labels.IS_SUPP'),
										 \Config::get('scsiie.ATT.IS_CUST') => trans('userinterface.labels.IS_CUST'),
										 \Config::get('scsiie.ATT.IS_PART') => trans('userinterface.labels.IS_PART'),
											],
											$iFilterBp, ['class' => 'form-control', 'required']) !!}
	</span>
@endsection

@section('title', trans('userinterface.titles.LIST_BPS'))

@section('content')
	<?php $sRoute='siie.bps' ?>
	@section('create')
		{{-- @include('templates.form.create') --}}
	@endsection
	<table class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th data-field="id" data-sortable="true">{{ trans('userinterface.labels.BP') }}</th>
			<th data-sortable="true">{{ trans('userinterface.labels.RFC') }}</th>
			<th data-sortable="true">Rot. lotes</th>
			<th data-sortable="true">{{ trans('userinterface.labels.ERP_ID') }}</th>
			<th>ATT</th>
			<th data-sortable="true">{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
		</thead>
		<tbody>
			@foreach($bps as $bpartner)
				<tr>
					<td>{{ $bpartner->name }}</td>
					<td>{{ $bpartner->fiscal_id }}</td>
					<td>
						@if ($bpartner->is_rotation_required)
								<span class="label label-success">{{ 'REQUERIDA' }}</span>
						@else
								<span class="label label-danger">{{ 'NO' }}</span>
						@endif
					</td>
					<td>{{ $bpartner->external_id }}</td>
					<td>
						@if ($bpartner->is_company)
								<span class="label label-success">{{ trans('userinterface.labels.IS_COMP') }}</span>
						@elseif ($bpartner->is_supplier)
								<span class="label label-default">{{ trans('userinterface.labels.IS_SUPP') }}</span>
						@elseif ($bpartner->is_customer)
								<span class="label label-primary">{{ trans('userinterface.labels.IS_CUST') }}</span>
						@elseif ($bpartner->is_related_party)
								<span class="label label-info">{{ trans('userinterface.labels.IS_PART') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.OTHER') }}</span>
						@endif
					</td>
					<td>
						@if (! $bpartner->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $bpartner;
								$iRegistryId = $bpartner->id_partner;
								$loptions = [
									// \Config::get('scsys.OPTIONS.EDIT'),
									// \Config::get('scsys.OPTIONS.DESTROY'),
									// \Config::get('scsys.OPTIONS.ACTIVATE'),
									// \Config::get('scsys.OPTIONS.DUPLICATE'),
									// \Config::get('scsys.OPTIONS.COPY'),
									// \Config::get('scsys.OPTIONS.NEW_BRANCH'),
								];
						?>
						@include('templates.list.options')
						<a href="{{ route('siie.branches.index', $bpartner->id_partner) }}" class="btn btn-success btn-xs">
							<span class="glyphicon glyphicon-home" aria-hidden="true"></span>
						</a>
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $bps->render() !!}
@endsection
