@extends('userpermissions.create')
@section('menu')
@include('templates.menu.menumodules')
@endsection
@section('title', trans('userinterface.titles.CREATE_USER_PERMISSION') ." ". $user->username)

@section('content')

{!! Form::open(['route' => 'siie.userpermissionbranche.store', 'method' => 'POST']) !!}
<div class="container-fluid" style="top:100px">
   <div class="row">
		 <div class="col-md-4"></div>
      <div class="col-md-4 text-center form-group">

         <div class="well">
					 <h3>{!! trans('userinterface.MODULES') !!}</h3>
            {!! Form::select("module_id", $modules, null, ['class'=>'form-control select-modules modules', 'placeholder' => trans('userinterface.titles.SELECT_MODULE'), 'required']) !!}
         </div>

				 <div class="well text-left permissions" id="permissions">
				 </div>

				 <div class="well">
           <h3>{!! trans('userinterface.labels.BRANCH') !!}</h3>
           {!! Form::select("permission_id", $branches, null, ['class'=>'form-control select-branches branches', 'placeholder' => trans('userinterface.titles.SELECT_BRANCH'), 'required']) !!}
         </div>

         <div class="well whs" id="whs">
					 <h3>{!! trans('userinterface.titles.LIST_WAREHOUSES') !!}</h3>
         </div>

         <div class="well">
          {!! Form::label('privilege_id', trans('userinterface.labels.PRIVILEGE')) !!}
          {!! Form::select('privilege_id', $privileges, isset($assignament) ? $assignament->privilege->id : null , ['class' => 'form-control select-privilege', 'placeholder' => trans('userinterface.placeholders.SELECT_PRIVILEGE'), 'required']) !!}
        </div>
      </div>

			<div class="col-md-4">
        <input type="hidden" name="selectedUserId"  id="selectedUserId" value={!!$id!!}>
      </div>

	</div>
  <div class="row">
    <div class="col-md-12 text-right">
			{!! Form::submit( trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
				<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="location.href='{{ route('siie.userpermissionbranche.index') }}'">
		</div>
  </div>
</div>
{!! Form::close() !!}
@endsection
@section('js')
<script languaje='javascript'>

   $('.select-modules').chosen({});
   $('.select-permissions').chosen({});
   $('.select-branches').chosen({});
   $('.select-privilege').chosen({});

   //filter permission by modules.
   $(document).on('change', '.modules', function(){
       var eti_id = $(this).val();
       var div = $(this).parent();
       var opt = " ";
       $.ajax({
         type:'get',
         url:'{!!URL::to('siie/userpermissionbranche/findPermission')!!}',
         data:{'id':eti_id},
         success:function(data){
           opt += '<h3 class="text-center"> PERMISOS </h3><br>';
             for(var i = 0; i < data.length; i++){
               opt += '<label><input class="text-left" name="permission_id[]" type="checkbox" value="' + data[i].id_permission + '"> ' + data[i].name + '</label><br>';
             }
           $('.permissions').empty(" ");
           $('.permissions').append(opt);
         },
         error:function(){}
       });
   });

   $(document).on('change', '.branches', function(){
       var eti_id = $(this).val();

       var opt = " ";
         // document.getElementById("branches").classList.remove("displayNone");
         $.ajax({
           type:'get',
           url:'{!!URL::to('siie/userpermissionbranche/findWhs')!!}',
           data:{'id':eti_id},
           success:function(data){
             opt += '<h3 class="text-center"> Almacenes </h3><br>';
             for(var i = 0; i < data.length; i++){
                 opt += '<label><input class="text-left" name="whs_id" type="radio" value="' + data[i].id_whs + '"> ' + data[i].name + '</label><br>';
             }
             $('.whs').empty(" ");
             $('.whs').append(opt);

           },
           error:function(){

           }
         });
   });
</script>
@endsection
