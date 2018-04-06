@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

<?php $sRoute='qms.segregations'?>

@section('content')
	@section('thefilters')
		{!! Form::open(['route' => [ $sRoute.'.index'],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
				<div class="input-group">
					<span class="input-group-btn">
						{!! Form::date('filterDate', $tFilterDate, ['class'=>'form-control']) !!}
					</span>
					<span class="input-group-btn">
							<button id="searchbtn" type="submit" class="form-control">
								<span class="glyphicon glyphicon-search"></span>
							</button>
					</span>
				</div>
			</div>
		{!! Form::close() !!}
	@endsection
	@include('wms.segregations.classification')
	<br />
	<div class="row">
		<table id="table_seg" class="table table-striped table-bordered responsive" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th>id_item</th>
		            <th>id_unit</th>
		            <th>id_lot</th>
		            <th>id_pallet</th>
		            <th>id_whs</th>
		            <th>id_branch</th>
		            <th>id_reference</th>
		            <th>segregation_type_id</th>
		            <th data-priority="1">Clave</th>
		            <th>Item</th>
		            <th data-priority="1">Unidad</th>
		            <th>Lote</th>
		            <th>Tarima</th>
		            <th data-priority="1">Segregado</th>
		            <th>Almacén</th>
		            <th data-priority="1">{{ trans('userinterface.labels.STATUS') }}</th>
		            <th>Ref</th>
								@if ($iQualityType == \Config::get('scqms.QMS_VIEW.CLASSIFY') || $iQualityType == \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY') || $iQualityType == \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'))
									<th>-</th>
									<th>-</th>
									<th>-</th>
								@endif
								<th>-</th>
								<th>-</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <td>{{ $row->id_item }}</td>
		            <td>{{ $row->id_unit }}</td>
		            <td>{{ $row->id_lot }}</td>
		            <td>{{ $row->id_pallet }}</td>
		            <td>{{ $row->id_whs }}</td>
		            <td>{{ $row->branch_id }}</td>
		            <td>{{ $row->id_reference }}</td>
		            <td>{{ $row->segregation_type_id }}</td>
		            <td>{{ $row->item_code }}</td>
		            <td>{{ $row->item }}</td>
		            <td>{{ $row->unit }}</td>
		            <td>{{ $row->lot_name }}</td>
		            <td>{{ $row->pallet }}</td>
		            <td>{{ $row->segregated }}</td>
		            <td>{{ $row->warehouse }}</td>
		            <td>
									<span class="{{ App\SUtils\SGuiUtils::getClassOfStatus($row->id_segregation_event) }}">
										{{ $row->status_qlty }}
									</span>
								</td>
								<td>{{ $row->id_reference }}</td>
								@if ($iQualityType == \Config::get('scqms.QMS_VIEW.CLASSIFY') || $iQualityType == \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY') || $iQualityType == \Config::get('scqms.QMS_VIEW.QUARANTINECLASSIFY'))
									<td>
										<a data-toggle="modal" data-target="#classQlty"
												title="Evaluar material/producto"
												onclick="classificateQlty(this)"
												class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-share" aria-hidden = "true"/>
										</a>
									</td>
									<td>
										<a data-toggle="modal" data-target="#classRls"
												title="Liberar material/producto"
												onclick="classificateRls(this)"
												class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-thumbs-up" aria-hidden = "true"/>
										</a>
									</td>
									<td>
										<a data-toggle="modal" data-target="#classRfs"
												title="Evaluar material/producto"
												onclick="classificateRfs(this)"
												class="btn btn-default btn-sm">
											<span class="glyphicon glyphicon-thumbs-down" aria-hidden = "true"/>
										</a>
									</td>
								@endif
									<td>{{ $row->id_segregation_event}}</td>
									<td>{{ $row->whs_location_id}}</td>
		        </tr>
					@endforeach
		    </tbody>
		</table>
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
		console.log('entre');
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
					console.log('success');
					opt+='<select class="form-control almacen" id="almacen"  name="almacen" required>';
					opt+='<option value=0>Seleccione un almacen</option>';
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

		$(document).on('change', '.almacen',function(){
			var eti_id=$(this).val();
			 var opt=" ";
			$.ajax({
				type:'get',
				url:'{!!URL::to('qms/segregation/findLocations')!!}',
				data:{'id':eti_id,'status':status},

					success:function(data){
						console.log('success');
						opt+='<select class="form-control" id="ubicacion"  name="ubicacion" required>';
							for(var i=0;i<data.length;i++){
								opt+='<option value="'+data[i].id_whs_location+'">'+data[i].name+'</option>';
						 }
						 opt+='</select>';
						 $('.location').empty(" ");
						 $('.location').append(opt);

					},
					error:function(){
							console.log('falle');
					}
			});

			});
	});
	</script>

@endsection
