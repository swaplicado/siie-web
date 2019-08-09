@extends('templates.home.modules')

@section('title', $title)

@section('content')

  <div class="row">
      <div class="col-md-7 col-md-offset-2">
        {!! Form::open(['route' => 'siie.import.docs']) !!}
          <div class="row">
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
            </div>
            <div class="col-md-4">
              Elementos importados
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Host base de datos') !!}
            </div>
            <div class="col-md-4">
              {!! Form::text('db_host', $db_host, ['class' => 'form-control', 'style' => 'text-align: center;', 'readonly']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'ítems') !!}
            </div>
            <div class="col-md-4">
              {!! Form::checkbox('items', 'items', true, ['class' => 'form-control']) !!}
            </div>
            @if ($items > 0)
              <div class="col-md-4">
                    {{ $items.' '.'ítems' }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Asociados de negocios') !!}
            </div>
            <div class="col-md-4">
              {!! Form::checkbox('partners', 'partners', true, ['class' => 'form-control']) !!}
            </div>
            @if ($partners > 0)
              <div class="col-md-4">
                    {{ $partners.' '.'Asociados de negocios' }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Sucursales') !!}
            </div>
            <div class="col-md-4">
              {!! Form::checkbox('branches', 'branches', true, ['class' => 'form-control']) !!}
            </div>
            @if ($branches > 0)
              <div class="col-md-4">
                    {{ $branches.' '.'Sucursales' }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Direcciones de sucursales') !!}
            </div>
            <div class="col-md-4">
              {!! Form::checkbox('addresses', 'addresses', true, ['class' => 'form-control']) !!}
            </div>
            @if ($adds > 0)
              <div class="col-md-4">
                    {{ $adds.' '.'Direcciones de sucursales' }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Año a importar') !!}
            </div>
            <div class="col-md-4">
              {!! Form::number('year', $year, ['class' => 'form-control', 'style' => 'text-align: center;']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Base de datos origen') !!}
            </div>
            <div class="col-md-4">
              {!! Form::text('db_name', $db_import, ['class' => 'form-control', 'style' => 'text-align: center;', 'readonly']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', 'Documentos') !!}
            </div>
            <div class="col-md-4">
              {!! Form::checkbox('docs', 'docs', true, ['class' => 'form-control']) !!}
            </div>
            @if ($docs > 0)
              <div class="col-md-4">
                    {{ $docs.' '.'Documentos' }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-md-4">
              {!! Form::label('year', trans('siie.SYNCR.ROWS')) !!}
            </div>
            <div class="col-md-4">
              {!! Form::checkbox('rows1', 'rows1', true, ['class' => 'form-control']) !!}
            </div>
            @if ($rows1 > 0)
              <div class="col-md-4">
                    {{ $rows1.' '.trans('siie.SYNCR.ROWS') }}
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-md-1">
            </div>
            <div class="col-md-7 col-md-offset-4">
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
            timer: 500000,
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
