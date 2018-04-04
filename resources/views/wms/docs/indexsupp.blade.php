@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

<?php $sRoute='wms.docs'?>

@section('content')
  @section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index', $iDocCategory, $iDocClass, $iDocType, $iViewType, $title],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
		    <div class="input-group">
					@include('templates.list.search')
					{{-- <span class="input-group-btn">
					  {!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']); !!}
					</span> --}}
			    <span class="input-group-btn">
			        <button id="searchbtn" type="submit" class="form-control">
								<span class="glyphicon glyphicon-search"></span>
							</button>
					</span>
		    </div>
			</div>
		{!! Form::close() !!}
  @endsection
	<div class="row">
		<table id="docTable" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Folio</th>
		            <th data-priority="2">Fecha</th>
								<th>ID ERP</th>
								<th data-priority="2">Ref.</th>
		            <th data-priority="1">Asociado de negocios</th>
		            <th>RFC</th>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
									<th>Cantidad</th>
			            <th>Cant. procesada</th>
			            <th>Avance %</th>
									<th>Ver</th>
								@else
									<th>Cve m/p</th>
									<th>Mat/Prod</th>
									<th>Cantidad</th>
									<th>Surtida</th>
									<th>Avance %</th>
									<th>Un.</th>
								@endif
								<th>Cerrado</th>
								<th>Abrir</th>
								<th>Status</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($documents as $doc)
						<tr>
                <td class="small">{{ $doc->num }}</td>
                <td class="small">{{ $doc->dt_doc }}</td>
                <td class="small">{{ $doc->external_id }}</td>
								<td class="small">{{ $doc->num_src }}</td>
		            <td class="small">{{ $doc->name }}</td>
		            <td class="small">{{ $doc->cve_an }}</td>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_doc, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_sur, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber((($doc->qty_sur + $doc->qty_sur_ind) * 100)/$doc->qty_doc, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td>
										<a href="{{ route('siie.docs.view', $doc->id_document) }}" title="Ver documento"
																																class="btn btn-info btn-sm">
											<span class=" glyphicon glyphicon-eye-open" aria-hidden = "true"/>
										</a>
									</td>
								@else
									<td class="small">{{ $doc->cve_item }}</td>
									<td class="small">{{ $doc->item }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_row, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_sur + $doc->qty_sur_ind, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber((($doc->qty_sur + $doc->qty_sur_ind) * 100)/$doc->qty_row, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td class="small">{{ $doc->unit }}</td>
								@endif
								<td>
									@if (! $doc->is_closed)
											<span class="label label-info">{{ trans('wms.labels.OPENED') }}</span>
									@else
											<span class="label label-warning">{{ trans('wms.labels.CLOSED') }}</span>
									@endif
								</td>
								<td>
									<a href="{{ route('wms.docs.openclose', [\Config::get('scsiie.DOC_OPER.OPEN'), $doc->id_document]) }}" title="Abrir para surtido"
																															class="btn btn-default btn-sm">
										<span class="glyphicon glyphicon-ok-circle" aria-hidden = "true"/>
									</a>
								</td>
								<td class="small">
									@if (! $doc->is_deleted)
										<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
									@else
										<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
									@endif
								</td>
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
	<script src="{{ asset('js/docs/docsbysupp.js') }}"></script>
@endsection
