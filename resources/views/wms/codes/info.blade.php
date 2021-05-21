@extends('templates.basic_form')
@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', 'Consultar codigo de barras')
@section('titlepanel', 'Resultados de búsqueda')

@section('head')
	<style>
		.vertical-center {
			margin: 0;
			position: relative;
			top: 50%;
			-ms-transform: translateY(-50%);
			transform: translateY(-50%);
		}
		.txt-center {
			text-align: center;
		}
		.labelName {
			font: 30px sans-serif;
			padding: 5px;
			color: rgb(7, 7, 7);
		}
		.textContent {
			font: 50px sans-serif;
			padding: 5px;
			color: rgb(32, 84, 241);
		}
		.label-others {
			font: 20px sans-serif;
			padding: 5px;
			color: rgb(7, 7, 7);
		}
		.text-content-others {
			font: 30px sans-serif;
			padding: 5px;
			color: rgb(41, 5, 5);
		}
		.right {
			text-align: right;
		}
		.center-div {
			padding: 5px 0;
			text-align: center;
		}
	</style>
@endsection

@section('content')

@if ($type == 1)
	<div class="row">
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="labelName vertical-center">LOTE:</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="textContent vertical-center">{{ $info->lot }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center">Caducidad:</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center">{{ $info->dt_expiry }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center">ítem:</label>
			</div>
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center">{{ $info->item->code.' - '.$info->item->name }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center">Unidad:</label>
			</div>
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center">{{ $info->unit->code.' - '.$info->unit->name }}</label>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<table id="stockTable" class="table table-striped table-bordered display no-wrap" cellspacing="0" style="width:100%">
					<thead>
						<th>Sucursal</th>
						<th>Almacén</th>
						<th>Tarima</th>
						<th>Existencias</th>
						<th>Segregado</th>
						<th>Disponible</th>
						<th>Unidad</th>
					</thead>
					<tbody>
						@foreach ($stock as $stk)
							<tr>
								<td>{{ $stk->branch }}</td>
								<td>{{ $stk->warehouse }}</td>
								<td>{{ $stk->pallet_id }}</td>
								<td>{{ session('utils')->formatNumber($stk->stock, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td>{{ session('utils')->formatNumber($stk->segregated, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td>{{ session('utils')->formatNumber(($stk->stock - $stk->segregated), \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td>{{ $stk->unit_code }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group">
			<div class="form-group row"></div>
			<div class="col-md-12">
				<div class="form-group row">
					<div class="col-md-3">
						<input type="button" name="Regresar" value="Regresar" class="btn btn-danger" onClick="location.href='{{ route('wms.codes.consult') }}'">
					</div>
				</div>
			</div>
		</div>
	</div>
@endif

@if ($type == 2)
	<div class="row">
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="labelName vertical-center txt-center">ID TARIMA:</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="textContent vertical-center txt-center">{{ $info->pallet }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center txt-center">ítem:</label>
			</div>	
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center txt-center">{{ $info->item->code.' - '.$info->item->name }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center txt-center">Unidad:</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center txt-center">{{ $info->unit->code.' - '.$info->unit->name }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center txt-center">Sucursal:</label>
			</div>
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center txt-center">{{ $stock[0]->branch }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 center-div">
				<label class="label-others vertical-center txt-center">Almacén:</label>
			</div>
			<div class="col-md-12 center-div">
				<label class="text-content-others vertical-center txt-center">{{ $stock[0]->warehouse }}</label>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12">
				<table id="stockTable" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
					<thead>
						<th>Lote</th>
						<th>Existencias</th>
						<th>Segregado</th>
						<th>Disponible</th>
						<th>Unidad</th>
					</thead>
					<tbody>
						@foreach ($stock as $stk)
							<tr>
								<td>{{ $stk->lot }}</td>
								<td>{{ session('utils')->formatNumber($stk->stock, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td>{{ session('utils')->formatNumber($stk->segregated, \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td>{{ session('utils')->formatNumber(($stk->stock - $stk->segregated), \Config::get('scsiie.FRMT.QTY')) }}</td>
								<td>{{ $stk->unit_code }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
		<br>
		<div class="form-group">
			<div class="form-group row"></div>
			<div class="col-md-12">
				<div class="form-group row">
					<div class="col-md-3">
						<input type="button" name="Regresar" value="Regresar" class="btn btn-danger" onClick="location.href='{{ route('wms.codes.consult') }}'">
					</div>
				</div>
			</div>
		</div>
	</div>

@endif
@if ($type == 3)
<div class="form-group">

	<div class="form-group row"></div>

	<div class="col-md-12">

		<div class="form-group row">

			{!! Form::label('id_whs_location', 'Id Ubicacion',['class'=>'col-md-2 control-label']) !!}

			<div class="col-md-3">

				{!! Form::text('id_whs_location', $info->id_whs_location, ['class'=>'form-control' , 'disabled']) !!}

			</div>

			{!! Form::label('location', 'Nombre Ubicacion',['class'=>'col-md-2 control-label']) !!}

			<div class="col-md-3">

				{!! Form::text('location', $info->name, ['class'=>'form-control' , 'disabled']) !!}

			</div>

		</div>

	</div>

</div>


<div class="form-group">

	<div class="form-group row"></div>

	<div class="col-md-12">

		<div class="form-group row">

			<div class="col-md-3">

				<input type="button" name="Regresar" value="Regresar" class="btn btn-danger" onClick="location.href='{{ route('wms.codes.consult') }}'">

			</div>

		</div>

	</div>

</div>



@endif
@endsection

@section('js')

	<script type="text/javascript">
		$(document).ready( function () {
			$('#stockTable').DataTable();
		} );
	</script>

	@endsection
