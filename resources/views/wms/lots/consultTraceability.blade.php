@extends('templates.basic_form')
@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', 'Trazabilidad de lotes con  codigo de barras')

@section('content')
  <div class="row">

    <div class="form-group">
      <div class="form-group row"></div>
      <div class="col-md-12">
        <div class="form-group row">
          {!! Form::open(['route' => 'wms.traceability.gettraceability', 'method' => 'POST']) !!}
          {!! Form::label('codigo', trans('Codigo de Barras:'),['class'=>'col-md-2 control-label']) !!}
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
	@endsection
