@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_STOCK'))

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
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'))
									<th>Tarima</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'))
		            	<th>Lote</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION'))
			            <th>Ubicación</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE') ||
								 			$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE'))
			            <th>Almacén</th>
								@endif
		            <th>Entradas</th>
		            <th>Salidas</th>
		            <th>Existencia</th>
								<th>Unidad</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <td>{{ $row->item_code }}</td>
		            <td>{{ $row->item }}</td>
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'))
									<td>{{ $row->pallet == 'N/A' ? 'SIN TARIMA' : $row->pallet }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT'))
									<td>{{ $row->lot_ }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION'))
									<td>{{ $row->location }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
													$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE'))
									<td>{{ $row->warehouse }}</td>
								@endif
		            <td align="right">{{ session('utils')->formatNumber($row->inputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->outputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->stock, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td align="right">{{ $row->unit }}</td>
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