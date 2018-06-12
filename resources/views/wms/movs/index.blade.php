@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_MOVEMENTS'))

<?php $sRoute="wms.movs"?>

@section('content')
	@section('thefilters')
		{!! Form::open(['route' => [$sRoute.'.index'],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
				<div class="input-group">
					@include('templates.list.search')
					<span class="input-group">
						{!! Form::select('warehouse', $lWarehouses, $iFilterWhs,
															['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.WAREHOUSE')]) !!}
					</span>
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
		<table id="movs_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Folio</th>
								<th data-priority="1">{{ trans('userinterface.labels.DATE') }}</th>
		            <th data-priority="1">{{ trans('userinterface.labels.CODE') }}</th>
		            <th data-priority="1">{{ trans('wms.labels.MAT_PROD') }}</th>
								<th data-priority="1">Entradas</th>
								<th data-priority="1">Salidas</th>
								<th data-priority="1">Unidad</th>
		            <th>Sucursal</th>
		            <th>Almacén</th>
		            <th>Tipo movimiento</th>
		            <th>Tipo</th>
								<th data-priority="1">Doc</th>
								<th>Clase</th>
								<th>Cat</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($rows as $row)
						<tr>
								<td>{{ $row->mov_code.'-'.$row->mov_folio }}</td>
								<td>{{ \Carbon\Carbon::parse($row->mov_date)->format('Y-m-d') }}</td>
								{{-- <td>{{ $row->movement->dt_date }}</td> --}}
		            <td>{{ $row->item_code }}</td>
		            <td>{{ $row->item }}</td>
								@if ($row->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN'))
									<td align="right">{{ session('utils')->formatNumber($row->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td align="right">{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
								@else
									<td align="right">{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td align="right">{{ session('utils')->formatNumber($row->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
								@endif
								<td align="right">{{ $row->unit_code }}</td>
								<td>{{ $row->branch }}</td>
								<td>{{ $row->warehouse }}</td>
								<td>{{ $row->movement }}</td>
								@if ($row->mvt_trn_type_id > 1)
									<td>{{ $row->trn_name }}</td>
								@elseif($row->mvt_adj_type_id > 1)
									<td>{{ $row->adj_name }}</td>
								@elseif($row->mvt_mfg_type_id > 1)
									<td>{{ $row->mfg_name }}</td>
								@elseif($row->mvt_exp_type_id > 1)
									<td>{{ $row->exp_name }}</td>
								@else
									<td>N/A</td>
								@endif
								@if ($row->doc_order_id != 1)
									<td>{{ $row->num_order }}</td>
									<td>{{ trans('siie.labels.ORDER') }}</td>
									<td>{{ $row->order_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@elseif($row->doc_invoice_id != 1)
									<td>{{ $row->ser_num_invoice == '' ? $row->num_invoice : ($row->ser_num_invoice.'-'.$row->num_invoice) }}</td>
									<td>{{ trans('siie.labels.INVOICE') }}</td>
									<td>{{ $row->invoice_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@elseif($row->doc_debit_note_id > 1)
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
								@elseif($row->doc_credit_note_id > 1)
									<td>{{ $row->ser_num_cn == '' ? $row->num_cn : ($row->ser_num_cn.'-'.$row->num_cn) }}</td>
									<td>{{ trans('siie.labels.CREDIT_NOTE') }}</td>
									<td>{{ $row->cn_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@else
									<td>N/A</td>
									<td>N/A</td>
									<td>N/A</td>
								@endif
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
	<script src="{{ asset('js/wms/movs.js') }}"></script>
	<script>
			 var folioParameter = <?php echo json_encode($iFolio); ?>;
			 if (folioParameter != 0) {
						swal(
							  'Folio: ' + folioParameter,
							  'El movimiento ha sido guardado.',
							  'success'
							);
			 }

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
	<script src="{{ asset('datatables/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('datatables/buttons.flash.min.js') }}"></script>
	<script src="{{ asset('datatables/jszip.min.js') }}"></script>
	<script src="{{ asset('datatables/pdfmake.min.js') }}"></script>
	<script src="{{ asset('datatables/vfs_fonts.js') }}"></script>
	<script src="{{ asset('datatables/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('datatables/buttons.print.min.js') }}"></script>
@endsection
