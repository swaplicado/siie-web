@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

<?php $sRoute='qms.segregations'?>

@section('content')
	@section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index'],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-btn">
						{!! Form::date('filterDate', $tFilterDate, ['class'=>'form-control']) !!}
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
	@include('wms.segregations.classification')
	<br />
	<div class="row">
		<table id="table_seg" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th>-</th>
		            <th data-priority="1">Clave</th>
		            <th>Item</th>
		            <th data-priority="1">Unidad</th>
		            <th>Lote</th>
		            <th>Tarima</th>
		            <th data-priority="1">Segregado</th>
		            <th>Almacén</th>
		            <th data-priority="1">{{ trans('userinterface.labels.STATUS') }}</th>
		            <th>Doc</th>
								@if ($iQualityType == \Config::get('scqms.QMS_VIEW.CLASSIFY'))
									<th>-</th>
								@endif
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <td>{{ $row->id_item }}</td>
		            <td>{{ $row->id_unit }}</td>
		            <td>{{ $row->id_lot }}</td>
		            <td>{{ $row->id_pallet }}</td>
		            <td>{{ $row->id_whs }}</td>
		            <td>{{ $row->branch_id }}</td>
		            <td>{{ $row->id_document }}</td>
		            <td>{{ $row->id_status }}</td>
		            <td>{{ $row->item_code }}</td>
		            <td>{{ $row->item }}</td>
		            <td>{{ $row->unit }}</td>
		            <td>{{ $row->lot_name }}</td>
		            <td>{{ $row->pallet }}</td>
		            <td>{{ $row->segregated }}</td>
		            <td>{{ $row->warehouse }}</td>
		            <td>
									<span class="{{ App\SUtils\SGuiUtils::getClassOfStatus($row->id_status) }}">
										{{ $row->status_qlty }}
									</span>
								</td>
								<td>{{ $row->num_doc }}</td>
								@if ($iQualityType == \Config::get('scqms.QMS_VIEW.CLASSIFY'))
									<td>
										<a data-toggle="modal" data-target="#classQlty"
												title="Evaluar material/producto"
												onclick="classificateUnits(this)"
												class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-search" aria-hidden = "true"/>
										</a>
									</td>
								@endif
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script src="{{ asset('js/segregation/segregation.js')}}"></script>
	<script src="{{ asset('js/segregation/segregations_table.js')}}"></script>
@endsection
