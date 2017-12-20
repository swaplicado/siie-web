@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

<?php $sRoute='siie.docs'?>

@section('content')
  @section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index', $iDocCategory, $iDocClass], 'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
		    <div class="input-group">
					@include('templates.list.search')
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
		            <th data-priority="2">Fecha</th>
		            <th data-priority="1">Fecha documento</th>
		            <th data-priority="1">Asociado de negocios</th>
		            <th data-priority="2">RFC</th>
		            <th data-priority="1">Núm.</th>
		            <th data-priority="2">ID ERP</th>
		            <th>Subtotal</th>
		            <th>Total</th>
		            <th>TC</th>
		            <th>Subotal $</th>
		            <th>Total $</th>
		            <th>Mon.</th>
		            <th>Status</th>
		            <th>Ver</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($documents as $doc)
						<tr>
                <td class="small">{{ \Carbon\Carbon::parse($doc->dt_date)->format('d-m-Y') }}</td>
                <td class="small">{{ \Carbon\Carbon::parse($doc->dt_doc)->format('d-m-Y') }}</td>
		            <td class="small">{{ $doc->name }}</td>
		            <td class="small">{{ $doc->fiscal_id }}</td>
		            <td class="small">{{ $doc->num }}</td>
		            <td class="small">{{ $doc->external_id }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($doc->subtotal, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($doc->total, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($doc->exchange_rate, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($doc->subtotal_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($doc->total_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small">{{ $doc->cur_code }}</td>
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
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
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
