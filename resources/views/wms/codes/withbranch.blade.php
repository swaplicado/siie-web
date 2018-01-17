@extends('templates.basic_form')
@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', 'Consultar codigo de barras')

@section('content')
  <div class="row">

    <div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

        <div class="form-group row">

          {!! Form::open(['route' => 'wms.codes.decodewith', 'method' => 'POST']) !!}

          {!! Form::label('branch', trans('Sucursal:'),['class'=>'col-md-2 control-label']) !!}

          <div class="col-md-3">

            {!! Form::select('branch', $branch, null , ['class'=>'form-control select-branch branch', 'placeholder' => 'Selecciona...', 'required']) !!}

          </div>
          {!! Form::label('whs', trans('Almacen:'),['class'=>'col-md-2 control-label']) !!}
          <div class="col-md-2 vaciar">

          {!! Form::select('productos',[ ], null , ['class'=>'form-control select-producto productos', 'placeholder' => 'Selecciona...', 'required']) !!}
         </div>

        </div>

      </div>
      <div class="col-md-12">

        <div class="form-group row">


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


      $(document).ready(function(){
        $('.select-usuario').chosen({
          placeholder_select_single: 'Seleccione un item...'
        });
        $('.select-branch').chosen({
          placeholder_select_single: 'Seleccione una unidad...'
        });

        $(document).on('change', '.branch', function(){
           console.log("hmm its change");

           var eti_id=$(this).val();
           var div=$(this).parent();
           var opt=" ";
            $.ajax({
              type:'get',
              url:'{!!URL::to('wms/codes/findWhs')!!}',
              data:{'id':eti_id},
              success:function(data){

                opt+='<select class="form-control select-producto almacenes"  name="almacenes" id="almacenes">';

                  for(var i=0;i<data.length;i++){
                    opt+='<option value="'+data[i].id_whs+'">'+data[i].id_whs+".- "+data[i].name+'</option>';
                  }

                console.log(opt);
                $('.vaciar').empty(" ");
                $('.vaciar').append(opt);

                $('.select-etiqueta').chosen({
                  placeholder_select_single: 'Seleccione un item...'
                });
                $('.select-producto').chosen({
                  placeholder_select_single: 'Seleccione una unidad...'
                });
              },
              error:function(){

              }
            });

        });

      });
  </script>

	@endsection
