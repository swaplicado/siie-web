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
		{!! Form::open(['route' => [ $sRoute.'.index', $iDocCategory, $iDocClass, $iDocType, $iViewType, $iSuppType, $title],
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
	<div class="row">
		<table id="docTable" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">{{ trans('wms.labels.FOLIO') }}</th>
		            <th data-priority="2">{{ trans('siie.labels.DATE') }}</th>
								<th>ID ERP</th>
								<th data-priority="2">{{ trans('wms.labels.REF') }}</th>
		            <th data-priority="1">{{ trans('siie.labels.BUSINESS_ASSOCIATE') }}</th>
		            <th>{{ trans('siie.labels.FISCAL_ID') }}</th>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
									<th>{{ trans('siie.labels.QUANTITY') }}</th>
			            <th>Cant. procesada</th>
			            <th>Avance %</th>
									<th>{{ trans('actions.SEE') }}</th>
								@else
									<th>Cve m/p</th>
									<th>{{ trans('wms.labels.MAT_PROD') }}</th>
									<th>{{ trans('siie.labels.QUANTITY') }}</th>
									<th>{{ trans('siie.labels.SUPPLIED') }}</th>
									<th>Avance %</th>
									<th>{{ trans('wms.labels.UN') }}</th>
								@endif
								<th>Cerrado</th>
								<th>{{ trans('actions.OPEN') }}</th>
								<th>Status</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($documents as $doc)
						<tr>
                <td class="small">{{ $doc->folio }}</td>
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
