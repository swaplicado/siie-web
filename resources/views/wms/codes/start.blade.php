@extends('templates.basic_form')
@include('templates.head')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.GENERATION_BARCODE'))

@section('content')
  <div class="row">

    <div class="form-group">

      <div class="form-group row"></div>

      <div class="col-md-12">

        <div class="form-group row">

          {!! Form::open(['route' => 'wms.codes.generate', 'method' => 'POST']) !!}

          {!! Form::label('etiqueta', trans('Etiquetas para:'),['class'=>'col-md-2 control-label']) !!}

          <div class="col-md-3">

            {!! Form::select('etiqueta', ['Tarima','Item suelto'], null , ['class'=>'form-control select-etiqueta etiqueta', 'placeholder' => 'Selecciona...', 'required']) !!}

          </div>

          <div class="col-md-2 vaciar">

          {!! Form::select('productos',[ ], null , ['class'=>'form-control select-producto productos', 'placeholder' => 'Selecciona...', 'required']) !!}
         </div>

        </div>

      </div>

      <div class="col-d-12">


      	<div class="form-group" align="right">
      		{!! Form::submit('Generar', ['class' => 'btn btn-primary', 'onClick' => 'disable(this)']) !!}
        </div>
      </div>

    </div>

  </div>




@endsection

@section('js')

	<script type="text/javascript">


	</script>

  <script type="text/javascript">

      $(window).onload = function(){
        console.log('entre');
      };

      $(document).ready(function(){
        $('.select-etiqueta').chosen({
          placeholder_select_single: 'Seleccione un item...'
        });
        $('.select-producto').chosen({
          placeholder_select_single: 'Seleccione una unidad...'
        });

        $(document).on('change', '.etiqueta', function(){
           //console.log("hmm its change");

            var eti_id=$(this).val();
            var div=$(this).parent();
            var opt=" ";

            $.ajax({
              type:'get',
              url:'{!!URL::to('wms/codes/findProductName')!!}',
              data:{'id':eti_id},
              success:function(data){
                console.log('success');




                opt+='<select class="form-control select-producto productos"  name="productos" id="productos">';

                if(eti_id==1){
                  for(var i=0;i<data.length;i++){
                    opt+='<option value="'+data[i].id_lot+'">'+data[i].id_lot+".- "+data[i].name+'</option>';
                  }
                }
                else{
                  for(var i=0;i<data.length;i++){
                    opt+='<option value="'+data[i].id_pallet+'">'+data[i].pallet+'</option>';
                  }
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
