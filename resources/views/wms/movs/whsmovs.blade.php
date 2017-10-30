@extends('templates.formmovs')

@section('head')
	@include('templates.headmovs')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_MOVS'))

@section('content')
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
  			{!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE').'*') !!}
				{!! Form::select('mvt_whs_type_id', $movTypes, isset($movement) ?  $movement->mvt_whs_type_id : null , ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_GROUP'), 'required']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('folio', trans('userinterface.labels.MVT_FOLIO').'*') !!}
  			{!! Form::text('folio',
  				isset($movement) ? $movement->folio : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required', 'unique']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
  			{!! Form::date('dt_date', \Carbon\Carbon::now(), ['class'=>'form-control']) !!}
  		</div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
  			{!! Form::label('whs', trans('userinterface.labels.MVT_WHS_SRC').'*') !!}
				{!! Form::select('whs', $warehouses, 1, ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_GROUP'), 'required']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('whs_id', trans('userinterface.labels.MVT_WHS_DEST').'*') !!}
				{!! Form::select('whs_id', $warehouses, isset($movement) ?  $movement->whs_id : null , ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_GROUP'), 'required']) !!}
  		</div>

      <div class="form-group">
				{!! Form::open(['url' => '#']) !!}
	  			{!! Form::label('item', trans('userinterface.labels.WHS_ITM').'*') !!}
						<div class="row">
						  <div class="col-md-6">
									{!! Form::text('item',
										isset($whs) ? $whs->code : null , ['class'=>'form-control', 'id' => 'item', 'placeholder' => trans('userinterface.placeholders.CODE'),
										 																		'required', 'unique']) !!}
							</div>
						  <div class="col-md-3">
									{!! Form::number('quantity', 1, ['class'=>'form-control', 'id' => 'quantity']) !!}
							</div>
						  <div class="col-md-3">
									{!! Form::submit(trans('actions.ADD'), ['class'=>'form-control btn-primary', 'id' => 'tButton']) !!}
							</div>
						</div>
				{!! Form::close() !!}
  		</div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
			<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
					<thead>
							<tr class="titlerow">
									<th>{{ trans('wms.labels.CODE') }}</th>
									<th>{{ trans('wms.labels.MAT_PROD') }}</th>
									<th>{{ trans('wms.labels.UNIT') }}</th>
									<th>{{ trans('wms.labels.LOCATION') }}</th>
									<th>{{ trans('wms.labels.LOT') }}</th>
									<th>{{ trans('wms.labels.PALLET') }}</th>
									<th>{{ trans('wms.labels.PRICE') }}</th>
									<th>{{ trans('wms.labels.QTY') }}</th>
							</tr>
					</thead>
					<tfoot>
							<tr class="totalColumn">
									<td>{{ trans('userinterface.TOTAL') }}</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right" class="totalCol"></td>
							</tr>
					</tfoot>
					<tbody id="lbody">
						@foreach ($movement->rows as $row)
							<tr>
									<td>{{ $row->item->code }}</td>
									<td>{{ $row->item->name }}</td>
									<td>{{ $row->item->unit->code }}</td>
									<td>{{ 'Estante' }}</td>
									<td>{{ '12354872' }}</td>
									<td>{{ $row->pallet->pallet }}</td>
									<td align="right">{{ $row->amount_unit }}</td>
									<td align="right" class="summ">{{ $row->quantity }}</td>
							</tr>
						@endforeach
					</tbody>
			</table>
    </div>
  </div>
@endsection

@section('js')
	<script>
	var totals=[0,0,0];
	$(document).ready(function(){

			var $dataRows=$("#example tr:not('.totalColumn, .titlerow')");

			$dataRows.each(function() {
					$(this).find('.summ').each(function(i){
							totals[i]+=parseFloat( $(this).html());
					});
			});
			$("#example td.totalCol").each(function(i){
					$(this).html(totals[i].toFixed(8));
			});

	});

	$(document).ready(function() {
	    $('#example').DataTable();
	});

	</script>
@endsection
