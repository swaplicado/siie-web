@extends('userpermissions.create')
@section('menu')
@include('templates.menu.menumodules')
@endsection
@section('title', trans('userinterface.titles.CREATE_USER_PERMISSION') . " " . \Auth::user()->username)
@section('content')
{!! Form::open(['route' => 'admin.userpermissions.store', 'method' => 'POST']) !!}
<div class="container-fluid" style="top:100px">
   <div class="row">
		 <div class="col-md-4"></div>
      <div class="col-md-4 text-center form-group">
          {{-- MODULES SELECT --}}
         <div class="well">
					 <h3>{!! trans('userinterface.MODULES') !!}</h3>
            {!! Form::select("module_id", $modules, null, ['class'=>'form-control select-modules modules', 'placeholder' => trans('userinterface.titles.SELECT_MODULE')]) !!}
         </div>
          {{-- PERMISSIONS SELECT --}}
				 <div class="well text-left permissions" id="permissions">
				 </div>
         {{-- SYS_PERMISSIONS SELECT --}}
				 <div class="well">
					 <h3>{!! trans('userinterface.SYS_PERMISSIONS') !!}</h3>
            {!! Form::select("permission_type_id", $syspermissions, null, ['class'=>'form-control select-syspermissions syspermissions', 'placeholder' => trans('userinterface.placeholders.SELECT_PERMISSION')]) !!}
         </div>
         {{-- COMPANIES SELECT --}}
				 <div class="well companies displayNone" id="companies">
         </div>
         {{-- BRANCHES SELECT --}}
				 <div class="well branches displayNone" id="branches">
         </div>
         {{-- PRIVILEGES SELECT --}}
         <div class="well" id="privileges">
					 <h3>{!! trans('userinterface.PRIVILEGES') !!}</h3>
            {!! Form::select("privilege_id", $privileges, null, ['class'=>'form-control select-privileges', 'placeholder' => trans('userinterface.placeholders.SELECT_PRIVILEGE')]) !!}
         </div>
      </div>

			<div class="col-md-4"></div>

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
   $('.select-companies').chosen({});
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
               opt += '<label><input class="text-left" name="permission_id" type="checkbox" value="' + data[i].id_permission + '"> ' + data[i].name + '</label><br>';
             }
           $('.permissions').empty(" ");
           $('.permissions').append(opt);
         },
         error:function(){}
       });
   });

   $(document).on('change', '.syspermissions', function(){
       var eti_id = $(this).val();
       var div = $(this).parent();
       var opt = " ";
       if (eti_id==2){
         // document.getElementById("companies").classList.remove("displayNone");
         $.ajax({
           type:'get',
           url:'{!!URL::to('admin/userpermissions/findCompanies')!!}',
           success:function(data){
             document.getElementById("companies").classList.remove("displayNone");
             opt += '<h3 class="text-center"> EMPRESA </h3><br>';
             for(var i = 0; i < data.length; i++){
                 opt += '<label><input class="text-left company_id_opt" name="company_id_opt" type="checkbox" value="' + data[i].id_company + '"> ' + data[i].name + '</label><br>';
             }
             $('.companies').empty(" ");
             $('.companies').append(opt);
           },
           error:function(){}
         });
       }
       else{
         $('.companies').empty(" ");
       }
   });

   $(document).on('change', '.company_id_opt', function(){
       var eti_id = $(this).val();
       var div = $(this).parent();
       var opt = " ";
         // document.getElementById("companies").classList.remove("displayNone");
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
