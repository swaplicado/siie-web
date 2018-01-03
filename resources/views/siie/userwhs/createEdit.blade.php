@extends('templates.basic_form')
@include('templates.head')
@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', trans('userinterface.titles.LIST_USERS').' sucursales')

@section('content')
	<?php $sRoute='siie.userwhs'

	?>
{!! Form::open(['route' => 'siie.userwhs.store' , 'method' => 'POST', 'files' => true]) !!}
	<table data-toggle="table" class="table table-condensed">
		<tr>
      <th>Nombre Usuario:</th>
      <th>Sucursal:</th>
      <th>Almacenes:</th>
    </tr>
    <tr>
      <td>
		     <input type="text" value="<?php echo $users->username; ?>" readonly></input>
				 <input type="hidden" value="<?php echo $users->id; ?>" name="user" id="user"></input>
      </td>
      <td>
        <select class="form-control select-branch branch" name="branch" id="branch" placeholder="Seleccione ...">
			@foreach($branches as $branch)
		      <option value="<?php echo $branch->branch->id_branch ?>" ><?php echo $branch->branch->name ?></option>
			@endforeach
        </select>
      </td>
      <td class="vaciar"></td>
      </tr>
		</tbody>
	</table>
	{!! Form::submit( trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="location.href='{{ route('admin.userpermissions.index') }}'">
</form>
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
           //console.log("hmm its change");

            var eti_id=$(this).val();
            var opt=" ";
            $.ajax({
              type:'get',
              url:'{!!URL::to('siie/userwhs/findWhs')!!}',
              data:{'id':eti_id},
              success:function(data){

								var arrayJS=<?php echo json_encode($whs);?>;
								var flag=false;

                  for(var i=0;i<data.length;i++){
										for(var j=0;j<arrayJS.length;j++){
											flag=false;
											if(arrayJS[j]==data[i].id_whs)
											{
												flag=true;
											}
										}
										if(flag==true){
											opt+='<label>'+data[i].name+'<input name="whs[]" id="whs" type="checkbox" checked value="'+data[i].id_whs+'"></label>';
										}else {
											opt+='<label>'+data[i].name+'<input name="whs[]" id="whs" type="checkbox" value="'+data[i].id_whs+'"></label>';
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
