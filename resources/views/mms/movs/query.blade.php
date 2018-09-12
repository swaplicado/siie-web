@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', $sTitle)

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', $sTitle)

@section('content')

  <?php $sRoute="mms.movs"?>

  @section('filters')
    {!! Form::open(['route' => [$sRoute.'.show', $iQueryType, $sTitle],
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
		<table id="query_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
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
								<th>Ord. Prod.</th>
								<th>{{ trans('siie.labels.CREATED') }}</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($lMovs as $row)
						<tr>
								<td>{{ $row->mov_code.'-'.session('utils')->formatFolio($row->mov_folio) }}</td>
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
								@if($row->mvt_exp_type_id > 1)
									<td>{{ $row->exp_name }}</td>
								@else
									<td>N/A</td>
								@endif
								@if ($row->prod_ord_id == 1)
									<td>N/A</td>
								@else
									<td>{{ 'OP-'.session('utils')->formatFolio($row->po_folio) }}</td>
								@endif
								<td>{{ $row->created_at }}</td>
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	<script src="{{ asset('moment/moment.js') }}"></script>
	<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
	<script src="{{ asset('js/mms/movs/tables.js') }}"></script>
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

@section('footer')
    @include('templates.footer')
@endsection
