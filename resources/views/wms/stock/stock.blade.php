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
	@section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index', $iStockType],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
		    <div class="input-group">
					<span class="input-group-btn">
					  {!! Form::date('filterDate', $tfilterDate, ['class'=>'form-control']) !!}
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
		            <th data-priority="1">Clave</th>
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
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'))
			            <th>Sucursal</th>
								@endif
		            <th>Entradas</th>
		            <th>Salidas</th>
		            <th data-priority="1">Existencia</th>
		            <th data-priority="1">Segregado</th>
		            <th data-priority="1">Disponible</th>
								<th data-priority="1">Unidad</th>
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
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'))
									<td>{{ $row->branch_ }}</td>
								@endif
		            <td align="right">{{ session('utils')->formatNumber($row->inputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->outputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->stock, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->segregated, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber(($row->stock - $row->segregated), \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td align="right">{{ $row->unit }}</td>
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
@endsection
