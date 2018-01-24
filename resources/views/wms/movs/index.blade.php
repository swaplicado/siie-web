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
		<table id="table_id" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Fecha</th>
		            <th data-priority="1">Clave</th>
		            <th data-priority="1">Item</th>
								<th data-priority="1">Entradas</th>
								<th data-priority="1">Salidas</th>
								<th data-priority="1">Unidad</th>
		            <th>Sucursal</th>
		            <th>Almacén</th>
		            <th>Tipo movimiento</th>
								<th data-priority="1">Folio</th>
		            <th>Tipo</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($rows as $row)
						<tr>
								<td>{{ \Carbon\Carbon::parse($row->movement->dt_date)->format('d-m-Y') }}</td>
								{{-- <td>{{ $row->movement->dt_date }}</td> --}}
		            <td>{{ $row->item->code }}</td>
		            <td>{{ $row->item->name }}</td>
								@if ($row->movement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN'))
									<td align="right">{{ session('utils')->formatNumber($row->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td align="right">{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
								@else
									<td align="right">{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td align="right">{{ session('utils')->formatNumber($row->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
								@endif
								<td align="right">{{ $row->item->unit->code }}</td>
								<td>{{ $row->movement->branch->name }}</td>
								<td>{{ $row->movement->warehouse->name }}</td>
								<td>{{ $row->movement->mvtType->name }}</td>
								<td>{{ $row->movement->folio }}</td>
								@if ($row->movement->mvt_trn_type_id != 1)
									<td>{{ $row->movement->trnType->name }}</td>
								@elseif($row->movement->mvt_adj_type_id != 1)
									<td>{{ $row->movement->adjType->name }}</td>
								@elseif($row->movement->mvt_mfg_type_id != 1)
									<td>{{ $row->movement->mfgType->name }}</td>
								@elseif($row->movement->mvt_exp_type_id != 1)
									<td>{{ $row->movement->expType->name }}</td>
								@else
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
@endsection
