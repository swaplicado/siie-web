@extends('templates.home.modules')

@section('title', $title)

@section('content')

  <div class="row">
      <div class="col-md-7 col-md-offset-2">
        {!! Form::open(['route' => 'siie.import.docs']) !!}
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Host base de datos') !!}
            </div>
            <div class="col-md-6">
              {!! Form::text('db_host', session('db_host'), ['class' => 'form-control', 'style' => 'text-align: center;', 'readonly']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'ítems') !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('items', 'items', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Asociados de negocios') !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('partners', 'partners', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Sucursales') !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('branches', 'branches', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Direcciones de sucursales') !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('addresses', 'addresses', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Año a importar') !!}
            </div>
            <div class="col-md-6">
              {!! Form::number('year', '2018', ['class' => 'form-control', 'style' => 'text-align: center;']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Base de datos origen') !!}
            </div>
            <div class="col-md-6">
              {!! Form::text('db_name', session('db_import'), ['class' => 'form-control', 'style' => 'text-align: center;', 'readonly']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', 'Documentos') !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('docs', 'docs', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', trans('siie.SYNCR.ROWS1')) !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('rows1', 'rows1', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              {!! Form::label('year', trans('siie.SYNCR.ROWS2')) !!}
            </div>
            <div class="col-md-6">
              {!! Form::checkbox('rows2', 'rows2', true, ['class' => 'form-control']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-7 col-md-offset-6">
              <div class="form-group" align="right">
            		{!! Form::submit(trans('actions.IMPORT'), ['id' => 'submitBtn','class' => 'btn btn-primary', 'onClick' => 'holaFun()']) !!}
              </div>
            </div>
          </div>
        {!! Form::close() !!}
      </div>
  </div>

@endsection

@section('js')
  <script>
      var isImported = <?php echo json_encode($isImported); ?>;



      if (isImported == 1) {
        swal({
          type: 'success',
          title: 'Proceso finalizado',
          showConfirmButton: false,
          timer: 1500
          })
      }

      function holaFun() {
        swal({
            title: 'Espere...',
            text: 'Se está realizando el proceso de importación.',
            timer: 300000,
            onOpen: () => {
              swal.showLoading()
            }
          }).then((result) => {
            if (result.dismiss === 'timer') {
              console.log('I was closed by the timer');
            }
          });
      }
  </script>
@endsection
