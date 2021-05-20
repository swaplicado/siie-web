@extends('templates.basic_form')
@include('templates.head')

@section('menu')
@include('templates.menu.menumodules')
@endsection

@section('title', 'Consultar codigo de barras')
@section('titlepanel', 'Consultar codigo de barras')

@section('content')
  <div class="row">

    <div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

        <div class="form-group row">

          {!! Form::open(['route' => 'wms.codes.decode', 'method' => 'POST', 'id' => 'barcodeForm' ]) !!}

            {!! Form::label('codigo', trans('Codigo de Barras:'),['class'=>'col-md-2 control-label']) !!}

            <div class="col-md-6">

              {!! Form::text('codigo', null, ['class'=>'form-control', 'placeholder' => 'Ingresa codigo de barras...',
              'required']) !!}

            </div>

            <div class="col-md-3">

          {!! Form::submit('Consultar', ['class' => 'btn btn-primary', 'onClick' => 'showLoading(3000)']) !!}

          </div>

        </div>

      </div>

    </div>

  </div>
@endsection

@section('js')


<script type="text/javascript">
  $(document).ready(function() {
    document.getElementById('codigo').focus();
  });

  function showLoading(dTime) {
    swal({
        title: 'Espere',
        text: 'Cargando...',
        timer: dTime,
        onOpen: () => {
          swal.showLoading()
        }
      }).then((result) => {
        if (result.dismiss === 'timer') {
        }
    });
}
</script>

@endsection