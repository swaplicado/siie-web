@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

<?php $sRoute='siie.docs'?>

@section('content')
	<div class="row">
		<div class="col-md-6">
				<div class="form-group">
					{!! Form::label('email', trans('siie.labels.PARTNER')) !!}
					{!! Form::text('name', $document->partner->name, ['class'=>'form-control input-sm', 'required', 'readonly']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('email', trans('siie.labels.DATE')) !!}
					{!! Form::date('name', $document->dt_date, ['class'=>'form-control input-sm', 'required', 'readonly']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('email', trans('siie.labels.CURRENCY')) !!}
					{!! Form::text('name', $document->currency->name, ['class'=>'form-control input-sm', 'required', 'readonly']) !!}
				</div>
				<div class="form-group">
					{!! Form::label('email', trans('siie.labels.EXCHANGE_RATE')) !!}
					{!! Form::text('name', session('utils')->formatNumber($document->exchange_rate, \Config::get('scsiie.FRMT.QTY')),
						['class'=>'form-control input-sm','readonly', 'style' => 'text-align: right;']) !!}
				</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.SUB_TOTAL_M')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->subtotal, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.TRA_TAXES')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->tax_charged, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.RET_TAXES')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->tax_retained, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.TOTAL_M')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->total, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.SUB_TOTAL_D')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->subtotal_cur, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm',
														'readonly', 'style' => 'text-align: right;']) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.TRA_TAXES_D')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->tax_charged_cur, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.RET_TAXES_D')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->tax_retained_cur, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
					<div class="form-group">
						{!! Form::label('email', trans('siie.labels.TOTAL_D')) !!}
						{!! Form::text('name', session('utils')->formatNumber($document->total_cur, \Config::get('scsiie.FRMT.AMT')),
														['class'=>'form-control input-sm', 'readonly', 'style' => 'text-align: right;']) !!}
					</div>
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class="row">
		<table id="docTable" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
		    <thead>
		        <tr class="titlerow">
		            <th>{{ trans('siie.labels.KEY') }}</th>
		            <th>{{ trans('siie.labels.CONCEPT') }}</th>
		            <th>{{ trans('siie.labels.QUANTITY') }}</th>
								<th>{{ trans('siie.labels.UNIT') }}</th>
		            <th>{{ trans('siie.labels.UN_PRICE') }} $</th>
		            <th data-priority="1">{{ trans('siie.labels.SUB_TOTAL') }}$</th>
		            <th data-priority="1">Imp. Carg.$</th>
		            <th>Imp. Ret.$</th>
		            <th data-priority="1">{{ trans('siie.labels.TOTAL') }}$</th>
		        </tr>
		    </thead>
		    <tbody>
					@foreach ($document->rows as $row)
						<tr>
		            <td class="small">{{ $row->concept_key }}</td>
		            <td class="small">{{ $row->concept }}</td>
								<td class="small" align="right">{{ session('utils')->formatNumber($row->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
		            <td class="small">{{ $row->unit->code }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($row->price_unit_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($row->subtotal_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($row->tax_charged_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($row->tax_retained_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		            <td class="small" align="right">{{ session('utils')->formatNumber($row->total_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
		        </tr>
					@endforeach
		    </tbody>
		</table>
	</div>
@endsection

@section('js')
	@include('templates.stock.scriptsstock')
	<script>

		$('#docTable').DataTable({
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
		      }
		  });

		  $(document).ready( function () {
		    var table = $('#docTable').DataTable();
		  });
	</script>
@endsection
