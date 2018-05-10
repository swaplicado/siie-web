@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

<?php $sRoute="wms.stock"?>

@section('content')
	@section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index', $iStockType, $sTitle],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
		    <div class="input-group">
					<span class="input-group">
						{!! Form::select('warehouse', $lWarehouses, $iFilterWhs,
															['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.WAREHOUSE')]) !!}
					</span>
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
		<table id="stock_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th data-priority="1">{{ trans('userinterface.labels.CODE') }}</th>
		            <th data-priority="1">{{ trans('wms.labels.MAT_PROD') }}</th>
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
									<th data-priority="1">Tarima</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
		            	<th data-priority="1">Lote</th>
		            	<th data-priority="1">Vencimiento</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION') ||
								$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
			            <th data-priority="1">Ubicación</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE') ||
								 			$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
			            <th data-priority="1">Almacén</th>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'))
			            <th data-priority="1">Sucursal</th>
								@endif
								<th data-priority="1">Disponible</th>
								<th data-priority="1">Existencia</th>
		            <th data-priority="2">Entradas</th>
		            <th data-priority="2">Salidas</th>
		            <th data-priority="1">Segregado</th>
								<th data-priority="1">Unidad</th>
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'))
									<th>Semaforo</th>
									<th>Maximo</th>
			            <th>Minimo</th>
									<th>Reorden</th>
								@endif


		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <td>{{ $row->item_code }}</td>
		            <td>{{ $row->item }}</td>
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
									<td>{{ $row->pallet == '1' ? 'SIN TARIMA' : $row->pallet }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET_BY_LOT') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
									<td>{{ $row->lot_ }}</td>
									<td>{{ $row->dt_expiry }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOCATION') ||
								$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
									<td>{{ $row->location }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE') ||
												$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_PALLET') ||
													$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
													$iStockType == \Config::get('scwms.STOCK_TYPE.STK_GENERAL'))
									<td>{{ $row->warehouse }}</td>
								@endif
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'))
									<td>{{ $row->branch_ }}</td>
								@endif
								<td align="right">{{ session('utils')->formatNumber(($row->stock - $row->segregated), \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td align="right">{{ session('utils')->formatNumber($row->stock, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->inputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->outputs, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td align="right">{{ session('utils')->formatNumber($row->segregated, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td align="right">{{ $row->unit }}</td>
								@if ($iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_LOT_BY_WAREHOUSE') ||
											$iStockType == \Config::get('scwms.STOCK_TYPE.STK_BY_BRANCH'))

											@if ($row->maxi==null)
											 <td align="right">{{ 'N/A'}}</td>
										 	@else
												@if ($row->stock<=$row->mini)
													<td align="center"><button class="btn btn-danger "><span class="glyphicon glyphicon-unchecked"></span></button></td>
												@endif
												@if ($row->stock<=$row->reorder && $row->stock>$row->mini)
													<td align="center"><button class="btn btn-warning "><span class="glyphicon glyphicon-unchecked"></span></button></td>
												@endif
												@if ($row->stock>$row->reorder && $row->stock<$row->maxi)
													<td align="center"><button class="btn btn-success "><span class="glyphicon glyphicon-unchecked"></span></button></td>
												@endif
												@if ($row->stock>$row->maxi)
													<td align="center"><button class="btn btn-info "><span class="glyphicon glyphicon-alert"></span></button></td>
												@endif

											@endif

											@if ($row->maxi==null)
												<td align="right">{{ 'N/A'}}</td>
											@else
												<td align="right">{{ $row->maxi}}</td>
											@endif

											@if ($row->mini==null)
												<td align="right">{{ 'N/A'}}</td>
											@else
												<td align="right">{{ $row->mini}}</td>
											@endif

											@if ($row->reorder==null)
												<td align="right">{{ 'N/A'}}</td>
											@else
												<td align="right">{{ $row->reorder}}</td>
											@endif
								@endif

		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
@endsection
