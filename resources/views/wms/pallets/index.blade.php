@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('addfilters')
	@include('templates.list.search')
@endsection

@section('title', 'Tarimas')

@section('content')
	<?php $sRoute="wms.pallets"?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" id="catalog_table" class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
		<thead>
			<th>ID</th>
			{{-- <th>{{ trans('wms.labels.PALLET') }}</th> --}}
			<th>{{ trans('wms.labels.MAT_PROD') }}</th>
			<th>{{ trans('userinterface.labels.UNIT') }}</th>
			<th>{{ trans('userinterface.labels.STATUS') }}</th>
			<th>{{ trans('userinterface.labels.ACTION') }}</th>
			<th>{{'Etiqueta'}}</th>
		</thead>
		<tbody>
			@foreach($pallets as $pallet)
				<tr>
					<td>{{ $pallet->id_pallet }}</td>
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
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection
@section('js')
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
