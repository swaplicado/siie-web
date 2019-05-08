@include('templates.head')
@extends('templates.basic_form')


@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.SEGREGATE_PALLET'))
@section('titlepanel', trans('userinterface.titles.SEGREGATE_PALLET'))



@section('content')
  <div class="row">

    <div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

        <div class="form-group row">

          {!! Form::open(['route' => 'wms.codes.decodePallet', 'method' => 'POST']) !!}

          {!! Form::label('codigo', trans('Codigo de Barras o "t" seguido del número de tarima. Ej. t948:'),['class'=>'col-md-2 control-label']) !!}

          <div class="col-md-3">

            {!! Form::text('codigo', null, ['class'=>'form-control', 'placeholder' => 'Ingresa codigo de barras...', 'required']) !!}

          </div>

          <div class="col-md-3">

            {!! Form::submit('Consultar', ['class' => 'btn btn-primary']) !!}

          </div>

        </div>

      </div>

    </div>

  </div>




@endsection

@section('js')

	<script type="text/javascript">


	</script>

  <script type="text/javascript">


  </script>

	@endsection
