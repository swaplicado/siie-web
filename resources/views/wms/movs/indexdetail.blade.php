@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

<?php $sRoute="wms.movs"?>

@section('content')
	@section('thefilters')
		{!! Form::open(['route' => [$sRoute.'.indexdetail'],
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
		<table id="movs_detail_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">{{ trans('userinterface.labels.CODE') }}</th>
								<th>{{ trans('wms.labels.MAT_PROD') }}</th>
								<th>{{ trans('wms.labels.LOT') }}</th>
								<th>{{ trans('wms.labels.EXPIRATION') }}</th>
								<th>{{ trans('wms.labels.UN') }}</th>
								<th>{{ trans('wms.labels.BCH') }}</th>
								<th>{{ trans('wms.labels.WHS') }}</th>
								<th>{{ trans('userinterface.labels.FOLIO') }}</th>
								<th>{{ trans('userinterface.labels.MVT_DATE') }}</th>
								<th>{{ trans('wms.labels.MVT_TYPE') }}</th>
								<th>{{ trans('wms.labels.INPUTS') }}</th>
								<th>{{ trans('wms.labels.OUTPUTS') }}</th>
								<th>{{ trans('siie.labels.ORDER') }}</th>
								<th>{{ trans('userinterface.labels.DATE') }}</th>
								<th>{{ trans('siie.labels.INVOICE') }}</th>
								<th>{{ trans('userinterface.labels.DATE') }}</th>
								<th>{{ trans('siie.labels.C_N') }}</th>
								<th>{{ trans('userinterface.labels.DATE') }}</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($lRows as $row)
						<tr>
								<td>{{ $row->item_code }}</td>
								<td>{{ $row->item }}</td>
								<td>{{ $row->lot }}</td>
								<td>{{ \Carbon\Carbon::parse($row->dt_expiry)->format('Y-m-d') }}</td>
								{{-- <td>{{ $row->movement->dt_date }}</td> --}}
		            <td>{{ $row->unit }}</td>
		            <td>{{ $row->branch_code }}</td>
		            <td>{{ $row->whs_code }}</td>
		            <td>{{ $row->mov_code.'-'.str_pad($row->mov_folio, 5, "0", STR_PAD_LEFT) }}</td>
								<td>{{ \Carbon\Carbon::parse($row->mov_date)->format('Y-m-d') }}</td>
		            <td>{{ $row->movement }}</td>
								<?php
											$dInputs = 0;
											$dOutputs = 0;
											if ($row->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
												 $dInputs = $row->is_lot ? $row->lot_quantity : $row->row_quantity;
											}
											else {
												 $dOutputs = $row->is_lot ? $row->lot_quantity : $row->row_quantity;
											}
								 ?>
								<td align="right">{{ session('utils')->formatNumber($dInputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td align="right">{{ session('utils')->formatNumber($dOutputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
								@if ($row->doc_order_id > 1)
									<td>{{ $row->ser_num_order == '' ? $row->num_order : $row->ser_num_order.'-'.$row->num_order }}</td>
									<td>{{ \Carbon\Carbon::parse($row->dt_order)->format('Y-m-d') }}</td>
								@else
									<td>{{ '---' }}</td>
									<td>{{ '---' }}</td>
								@endif
								@if ($row->doc_invoice_id > 1)
									<td>{{ $row->ser_num_invoice == '' ? $row->num_invoice : $row->ser_num_invoice.'-'.$row->num_invoice }}</td>
									<td>{{ \Carbon\Carbon::parse($row->dt_invoice)->format('Y-m-d') }}</td>
								@else
									<td>{{ '---' }}</td>
									<td>{{ '---' }}</td>
								@endif
								@if ($row->doc_credit_note_id > 1)
									<td>{{ $row->ser_num_cn == '' ? $row->num_cn : $row->ser_num_cn.'-'.$row->num_cn }}</td>
									<td>{{ \Carbon\Carbon::parse($row->dt_cn)->format('Y-m-d') }}</td>
								@else
									<td>{{ '---' }}</td>
									<td>{{ '---' }}</td>
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
