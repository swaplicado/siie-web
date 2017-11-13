@extends('templates.formmovs')

@section('head')
	@include('templates.headmovs')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_MOVS'))

@section('content')
{!! Form::open(
	['route' => 'wms.movs.store', 'method' => 'POST', 'id' => 'theForm']
	) !!}
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
				{!! Form::hidden('mvt_whs_class_id', $oMovType->mvt_class_id) !!}
  			{!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE').'*') !!}
				{!! Form::select('mvt_whs_type_id', $movTypes,  $movement->mvt_whs_type_id != 0 ?  $movement->mvt_whs_type_id : $oMovType->id_mvt_type, ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'), 'required']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('mvt_com', trans('userinterface.labels.MVT_TYPE').'*') !!}
				{!! Form::select('mvt_com', $mvtComp, 1, ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'), 'required']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('folio', trans('userinterface.labels.MVT_FOLIO').'*') !!}
  			{!! Form::text('folio',
  				isset($movement) ? $movement->folio : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.FOLIO'), 'required', 'unique']) !!}
  		</div>


    </div>
    <div class="col-md-6">
			<div class="form-group">
  			{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
  			{!! Form::date('dt_date', \Carbon\Carbon::now(), ['class'=>'form-control']) !!}
  		</div>

			<div class="form-group">
				@if ($oMovType->mvt_class_id == \Config::get('scwms.MVT_CLS_OUT'))
						{!! Form::label('whs_src', trans('userinterface.labels.MVT_WHS_SRC').'*') !!}
						{!! Form::select('whs_src', $warehouses, 0, ['class'=>'form-control',
																																	'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required']) !!}
				@else
		  			{!! Form::label('whs_des', trans('userinterface.labels.MVT_WHS_DEST').'*') !!}
						{!! Form::select('whs_des', $warehouses, 0, ['class'=>'form-control',
																																	'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required']) !!}
				@endif
			</div>

      	<div class="form-group">
	  			{!! Form::label('item', trans('userinterface.labels.WHS_ITM').'*') !!}
						<div class="row">
						  <div class="col-md-6">
									{!! Form::text('item',
										isset($whs) ? $whs->code : null , ['class'=>'form-control', 'id' => 'item', 'placeholder' => trans('userinterface.placeholders.CODE'),
																												'onkeypress' => 'addRowByEnter(event)', 'required']) !!}
							</div>
						  <div class="col-md-3">
									{!! Form::number('quantity', 1, ['class'=>'form-control', 'id' => 'quantity','onkeypress' => 'addRowByEnter(event)',
																												'placeholder' => trans('userinterface.placeholders.QUANTITY')]) !!}
							</div>
						  <div class="col-md-3">
									{{-- {!! Form::submit(trans('actions.ADD'), ['class'=>'form-control btn-primary', 'id' => 'tButton']) !!} --}}
									<button id="tButton" type="button" class="btn btn-primary">{{ trans('actions.ADD') }}</button>
							</div>
						</div>
  			</div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
			{{-- <button class="btn btn-danger" type="button" onclick="fnselect()"><i class="glyphicon glyphicon-remove"></i></button>
			<br />
			<br /> --}}
			<div class="form-group">
				<table id="example" class="table table-bordered" cellspacing="0" width="100%">
						<thead>
								<tr class="titlerow">
										<th data-override="id_tr" style="display:none;"></th>
										<th data-override="code">{{ trans('wms.labels.CODE') }}</th>
										<th>{{ trans('wms.labels.MAT_PROD') }}</th>
										<th>{{ trans('wms.labels.UNIT') }}</th>
										<th>{{ trans('wms.labels.LOCATION') }}</th>
										<th>{{ trans('wms.labels.LOT') }}</th>
										<th data-override="price">{{ trans('wms.labels.PALLET') }}</th>
										<th data-override="price">{{ trans('wms.labels.PRICE') }}</th>
										<th data-override="qty">{{ trans('wms.labels.QTY') }}</th>
										<th>-</th>
										<th data-override="id_item" style="display:none;"></th>
										<th data-override="id_unit" style="display:none;"></th>
										<th data-override="id_location" style="display:none;"></th>
										<th data-override="id_pallet" style="display:none;"></th>
								</tr>
						</thead>
						<tfoot>
								{{-- <tr class="totalColumn">
										<td>{{ trans('userinterface.TOTAL') }}</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td align="right" class="totalCol"></td>
								</tr> --}}
						</tfoot>
						<tbody id="lbody">
							@foreach ($movement->rows as $row)
								<tr  class="clickable" data-toggle="collapse" id="{{ $row->item->id_item }}" data-target=".{{ $row->item->id_item }}">
										<td><i class="glyphicon glyphicon-plus"></i></td>
										<td>{{ $row->item->code }}</td>
										<td>{{ $row->item->name }}</td>
										<td>{{ $row->item->unit->code }}</td>
										<td>{{ 'Estante' }}</td>
										<td>{{ '12354872' }}
											<a href="">
													<i class="glyphicon glyphicon-duplicate btn-sm"/>
											</a>
										</td>
										<td>{{ $row->pallet->pallet }}</td>
										<td align="right">{{ $row->amount_unit }}</td>
										<td align="right" class="summ">{{ $row->quantity }}</td>
								</tr>
								@foreach ($row->lotRows as $lotRow)
									<tr class="collapse {{ $row->item->id_item }}">
										<td>data</td>
				          	<td>data</td>
				            <td>data</td>
									</tr>
								@endforeach
							@endforeach
						</tbody>
				</table>
			</div>
    </div>
  </div>
	<div class="form-group" align="right">
		<a id="idFreeze" class="btn btn-info" onclick="unfreeze()" role="button">{{ trans('actions.FREEZE') }}</a>
		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled']) !!}
		<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="location.href='{{ route('wms.home') }}'"/>
	</div>
{!! Form::close() !!}
@endsection

@section('js')
	@include('templates.scriptsmovs')
	<script>

		var lotsjs = <?php echo json_encode($lots); ?>;
		var locationsjs = <?php echo json_encode($locations); ?>;
		var palletsjs = <?php echo json_encode($pallets); ?>;
		var bInput = <?php echo json_encode($oMovType->mvt_class_id != \Config::get('scwms.MVT_CLS_OUT')); ?>;

		var globalData = new GlobalData(lotsjs, locationsjs, palletsjs, bInput);

		// var totals=[0,0,0];
		/*
		* This function puts a row of totals in the table
		*/
		// $(document).ready(function(){
		//
		// 		var $dataRows=$("#example tr:not('.totalColumn, .titlerow')");
		//
		// 		$dataRows.each(function() {
		// 				$(this).find('.summ').each(function(i){
		// 						totals[i]+=parseFloat( $(this).html());
		// 				});
		// 		});
		// 		$("#example td.totalCol").each(function(i){
		// 				$(this).html(totals[i].toFixed(8));
		// 		});
		//
		// });

		// $(document).ready(function() {
		//     $('#example').DataTable();
		// });

		/*
		* When freeze is pressed, the field of item, quantity and the button
		* of add are disabled, but the data of the movement is send to server too
		*/
		function unfreeze() {
			var fre = document.getElementById("idFreeze"); // freeze button
			var but = document.getElementById("tButton"); // Add button
			var item = document.getElementById("item"); // item field
			var qty = document.getElementById("quantity"); // quantity field
			var sBut = document.getElementById("saveButton"); // save button

			var tableMov = $('#example').tableToJSON(); // the table is passed to JSON format

			if (fre.firstChild.data == "Congelar") {
				if (validateMovement(tableMov)) {
						but.disabled = true;
						item.disabled = true;
						qty.disabled = true;
						sBut.disabled = false;

						$(function(){
						  $("button.removebutton").attr("disabled", true);
						  $("button.buttlots").attr("disabled", true);
						});

						var tableMov = $('#example').tableToJSON(); // the table is passed to JSON format
						var serverData = new ServerData(datas.lotRows, tableMov);
						setData(serverData); //the table is sends to the server

						fre.innerHTML = "Descongelar";
				}
			}
			else {
				but.disabled = false;
				item.disabled = false;
				qty.disabled = false;
				sBut.disabled = true;

				$(function(){
					$("button.removebutton").attr("disabled", false);
					$("button.buttlots").attr("disabled", false);
				});

				setData("");

				fre.innerHTML = "Congelar";
			}
		}

	</script>
@endsection

@include('wms.movs.lotrows')
