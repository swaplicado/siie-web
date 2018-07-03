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
		{!! Form::open(['route' => ['wms.movs.docs'],
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
		<table id="docs_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Folio</th>
								<th data-priority="1">Fecha</th>
		            <th>Monto</th>
								<th data-priority="1">Tipo movimiento</th>
								<th data-priority="1">Subtipo</th>
								<th>Sucursal</th>
		            <th data-priority="1">Almacén</th>
								<th data-priority="1">Doc</th>
								<th>Clase</th>
								<th>Cat</th>
		            <th>Opciones</th>
		            <th data-priority="2">Creación</th>
		            <th data-priority="2">Usuario</th>
		            <th data-priority="2">Modificación</th>
		            <th data-priority="2">Usuario</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($lMovs as $mov)
						<tr>
								<td>{{ $mov->mov_code.'-'.str_pad($mov->mov_folio, 5, "0", STR_PAD_LEFT) }}</td>
								<td>{{ \Carbon\Carbon::parse($mov->mov_date)->format('Y-m-d') }}</td>
								{{-- <td>{{ $row->movement->dt_date }}</td> --}}
								<td align="right">{{ session('utils')->formatNumber($mov->total_amount, \Config::get('scsiie.FRMT.AMT')) }}</td>
								<td>{{ $mov->movement }}</td>
								@if ($mov->mvt_trn_type_id > 1)
									<td>{{ $mov->trn_code.'-'.$mov->trn_name }}</td>
								@elseif($mov->mvt_adj_type_id > 1)
									<td>{{ $mov->adj_code.'-'.$mov->adj_name }}</td>
								@elseif($mov->mvt_mfg_type_id > 1)
									<td>{{ $mov->mfg_code.'-'.$mov->mfg_name }}</td>
								@elseif($mov->mvt_exp_type_id > 1)
									<td>{{ $mov->exp_code.'-'.$mov->exp_name }}</td>
								@else
									<td>N/A</td>
								@endif
								<td>{{ $mov->branch }}</td>
								<td>{{ $mov->warehouse }}</td>
								@if ($mov->doc_order_id != 1)
									<td>{{ $mov->num_order }}</td>
									<td>{{ trans('siie.labels.ORDER') }}</td>
									<td>{{ $mov->order_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@elseif($mov->doc_invoice_id != 1)
									<td>{{ $mov->ser_num_invoice == '' ? $mov->num_invoice : ($mov->ser_num_invoice.'-'.$mov->num_invoice) }}</td>
									<td>{{ trans('siie.labels.INVOICE') }}</td>
									<td>{{ $mov->invoice_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
								@elseif($mov->doc_debit_note_id > 1)
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
									<td>{{ 'NA' }}</td>
								@elseif($mov->doc_credit_note_id > 1)
									<td>{{ $mov->ser_num_cn == '' ? $mov->num_cn : ($mov->ser_num_cn.'-'.$mov->num_cn) }}</td>
									<td>{{ trans('siie.labels.CREDIT_NOTE') }}</td>
									<td>{{ $mov->cn_category_id == \Config::get('scsiie.DOC_CAT.PURCHASES') ? 'COMPRA' : 'VENTA' }}</td>
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
      									\Config::get('scsys.OPTIONS.ACTIVATE')
      								];
      						?>
									<div>
											@include('templates.list.options')
											<a
											href="{{ route('wms.movs.print', $mov->id_mvt) }}"
											title="{{ trans('actions.PRINT') }}"
											target="_blank"
												class="btn btn-primary btn-xs">
												<span class="glyphicon glyphicon-print" aria-hidden = "true"/>
											</a>
									</div>
      					</td>
								<td>{{ $mov->created_at }}</td>
								<td>{{ $mov->username_creation }}</td>
								<td>{{ $mov->updated_at }}</td>
								<td>{{ $mov->username_update }}</td>
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
	<script src="{{ asset('datatables/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('datatables/buttons.flash.min.js') }}"></script>
	<script src="{{ asset('datatables/jszip.min.js') }}"></script>
	<script src="{{ asset('datatables/pdfmake.min.js') }}"></script>
	<script src="{{ asset('datatables/vfs_fonts.js') }}"></script>
	<script src="{{ asset('datatables/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('datatables/buttons.print.min.js') }}"></script>
@endsection
