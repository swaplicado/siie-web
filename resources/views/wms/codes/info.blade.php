@extends('templates.basic_form')
@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', 'Consultar codigo de barras')

@section('content')

@if ($type == 1)
    <div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

      	<div class="form-group row">




          {!! Form::label('id_lot', 'Id Lote',['class'=>'col-md-2 control-label']) !!}

          <div class="col-md-3">

            {!! Form::text('id_lot', $info->id_lot, ['class'=>'form-control' , 'disabled']) !!}

          </div>

					{!! Form::label('lot', 'Lote',['class'=>'col-md-2 control-label']) !!}

					<div class="col-md-3">

						{!! Form::text('lot', $info->lot, ['class'=>'form-control' , 'disabled']) !!}

					</div>



        </div>

      </div>

    </div>

		<div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

      	<div class="form-group row">




          {!! Form::label('dt_expiry', 'Fecha de caducidad',['class'=>'col-md-2 control-label']) !!}

          <div class="col-md-3">

            {!! Form::text('dt_expiry', $info->dt_expiry, ['class'=>'form-control' , 'disabled']) !!}

          </div>

					{!! Form::label('item_id', 'Item',['class'=>'col-md-2 control-label']) !!}

					<div class="col-md-3">

						{!! Form::text('item_id', $info->item->name, ['class'=>'form-control' , 'disabled']) !!}

					</div>



        </div>

      </div>

    </div>

		<div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

      	<div class="form-group row">

          {!! Form::label('unit_id', '	Unidad',['class'=>'col-md-2 control-label']) !!}

          <div class="col-md-3">

            {!! Form::text('unit_id', $info->unit->name, ['class'=>'form-control' , 'disabled']) !!}

          </div>

					{!! Form::label('existencia', 'Existencias',['class'=>'col-md-2 control-label']) !!}

					<div class="col-md-3">

						{!! Form::text('existencia', $stock[0], ['class'=>'form-control' , 'disabled']) !!}

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

	@if ($type == 2)
	<div class="form-group">

		<div class="form-group row"></div>

		<div class="col-md-12">

			<div class="form-group row">




				{!! Form::label('id_pallet', 'Id Tarima',['class'=>'col-md-2 control-label']) !!}

				<div class="col-md-3">

					{!! Form::text('id_pallet', $info->id_pallet, ['class'=>'form-control' , 'disabled']) !!}

				</div>

				{!! Form::label('pallet', 'Tarima',['class'=>'col-md-2 control-label']) !!}

				<div class="col-md-3">

					{!! Form::text('pallet', $info->pallet, ['class'=>'form-control' , 'disabled']) !!}

				</div>



			</div>

		</div>

	</div>

	<div class="form-group">

		<div class="form-group row"></div>

		<div class="col-md-12">

			<div class="form-group row">




				{!! Form::label('quantity', 'Cantidad',['class'=>'col-md-2 control-label']) !!}

				<div class="col-md-3">

					{!! Form::text('quantity', $info->quantity, ['class'=>'form-control' , 'disabled']) !!}

				</div>

				{!! Form::label('item_id', 'Item',['class'=>'col-md-2 control-label']) !!}

				<div class="col-md-3">

					{!! Form::text('item_id', $info->item->name, ['class'=>'form-control' , 'disabled']) !!}

				</div>



			</div>

		</div>

	</div>

	<div class="form-group">

		<div class="form-group row"></div>

		<div class="col-md-12">

			<div class="form-group row">

				{!! Form::label('unit_id', '	Unidad',['class'=>'col-md-2 control-label']) !!}

				<div class="col-md-3">

					{!! Form::text('unit_id', $info->unit->name, ['class'=>'form-control' , 'disabled']) !!}

				</div>

				{!! Form::label('existencia', 'Existencias',['class'=>'col-md-2 control-label']) !!}

				<div class="col-md-3">

					{!! Form::text('existencia', $stock[0], ['class'=>'form-control' , 'disabled']) !!}

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
