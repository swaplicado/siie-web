@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

<?php $sRoute='wms.segregations'?>

@section('content')
	{{-- <div class="row">
			@include('templates.stock.filterstock')
	</div> --}}
	@include('wms.segregations.classification')
	<br />
	<div class="row">
		<table id="table_seg" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th data-priority="1">Clave</th>
		            <th>Item</th>
		            <th>Unidad</th>
		            <th>Lote</th>
		            <th>Tarima</th>
		            <th data-priority="1">Segregado</th>
		            <th>Almacén</th>
		            <th data-priority="1">{{ trans('userinterface.labels.STATUS') }}</th>
		            <th>Doc</th>
		            <th>-</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <td>{{ $row->id_item }}</td>
		            <td>{{ $row->id_unit }}</td>
		            <td>{{ $row->id_lot }}</td>
		            <td>{{ $row->id_pallet }}</td>
		            <td>{{ $row->id_whs }}</td>
		            <td>{{ $row->branch_id }}</td>
		            <td>{{ $row->id_document }}</td>
		            <td>{{ $row->id_status }}</td>
		            <td>{{ $row->item_code }}</td>
		            <td>{{ $row->item }}</td>
		            <td>{{ $row->unit }}</td>
		            <td>{{ $row->lot_name }}</td>
		            <td>{{ $row->pallet }}</td>
		            <td>{{ $row->segregated }}</td>
		            <td>{{ $row->warehouse }}</td>
		            <td>
									<span class="{{ App\SUtils\SGuiUtils::getClassOfStatus($row->id_status) }}">
										{{ $row->status_qlty }}
									</span>
								</td>
								<td>{{ $row->num_doc }}</td>
								<td>
									<a data-toggle="modal" data-target="#classQlty"
											title="Evaluar material/producto"
											onclick="classificateUnits(this)"
											class="btn btn-default btn-sm">
										<span class="glyphicon glyphicon-search" aria-hidden = "true"/>
									</a>
								</td>
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script src="{{ asset('js/segregation/segregation.js')}}"></script>
	<script src="{{ asset('js/segregation/segregations_table.js')}}"></script>
@endsection
