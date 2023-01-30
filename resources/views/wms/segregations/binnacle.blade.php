@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', 'Bitácora de Calidad')

<?php $sRoute='qms.segregations'?>

@section('content')
	@section('thefilters')
    {!! Form::open(['route' => [$sRoute.'.binnacle'],
										'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
			<div class="form-group">
				<div class="input-group">
          <span class="input-group-btn">
            <select style="font-size:8pt; width:170px" class="form-control" id="filterPallet" name="filterPallet">
                <option value="0">Selecciona Tarima</option>
              @foreach ($lPallet as $lpallet)
                @if($lpallet->id_pallet == $sFilterPallet)
                  <option value="<?php echo $lpallet->id_pallet;?>" selected><?php echo $lpallet->pallet;?></option>
                @else
                  <option value="<?php echo $lpallet->id_pallet;?>"><?php echo $lpallet->pallet;?></option>
                @endif
    					@endforeach
            </select>
          </span>
          <span class="input-group-btn">
            <select style="font-size:8pt;; width:170px" class="form-control" id="filterLot" name="filterLot">
                <option value="0">Selecciona Lote</option>
              @foreach ($lLot as $llot)
                @if($llot->id_lot == $sFilterLot)
                  <option value="<?php echo $llot->id_lot;?>" selected><?php echo $llot->lot;?></option>
                @else
                  <option value="<?php echo $llot->id_lot;?>"><?php echo $llot->lot;?></option>
                @endif
    					@endforeach
            </select>
          </span>
          <span class="input-group-btn">
            <select style="font-size:8pt; width:170px" class="form-control" id="filterItem" name="filterItem">
                <option value="0">Selecciona Item</option>
              @foreach ($lItem as $litem)
                @if($litem->item_code == $sFilterItem)
                  <option value="<?php echo $litem->item_code;?>" selected><?php echo $litem->item;?></option>
                @else
                  <option value="<?php echo $litem->item_code;?>"><?php echo $litem->item;?></option>
                @endif
    					@endforeach
            </select>
          </span>
          <span class="input-group-btn">
						{!! Form::text('filterDate', $oFilterDate == 0 ? null : $oFilterDate, ['class' => 'form-control', 'id' => 'filterDate']) !!}
					</span>
          <span class="input-group-btn">
            <select style="font-size:8pt ; width:170px" class="form-control" id="filterEvent" name="filterEvent">
                <option value="0">Selecciona Evento</option>
              @foreach ($lEvent as $levent)
                @if($levent->id_segregation_event == $sFilterEvent)
                  <option value="<?php echo $levent->id_segregation_event;?>" selected><?php echo $levent->event;?></option>
                @else
                  <option value="<?php echo $levent->id_segregation_event;?>"><?php echo $levent->event;?></option>
                @endif
    					@endforeach
            </select>
          </span>
          <span class="input-group-btn">
            <select style="font-size:8pt; width:170px" class="form-control" id="filterUser" name="filterUser">
                <option value="0">Selecciona Usuario</option>
              @foreach ($lUser as $luser)
                @if($luser->id_user == $sFilterUser)
                  <option value="<?php echo $luser->id_user;?>" selected><?php echo $luser->username;?></option>
                @else
                  <option value="<?php echo $luser->id_user;?>"><?php echo $luser->username;?></option>
                @endif
              @endforeach
            </select>
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
	<br />
	<div class="row">
		<table id="table_2" class="table table-striped table-bordered responsive" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th>Ítem</th>
		            <th data-priority="1">Unidad</th>
		            <th>Lote</th>
		            <th>Tarima</th>
								<th>E/S</th>
		            <th>Evento Segregación</th>
								<th>Fecha</th>
                <th>Cantidad</th>
                <th>Usuario</th>
								<th>Notas</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($data as $row)
						<tr>
		            <td>{{ $row->item }}</td>
		            <td>{{ $row->unit }}</td>
		            <td>{{ $row->lot_name }}</td>
		            <td>{{ $row->pallet }}</td>
								<td><?php  if($row->mov == 1){ echo "Entrada"; }
											else{ echo "Salida";}
										?>
		            <td>{{ $row->event }}</td>
                <td>{{ $row->date }}
                <td>{{ $row->qty }}</td>
                <td>{{ $row->username }}</td>
								<td>{{ $row->notes}}</td>
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
  <script src="{{ asset('moment/moment.js') }}"></script>
  <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
	<script>

			 $(function() {
				 $('input[id="filterDate"]').daterangepicker({
					 locale: {
									format: 'DD/MM/YYYY'
							}
				 });
			 });

			 $('#filterDate').on('apply.daterangepicker', function(ev, picker) {
				 console.log(picker.startDate.format('YYYY-MM-DD'));
				 console.log(picker.endDate.format('YYYY-MM-DD'));
			 });
       $('#table_2').DataTable({
            "language": {
              "sProcessing":     "Procesando...",
              "sLengthMenu":     "Mostrar _MENU_ registros",
              "sZeroRecords":    "No se encontraron resultados",
              "sEmptyTable":     "Ningún dato disponible en esta tabla",
              "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
              "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
              "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
              "sInfoPostFix":    "",
              "sSearch":         "Buscar:",
              "sUrl":            "",
              "sInfoThousands":  ",",
              "sLoadingRecords": "Cargando...",
              "oPaginate": {
                  "sFirst":    "Primero",
                  "sLast":     "Último",
                  "sNext":     "Siguiente",
                  "sPrevious": "Anterior"
              },
              "oAria": {
                  "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                  "sSortDescending": ": Activar para ordenar la columna de manera descendente"
              }
            },
            "colReorder": true
        });
	</script>
@endsection
