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

  <?php $sRoute="wms.pallets"?>

  @section('filters')
    {!! Form::open(['route' => array($sRoute.'.index', 0,''),
      'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
      <div class="form-group">
        <div class="input-group">
          @include('templates.list.search')
          <span class="input-group-btn">
            {!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']) !!}
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
	@section('create')
		@include('templates.form.create')
	@endsection

	@if(isset($iId))
			@if($iId>0)
				<label>Imprimir etiquetas de tarima <?php echo $iId ?> : </label>
				<a href="{{ route('wms.pallets.print', $iId) }}" 
						target="_blank" class="btn btn-success btn-xs">	
					<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
				</a>
			@endif
	@endif

	<table data-toggle="table" id="catalog_table" class="table table-striped display nowrap" style="width:100%">
		<thead>
			<th>ID</th>
			{{-- <th>{{ trans('wms.labels.PALLET') }}</th> --}}
			<th>{{ trans('wms.labels.MAT_PROD') }}</th>
			<th>{{ trans('userinterface.labels.UNIT') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
			<th>{{'Etiqueta'}}</th>
			<th>Creación</th>
			<th>Usr Creación</th>
		</thead>
		<tbody>
			@foreach($pallets as $pallet)
				<tr>
					<td>{{ session('utils')->formatPallet($pallet->id_pallet) }}</td>
					{{-- <td>{{ $pallet->pallet }}</td> --}}
					<td>{{ $pallet->item_code.'-'.$pallet->item }}</td>
					<td>{{ $pallet->unit_code.'-'.$pallet->unit }}</td>
					<td>
						@if (! $pallet->is_deleted)
								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
						@else
								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
						@endif
					</td>
					<td>
						<?php
								$oRegistry = $pallet;
								$iRegistryId = $pallet->id_pallet;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
					<td>
						<a href="{{ route('wms.pallets.barcode', $pallet->id_pallet) }}" target="_blank" class="btn btn-success btn-xs">
							<span class="glyphicon glyphicon-save" aria-hidden="true"></span>
						</a>
					</td>
					<td>{{ $pallet->created_at }}</td>
					<td>{{ $pallet->usr_creation }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
@section('js')
	<script src="{{ asset('moment/moment.js') }}"></script>
	<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
	
	<script>
		var iPallet = <?php echo json_encode($iId); ?>;
		var sItem = <?php echo json_encode($sItem); ?>;

		if (iPallet != 0 && sItem != '') {
				swal(
						'Tarima: ' + iPallet,
						'Se ha creado para: ' + sItem,
						'success',
					);
		}
		// new Vue({ el: '#vue-table' })

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

		var oCatalogTable = $('#catalog_table').DataTable({
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
		},
		"scrollX": true,
		"colReorder": true
	});

		//  $(document).ready(function() {
		// 	oTable = $('#task').DataTable({
		// 		"processing": true,
		// 		"serverSide": true,
		//  	"ajax": "route('wms.pallets.serverside')", //agregar llaves dobles de php para que imprima la ruta
		// 		"columns": [
		// 			{data: 'id_pallet'},
		// 			{ "defaultContent": "<button>Click!</button>"}
		// 		]
		// 	});
		// });
	</script>
@endsection
