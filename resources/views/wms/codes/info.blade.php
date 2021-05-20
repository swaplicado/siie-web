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
	</style>
@endsection

@section('content')

@if ($type == 1)
	<div class="row">
		<div class="row">
			<div class="col-md-offset-3 col-md-3">
				<label class="labelName vertical-center">LOTE:</label>
			</div>
			<div class="col-md-4">
				<label class="textContent vertical-center">{{ $info->lot }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Caducidad:</label>
			</div>
			<div class="col-md-9">
				<label class="text-content-others vertical-center">{{ $info->dt_expiry }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">ítem:</label>
			</div>
			<div class="col-md-9">
				<label class="text-content-others vertical-center">{{ $info->item->code.' - '.$info->item->name }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Unidad:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center">{{ $info->unit->code.' - '.$info->unit->name }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Existencias:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center right">{{ session('utils')->formatNumber($stock[\Config::get('scwms.STOCK.GROSS')], \Config::get('scsiie.FRMT.QTY')) }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Segregado:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center right">{{ session('utils')->formatNumber($stock[\Config::get('scwms.STOCK.SEGREGATED')], \Config::get('scsiie.FRMT.QTY')) }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Disponible:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center right">{{ session('utils')->formatNumber($stock[\Config::get('scwms.STOCK.AVAILABLE')], \Config::get('scsiie.FRMT.QTY')) }}</label>
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

@if ($type == 2)
	<div class="row">
		<div class="row">
			<div class="col-md-offset-3 col-md-3">
				<label class="labelName vertical-center">ID TARIMA:</label>
			</div>
			<div class="col-md-4">
				<label class="textContent vertical-center">{{ $info->pallet }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">ítem:</label>
			</div>
			<div class="col-md-9">
				<label class="text-content-others vertical-center">{{ $info->item->code.' - '.$info->item->name }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Unidad:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center">{{ $info->unit->code.' - '.$info->unit->name }}</label>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Existencias:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center right">{{ session('utils')->formatNumber($stock[0], \Config::get('scsiie.FRMT.QTY')) }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Segregado:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center right">{{ session('utils')->formatNumber($stock[\Config::get('scwms.STOCK.SEGREGATED')], \Config::get('scsiie.FRMT.QTY')) }}</label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-offset-1 col-md-2">
				<label class="label-others vertical-center">Disponible:</label>
			</div>
			<div class="col-md-7">
				<label class="text-content-others vertical-center right">{{ session('utils')->formatNumber($stock[\Config::get('scwms.STOCK.AVAILABLE')], \Config::get('scsiie.FRMT.QTY')) }}</label>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="row">
				{!! Form::label('unit_id', 'Lotes:',['class'=>'col-md-offset-1 col-md-2 control-label label-others']) !!}
			</div>
			<br>
			<br>
			@foreach ($lotStock as $stk)
				<div class="row">
					<div class="col-md-offset-1 col-md-3">						
						<label class="label-others vertical-center">{{ $stk->lot }}</label>
					</div>
					<div class="col-md-4">						
						<label class="text-content-others vertical-center right">{{ $stk->stock }}</label>
					</div>
				</div>
			@endforeach
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


	</script>

  <script type="text/javascript">


  </script>

	@endsection
