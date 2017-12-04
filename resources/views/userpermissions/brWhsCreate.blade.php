@extends('userpermissions.create')
@section('menu')
@include('templates.menu.menumodules')
@endsection
@section('title', trans('userinterface.titles.CREATE_USER_PERMISSION') . " ")// . $selectedNameId ." # de usuario: " . $selectedUserId)
@section('content')
  <?php
  $branches = array("", "", "");
  ?>
{!! Form::open(['route' => 'admin.userpermissions.store', 'method' => 'POST']) !!}
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

				 <div class="well branches" id="branches">
           <h3>{!! trans('userinterface.branches') !!}</h3>
           {{-- @foreach ($branches as $branch)
             <label>
               {!! Form::checkbox("branches_id", $branch->company_id, null, ['class'=>'','required']) !!}

             </label>
             <br>
           @endforeach --}}

         </div>

				 <div class="well branches displayNone" id="branches">
         </div>

         <div class="well" id="privileges">
					 <h3>{!! trans('userinterface.PRIVILEGES') !!}</h3>
            {!! Form::select("privilege_id", $privileges, null, ['class'=>'form-control select-privileges', 'placeholder' => trans('userinterface.placeholders.SELECT_PRIVILEGE'), 'required']) !!}
         </div>
      </div>

			<div class="col-md-4">
        <input type="hidden" name="selectedUserId"  id="selectedUserId" value={!!$selectedUserId!!}>
      </div>

	</div>
  <div class="row">
    <div class="col-md-12 text-right">
			{!! Form::submit( trans('actions.SAVE'), ['class' => 'btn btn-primary']) !!}
				<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="location.href='{{ route('admin.userpermissions.index') }}'">
		</div>
  </div>
</div>
{!! Form::close() !!}
<!-- Nuevo form end   -->
@endsection
@section('js')
<script languaje='javascript'>
   $('.select-syspermissions').chosen({});
   $('.select-modules').chosen({});
   $('.select-permissions').chosen({});
   $('.select-branches').chosen({});
   $('.select-privileges').chosen({});

   //filter permission by modules.
   $(document).on('change', '.modules', function(){
       var eti_id = $(this).val();
       var div = $(this).parent();
       var opt = " ";
       $.ajax({
         type:'get',
         url:'{!!URL::to('admin/userpermissions/findPermission')!!}',
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

   $(document).on('change', '.company_id_opt', function(){
       var eti_id = $(this).val();
       var div = $(this).parent();
       var opt = " ";
         // document.getElementById("branches").classList.remove("displayNone");
         $.ajax({
           type:'get',
           url:'{!!URL::to('admin/userpermissions/findBranches')!!}',
           success:function(data){
             document.getElementById("branches").classList.remove("displayNone");
             opt += '<h3 class="text-center"> Sucursal </h3><br>';
             for(var i = 0; i < data.length; i++){
                 opt += '<label><input class="text-left" name="company_id_opt" type="radio" value="' + data[i].id_branch + '"> ' + data[i].name + '</label><br>';
             }
             $('.branches').empty(" ");
             $('.branches').append(opt);
           },
           error:function(){}
         });
   });
</script>
@endsection