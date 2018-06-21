@extends('templates.basic_form')
@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

@section('content')
  <div class="row">
    <div class="form-group">
      <div class="form-group row"></div>
      <div class="col-md-12">
        <div class="form-group row">
          <?php
          switch ($type) {
            case 1:
          ?>
          {!! Form::open(['route' => 'qms.segregations.toQuarentine', 'method' => 'POST']) !!}

          <?php
              break;
            case 2:
          ?>
          {!! Form::open(['route' => 'qms.segregations.toRelease', 'method' => 'POST']) !!}
          <?php
              break;
            case 3:
          ?>
          {!! Form::open(['route' => 'qms.segregations.toRefuse', 'method' => 'POST']) !!}
          <?php
            default:
              // code...
              break;
          }
          ?>
					<input type="hidden" id="type" name="type" value=<?php echo $type ?>>
          {!! Form::label('codigo', trans('Codigo de Barras:'),['class'=>'col-md-2 control-label']) !!}
          <div class="col-md-3">
            {!! Form::text('codigo', null, ['class'=>'form-control', 'placeholder' => 'Ingresa codigo de barras...', 'required']) !!}
          </div>
          <div class="col-md-3">
            {!! Form::submit('Consultar', ['class' => 'btn btn-primary']) !!}
          </div>
					<div>

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
