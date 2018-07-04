@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

<?php $sRoute='qms.segregations'?>

@section('content')

	<br />
	<div class="row">
    {!! Form::open(['route' => 'qms.segregations.prepareData', 'method' => 'POST']) !!}
		<table class="table table-striped table-bordered responsive" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th data-priority="1">Clave</th>
		            <th>Ítem</th>
		            <th data-priority="1">Unidad</th>
		            <th>Lote</th>
		            <th>Tarima</th>
		            <th data-priority="1">Segregado</th>
		            <th>Almacén</th>
		            <th data-priority="1">{{ trans('userinterface.labels.STATUS') }}</th>
		            <th>Ref</th>

		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <input type="hidden" value="<?php echo $row->id_item ?>" id="id_item" name="id_item">
                <input type="hidden" value="<?php echo $row->id_unit ?>" id="id_unit" name="id_unit">
                <input type="hidden" value="<?php echo $row->id_lot ?>" id="id_lot" name="id_lot">
                <input type="hidden" value="<?php echo $row->id_pallet ?>" id="id_pallet" name="id_pallet">
                <input type="hidden" value="<?php echo $row->id_whs ?>" id="id_whs" name="id_whs">
                <input type="hidden" value="<?php echo $row->branch_id ?>" id="branch_id" name="branch_id">
                <input type="hidden" value="<?php echo $row->id_reference ?>" id="id_reference" name="id_reference">
                <input type="hidden" value="<?php echo $row->segregation_type_id ?>" id="segregation_type_id" name="segregation_type_id">
		            <td>{{ $row->item_code }} </td>
		            <td>{{ $row->item }}</td>
		            <td>{{ $row->unit }}</td>
								<td> -- </td>
								<td>{{ $row->pallet }}</td>
								<td> -- </td>
		            <td>{{ $row->warehouse }}</td>
		            <td>
									<span class="{{ App\SUtils\SGuiUtils::getClassOfStatus($row->id_segregation_event) }}">
										{{ $row->status_qlty }}
									</span>
								</td>
								<td>{{ $row->id_reference }}</td>
								<input type="hidden" value="<?php echo $row->id_segregation_event ?>" id="id_segregation_event" name="id_segregation_event">

            </tr>
					@endforeach
		    </tbody>
		</table>
	</div>

	<input type="hidden" value="<?php echo $newQ ?>" id="newQ" name="newQ">
	<input type="hidden" value="<?php echo $type ?>" id="type" name="type">
	@if($newQ == 0)
		<div class="col-md-3">

			{!! Form::select('statusRlP', $lStatusRec, null,
											['class'=>'form-control', 'id' => 'statusRlP',
											'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
											'required', ]) !!}
		</div>
		<div class="col-md-6">
			{!! Form::text('notes',null,['class' => 'form-control',
																						'id' => 'notesRFP']) !!}
		</div>
	@endif
	@if($newQ == 1)
		<div class="col-md-3">

			{!! Form::select('statusRFP', $lStatusLib, null,
											['class'=>'form-control statusRFP', 'id' => 'statusRFP',
											'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
											'required', ]) !!}
		</div>
		<div class="row">
		<div class="col-md-3 warehouseP">

		</div>
		<div class="col-md-3 locationP">

		</div>
	</div>
	<br>
		<div class="row">
		<div class="col-md-6">
			
			{!! Form::text('notes',null,['class' => 'form-control',
																						'id' => 'notesRFP']) !!}
		</div>
		</div>
		<br>
	@endif

  <div class="col-md-3">

    {!! Form::submit('Aceptar', ['class' => 'btn btn-primary']) !!}
  </div>

@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script src="{{ asset('js/segregation/segregation.js')}}"></script>
	<script src="{{ asset('js/segregation/segregations_table.js')}}"></script>
	<script type="text/javascript">

	$(document).ready(function(){
	var status = 0;
	$(document).on('change', '.statusRF',function(){
	  var eti_id=$(this).val();
		console.log(eti_id);
		 var opt=" ";
		 var opt2=" ";
		 status = eti_id;
		$.ajax({
			type:'get',
			url:'{!!URL::to('qms/segregation/findWarehouse')!!}',
			data:{'id':eti_id},

				success:function(data){
					opt+='<select class="form-control almacen" id="almacen"  name="almacen" required>';
						for(var i=0;i<data.length;i++){
				 			opt+='<option value="'+data[i].id_whs+'">'+data[i].name+'</option>';
					 }
					 opt+='</select>';
					 $('.warehouse').empty(" ");
					 $('.warehouse').append(opt);
					 opt2+='<select class="form-control ubicacion" id="ubicacion"  name="ubicacion" required>';
 					 opt2+='<option value=0>Seleccione una ubicacion</option>';
					  $('.location').empty(" ");
					 $('.location').append(opt2);

				},
				error:function(){
						console.log('falle');
				}
		});
		});

		$(document).on('change', '.statusRFP',function(){
			var eti_id=$(this).val();
			console.log(eti_id);
			 var opt=" ";
			 var opt2=" ";
			 status = eti_id;
			$.ajax({
				type:'get',
				url:'{!!URL::to('qms/segregation/findWarehouse')!!}',
				data:{'id':eti_id},

					success:function(data){
						opt+='<select class="form-control almacenP" id="almacenP"  name="almacenP" required>';
						opt+='<option value=0>Seleccione un almacen</option>';
							for(var i=0;i<data.length;i++){
								opt+='<option value="'+data[i].id_whs+'">'+data[i].name+'</option>';
						 }
						 opt+='</select>';
						 $('.warehouseP').empty(" ");
						 $('.warehouseP').append(opt);
						 opt2+='<select class="form-control ubicacion" id="ubicacionP"  name="ubicacionP" required>';
						 opt2+='<option value=0>Seleccione una ubicacion</option>';
							$('.locationP').empty(" ");
						 $('.locationP').append(opt2);

					},
					error:function(){
							console.log('falle');
					}
			});
		});
			$(document).on('change', '.almacenP',function(){
				var eti_id=$(this).val();
				 var opt=" ";
				 console.log('entre');
				$.ajax({
					type:'get',
					url:'{!!URL::to('qms/segregation/findLocations')!!}',
					data:{'id':eti_id,'status':status},

						success:function(data){
							console.log('success');
							opt+='<select class="form-control" id="ubicacionP"  name="ubicacionP" required>';
								for(var i=0;i<data.length;i++){
									opt+='<option value="'+data[i].id_whs_location+'">'+data[i].name+'</option>';
							 }
							 opt+='</select>';
							 $('.locationP').empty(" ");
							 $('.locationP').append(opt);

						},
						error:function(){
								console.log('falle');
						}
				});


				});
	});
	</script>

@endsection
