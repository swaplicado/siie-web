@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_MOVEMENTS'))

<?php $sRoute="wms.stock"?>

@section('content')
	{{-- <div class="row">
			@include('templates.stock.filterstock')
	</div> --}}
	<br />
	<div class="row">
		<table id="table_id" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th>Clave</th>
		            <th>Item</th>
								<th>Entradas</th>
								<th>Salidas</th>
								<th>Unidad</th>
		            <th>Sucursal</th>
		            <th>Almacén</th>
		            <th>Tipo movimiento</th>
		            <th>Tipo</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($rows as $row)
						<tr>
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
								<td>{{ $row->movement->whs->name }}</td>
								<td>{{ $row->movement->mvtType->name }}</td>
								@if ($row->movement->mvt_trn_type_id != 1)
									<td>{{ $row->movement->trnType->name }}</td>
								@elseif($row->movement->mvt_adj_type_id != 1)
									<td>{{ $row->movement->adjType->name }}</td>
								@elseif($row->movement->mvt_mfg_type_id != 1)
									<td>{{ $row->movement->mfgType->name }}</td>
								@elseif($row->movement->mvt_exp_type_id != 1)
									<td>{{ $row->movement->expType->name }}</td>
								@endif
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script>

	</script>
@endsection
