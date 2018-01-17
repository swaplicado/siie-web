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
					{{-- <span>
						<input id="filterDate" type="text" class="form-control" value="{{ $sFilterDate }}" />
					</span> --}}
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
		<table id="docTable" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
								<th data-priority="1">Folio</th>
		            <th data-priority="2">Fecha</th>
								<th>ID ERP</th>
		            <th data-priority="1">Asociado de negocios</th>
		            <th>Cve AN</th>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
									<th>Cantidad</th>
			            <th>Cant. procesada</th>
			            <th>Avance %</th>
			            <th>Cant. pendiente</th>
									<th>Status</th>
									<th>Ver</th>
									@if ($iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') && $iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'))
										<th data-priority="1">Devolver</th>
									@else
										<th data-priority="1">Surtir</th>
									@endif
								@else
									<th>Cve m/p</th>
									<th>Mat/Prod</th>
									<th>Cantidad</th>
									<th>Surtida</th>
									<th>Avance %</th>
									<th>Pendiente</th>
									<th>Un.</th>
									<th>Status</th>
								@endif
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($documents as $doc)
						<tr>
                {{-- <td class="small">{{ \Carbon\Carbon::parse($doc->dt_date)->format('d-m-Y') }}</td>
                <td class="small">{{ \Carbon\Carbon::parse($doc->dt_doc)->format('d-m-Y') }}</td> --}}
                <td class="small">{{ $doc->num }}</td>
                <td class="small">{{ $doc->dt_doc }}</td>
                <td class="small">{{ $doc->external_id }}</td>
		            <td class="small">{{ $doc->name }}</td>
		            <td class="small">{{ $doc->cve_an }}</td>
								@if ($iViewType == Config::get('scwms.DOC_VIEW.NORMAL'))
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_doc, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_sur, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->advance, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->pending, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td class="small">
										@if (! $doc->is_deleted)
												<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
										@else
												<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
										@endif
									</td>
									<td>
										<a href="{{ route('siie.docs.view', $doc->id_document) }}" title="Ver documento"
																																class="btn btn-info btn-sm">
											<span class=" glyphicon glyphicon-eye-open" aria-hidden = "true"/>
										</a>
									</td>
									<td>
										{{-- {{ dd($doc->id_document) }} --}}
											<?php
												if ($iDocCategory == \Config::get('scsiie.DOC_CAT.PURCHASES')) {
														if ($iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') && $iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE')) {
																$iMvtInvType = \Config::get('scwms.MVT_TP_OUT_PUR');
														}
														else {
																$iMvtInvType = \Config::get('scwms.MVT_TP_IN_PUR');
														}
												}
												else {
														if ($iDocClass == \Config::get('scsiie.DOC_CLS.ADJUST') && $iDocType == \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE')) {
																$iMvtInvType = \Config::get('scwms.MVT_TP_IN_SAL');
														}
														else {
																$iMvtInvType = \Config::get('scwms.MVT_TP_OUT_SAL');
														}
												}
										 ?>
										<a href="{{ route('wms.movs.supply', [$iMvtInvType, $doc->id_document]) }}" title="Surtir documento"
																																class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-import" aria-hidden = "true"/>
										</a>
									</td>
								@else
									<td class="small">{{ $doc->cve_item }}</td>
									<td class="small">{{ $doc->item }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_row, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->qty_sur, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->advance, \Config::get('scsiie.FRMT.QTY')) }}</td>
			            <td class="small" align="right">{{ session('utils')->formatNumber($doc->pending, \Config::get('scsiie.FRMT.QTY')) }}</td>
									<td class="small">{{ $doc->unit }}</td>
									<td class="small">
										@if (! $doc->is_deleted)
												<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
										@else
												<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
										@endif
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
	<script src="{{ asset('moment/moment.js') }}"></script>
	<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
	<script>
	// $.fn.dataTable.ext.search.push(
	// 		function( settings, data, dataIndex ) {
	// 				var filter = parseInt( $('#filter').val(), 10 );
	// 				var deleted = parseInt( data[12], 10 ) || 0; // use data for the age column
	// 				// var deleted = 2; // use data for the age column
	//
	// 				if ( filter == 1 && deleted == 1 ||
	// 						 filter == 2 && deleted == 0 ||
	// 					 	 filter == 3)
	// 				{
	// 						return true;
	// 				}
	// 				return false;
	// 		}
	// 	);

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

		$('#docTable').DataTable({
		      "language": {
		        "sProcessing":     "Procesando...",
		        "sLengthMenu":     "Mostrar _MENU_ registros",
		        "sZeroRecords":    "No se encontraron resultados",
		        "sEmptyTable":     "Ningún dato disponible en esta tabla",
		        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
		        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
		        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		        "sInfoPostFix":    "",
		        "sSearch":         "Buscar:",
		        "sUrl":            "",
		        "sInfoThousands":  ",",
		        "sLoadingRecords": "Cargando...",
		        "oPaginate": {
		            "sFirst":    "Primero",
		            "sLast":     "Último",
		            "sNext":     "Siguiente",
		            "sPrevious": "Anterior"
		        },
		        "oAria": {
		            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		        }
		      }
		  });

		  $(document).ready( function () {
		    var table = $('#docTable').DataTable();

		    // Event listener to the two range filtering inputs to redraw on input
		    // document.getElementById('filter').addEventListener("change", function() {
		    //     table.draw();
		    // });
		    // $('#filter').change( function() {
		    //     table.draw();
		    // });
		  });
	</script>
@endsection
