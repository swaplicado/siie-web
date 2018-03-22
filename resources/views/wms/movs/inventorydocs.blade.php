@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_DOCUMENTS'))

<?php $sRoute="wms.movs"?>

@section('content')
	@section('thefilters')
		{!! Form::open(['route' => [$sRoute.'.index'],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
				<div class="input-group">
					@include('templates.list.search')
					<span class="input-group-btn">
						{!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']); !!}
					</span>
					<span class="input-group-btn">
							<button id="searchbtn" type="submit" class="form-control">
								<span class="glyphicon glyphicon-search"></span>
							</button>
					</span>
				</div>
			</div>
		{!! Form::close() !!}
	@endsection
	<br />
	<div class="row">
		<table id="docs_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Fecha</th>
		            <th data-priority="1">Folio</th>
		            <th>Monto</th>
								<th data-priority="1">Tipo movimiento</th>
								<th data-priority="1">Subtipo</th>
								<th>Sucursal</th>
		            <th data-priority="1">Almacén</th>
								<th data-priority="1">Doc</th>
								<th>Clase</th>
								<th>Cat</th>
		            <th>Opciones</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($lMovs as $mov)
						<tr>
								<td>{{ \Carbon\Carbon::parse($mov->dt_date)->format('d-m-Y') }}</td>
								{{-- <td>{{ $row->movement->dt_date }}</td> --}}
		            <td>{{ $mov->folio }}</td>
								<td align="right">{{ session('utils')->formatNumber($mov->total_amount, \Config::get('scsiie.FRMT.AMT')) }}</td>
								<td>{{ $mov->mvtType->name }}</td>
								@if ($mov->mvt_trn_type_id != 1)
									<td>{{ $mov->trnType->name }}</td>
								@elseif($mov->mvt_adj_type_id != 1)
									<td>{{ $mov->adjType->name }}</td>
								@elseif($mov->mvt_mfg_type_id != 1)
									<td>{{ $mov->mfgType->name }}</td>
								@elseif($mov->mvt_exp_type_id != 1)
									<td>{{ $mov->expType->name }}</td>
								@else
									<td>N/A</td>
								@endif
								<td>{{ $mov->branch->name }}</td>
								<td>{{ $mov->warehouse->name }}</td>
								@if ($mov->doc_order_id != 1)
									<td>{{ $mov->order->num }}</td>
									<td>{{ $mov->order->docClass->name }}</td>
									<td>{{ $mov->order->doc_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@elseif($mov->doc_invoice_id != 1)
									<td>{{ $mov->invoice->num }}</td>
									<td>{{ $mov->invoice->docClass->name }}</td>
									<td>{{ $mov->invoice->doc_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@elseif($mov->doc_debit_note_id != 1)
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
								@elseif($mov->doc_credit_note_id != 1)
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
								@else
									<td>N/A</td>
									<td>N/A</td>
									<td>N/A</td>
								@endif
								<td>
      						<?php
      								$oRegistry = $mov;
      								$iRegistryId = $mov->id_mvt;
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
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script src="{{ asset('moment/moment.js') }}"></script>
	<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
	<script src="{{ asset('js/movements/docs/table.js')}}"></script>
	<script>
			 $(function() {
				 $('input[id="filterDate"]').daterangepicker({
					 locale: {
									format: 'DD/MM/YYYY'
							}
				 });
			 });

			 $('#filterDate').on('apply.daterangepicker', function(ev, picker) {
				 console.log(picker.startDate.format('YYYY-MM-DD'));
				 console.log(picker.endDate.format('YYYY-MM-DD'));
			 });
	</script>
@endsection
