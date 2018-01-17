@extends('templates.formmovs')

@section('head')
	@include('templates.headmovs')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', trans('userinterface.titles.WHS_MOVS'))

@if (is_object($oDocument))
	@section('progressbar')
		<div class="row">
			<div class="col-md-6  col-md-offset-5">
					Porcentaje de surtido
			</div>
		</div>
		<div class="progress">
		  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"></div>
		</div>
	@endsection
	@section('docrows')
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<table id="docTable" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="50%">
				    <thead>
				        <tr class="titlerow">
				            <th>Cve</th>
				            <th>Concepto</th>
				            <th>Clase</th>
				            <th>Cant.</th>
				            <th>Pendiente</th>
										<th>Un.</th>
				            <th>P.U.$</th>
				        </tr>
				    </thead>
				    <tbody>
							@foreach ($lDocData as $row)
								<tr>
				            <td class="small">{{ $row->concept_key }}</td>
				            <td class="small">{{ $row->concept }}</td>
				            <td class="small">{{ $row->class_name }}</td>
										<td class="small" align="right">{{ session('utils')->formatNumber($row->qty_row, \Config::get('scsiie.FRMT.QTY')) }}</td>
										<td class="small" align="right">{{ session('utils')->formatNumber($row->pending, \Config::get('scsiie.FRMT.QTY')) }}</td>
				            <td class="small">{{ $row->unit }}</td>
				            <td class="small" align="right">{{ session('utils')->formatNumber($row->price_unit_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
				        </tr>
							@endforeach
				    </tbody>
				</table>
			</div>
		</div>
	@endsection
@endif

@section('content')
{!! Form::open(
	['route' => 'wms.movs.store', 'method' => 'POST', 'id' => 'theForm']
	) !!}
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
				{!! Form::hidden('mvt_whs_class_id', $oMovType->mvt_class_id) !!}
  			{!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE').'*') !!}
				{!! Form::select('mvt_whs_type_id', $movTypes, $oMovType->id_mvt_type, ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'), 'disabled']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('mvt_com', trans('userinterface.labels.MVT_TYPE').'*') !!}
				{!! Form::select('mvt_com', $mvtComp, $iMvtSubType, ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'), 'required', ]) !!}
  		</div>

      {{-- <div class="form-group">
  			{!! Form::label('folio', trans('userinterface.labels.MVT_FOLIO').'*') !!}
  			{!! Form::text('folio', null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.FOLIO'), 'required', 'unique']) !!}
  		</div> --}}

			<div class="form-group">
  			{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
  			{!! Form::date('dt_date', \Carbon\Carbon::now(), ['class'=>'form-control']) !!}
  		</div>

    </div>
    <div class="col-md-6">

			<div class="form-group">
				@if (App\SUtils\SGuiUtils::isWhsShowed($oMovType->mvt_class_id, $oMovType->id_mvt_type, 'whs_src'))
						{!! Form::label('whs_src', trans('userinterface.labels.MVT_WHS_SRC').'*') !!}
						{!! Form::select('whs_src', $warehouses, $whs_src, ['class'=>'form-control border_red select-one',
																																	'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required']) !!}
				@endif
				@if (App\SUtils\SGuiUtils::isWhsShowed($oMovType->mvt_class_id, $oMovType->id_mvt_type, 'whs_des'))
		  			{!! Form::label('whs_des', ($oMovType->id_mvt_type == \Config::get('scwms.PALLET_RECONFIG_IN') ?
							trans('wms.labels.WAREHOUSE') :
									trans('userinterface.labels.MVT_WHS_DEST')).'*') !!}
						{!! Form::select('whs_des', $warehouses, $whs_des, ['class'=>'form-control select-one',
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
																											'placeholder' => trans('userinterface.placeholders.QUANTITY'),
																											$oMovType->id_mvt_type == \Config::get('scwms.PALLET_RECONFIG_IN') ||
																											$oMovType->id_mvt_type == \Config::get('scwms.PALLET_RECONFIG_OUT') ? 'disabled' : '']) !!}
						</div>
					  <div class="col-md-3">
								<button id="tButton" type="button" class="btn btn-primary">{{ trans('actions.ADD') }}</button>
						</div>
					</div>
			</div>
    </div>
  </div>
	@if (App\SUtils\SGuiUtils::showPallet($oMovType->id_mvt_type))
		<label style="color: #0200e6">{{ App\SUtils\SGuiUtils::getLabelOfPallet($oMovType->id_mvt_type) }}</label>
		@include('wms.movs.pallet')
		<br />
		<label style="color: #0200e6">{{ trans('wms.labels.ELEMENTS_TO_MOVE') }}</label>
	@endif
  <div class="row">
    <div class="col-xs-12">
			<div class="form-group">
				<table id="example" class="table table-bordered display responsive no-wrap" cellspacing="0" width="100%">
						<thead>
								<tr class="titlerow">
										<th style="display:none;"></th>
										<th>{{ trans('wms.labels.CODE') }}</th>
										<th>{{ trans('wms.labels.MAT_PROD') }}</th>
										<th>{{ trans('wms.labels.UNIT') }}</th>
										<th>{{ trans('wms.labels.LOCATION') }}</th>
										<th>{{ trans('wms.labels.PALLET') }}</th>
										<th>{{ trans('wms.labels.PRICE') }}</th>
										<th>{{ trans('wms.labels.QTY') }}</th>
										<th>{{ trans('wms.labels.LOT') }}</th>
										<th>{{ trans('wms.labels.STOCK') }}</th>
										<th>-</th>
								</tr>
						</thead>
						<tbody id="lbody">
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

		function GlobalData () {
		  this.oDocument = <?php echo json_encode($oDocument); ?>;
		  this.lLots = <?php echo json_encode($lots); ?>;
		  this.lPallets = <?php echo json_encode($pallets); ?>;
			this.lWarehouses = <?php echo json_encode($warehousesObj); ?>;
		  this.lLocations = <?php echo json_encode($locations); ?>;
		  this.lItemContainers = <?php echo json_encode($itemContainers); ?>;
		  this.bIsInputMov = <?php echo json_encode($oMovType->mvt_class_id != \Config::get('scwms.MVT_CLS_OUT')); ?>;
		  this.iMvtClass = <?php echo json_encode($oMovType->mvt_class_id); ?>;
		  this.iMvtType = <?php echo json_encode($oMovType->id_mvt_type); ?>;
		  this.IS_ITEM = 1;
		  this.IS_LOT = 2;
		  this.IS_PALLET = 3;

		  this.MVT_CLS_IN = <?php echo json_encode(\Config::get('scwms.MVT_CLS_IN')) ?>; //
		  this.MVT_CLS_OUT = <?php echo json_encode(\Config::get('scwms.MVT_CLS_OUT')) ?>; //

		  this.MVT_TP_IN_SAL = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_SAL')) ?>;
		  this.MVT_TP_IN_PUR = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_PUR')) ?>;
		  this.MVT_TP_IN_ADJ = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_ADJ')) ?>;
		  this.MVT_TP_IN_TRA = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_TRA')) ?>; // transfer (traspaso)
		  this.MVT_TP_IN_CON = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_CON')) ?>; // conversion
		  this.MVT_TP_IN_PRO = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_PRO')) ?>; // production
		  this.MVT_TP_IN_EXP = <?php echo json_encode(\Config::get('scwms.MVT_TP_IN_EXP')) ?>; // expenses
		  this.MVT_TP_OUT_SAL = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_SAL')) ?>;
		  this.MVT_TP_OUT_PUR = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_PUR')) ?>;
		  this.MVT_TP_OUT_ADJ = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_ADJ')) ?>;
		  this.MVT_TP_OUT_TRA = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_TRA')) ?>;
		  this.MVT_TP_OUT_CON = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_CON')) ?>;
		  this.MVT_TP_OUT_PRO = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_PRO')) ?>;
		  this.MVT_TP_OUT_EXP = <?php echo json_encode(\Config::get('scwms.MVT_TP_OUT_EXP')) ?>;

		  this.PALLET_RECONFIG_IN  =  <?php echo json_encode(\Config::get('scwms.PALLET_RECONFIG_IN')) ?>;
		  this.PALLET_RECONFIG_OUT  =  <?php echo json_encode(\Config::get('scwms.PALLET_RECONFIG_OUT')) ?>;

			this.LINK_ALL = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.ALL')); ?>;
			this.LINK_CLASS = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.CLASS')); ?>;
			this.LINK_TYPE = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.TYPE')); ?>;
			this.LINK_FAMILY = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.FAMILY')); ?>;
			this.LINK_GROUP = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.GROUP')); ?>;
			this.LINK_GENDER = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.GENDER')); ?>;
			this.LINK_ITEM = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.ITEM')); ?>;

			this.CONTAINER_NA = <?php echo json_encode(\Config::get('scwms.CONTAINERS.NA')); ?>;
	    this.CONTAINER_LOCATION = <?php echo json_encode(\Config::get('scwms.CONTAINERS.LOCATION')); ?>;
	    this.CONTAINER_WAREHOUSE = <?php echo json_encode(\Config::get('scwms.CONTAINERS.WAREHOUSE')); ?>;
	    this.CONTAINER_BRANCH = <?php echo json_encode(\Config::get('scwms.CONTAINERS.BRANCH')); ?>;
	    this.CONTAINER_COMPANY = <?php echo json_encode(\Config::get('scwms.CONTAINERS.COMPANY')); ?>;

			var qty = <?php echo json_encode(session('decimals_qty')) ?>;
			var amt = <?php echo json_encode(session('decimals_amt')) ?>;
			var loc = <?php echo json_encode(session('location_enabled')) ?>;
			this.DEC_QTY = parseInt(qty);
			this.DEC_AMT = parseInt(amt);
			this.LOCATION_ENABLED = (parseInt(loc) == 1);
			this.isPalletReconfiguration = this.iMvtType == this.PALLET_RECONFIG_IN || this.iMvtType == this.PALLET_RECONFIG_OUT;
		}

		var globalData = new GlobalData();
		movement.iDocumentId = globalData.oDocument != 0 ? globalData.oDocument.id_document : 0;

		if (localStorage.getItem('movement') !== null) {
			var errors = <?php echo json_encode($errors->all()) ?>;
			console.log(errors);

			if (errors.length > 0) {
				console.log("here again");
				var retrievedObject = localStorage.getItem('movement');
				console.log(JSON.parse(retrievedObject));
				movement = setMovement(JSON.parse(retrievedObject));

				if (movement.iWhsDes != 0) {
					document.getElementById('whs_des').value = movement.iWhsDes;
					$('#whs_des').prop('disabled', true).trigger("chosen:updated");
				}
				if (movement.iWhsSrc != 0) {
					document.getElementById('whs_src').value = movement.iWhsSrc;
					$('#whs_src').prop('disabled', true).trigger("chosen:updated");
				}

				if (globalData.iMvtType == globalData.PALLET_RECONFIG_IN) {
					oPalletRow = movement.auxPalletRow;
				}
				else {
					oPalletRow = '';
				}

				movement.rows.forEach(function(element) {
						var type = 0;
						if(element.iPalletId > 1) {
								type = globalData.IS_PALLET;
						}
						else if(element.lotRows.length == 0){
								type = globalData.IS_ITEM;
						}
						else {
							type = globalData.IS_LOT;
						}

				    addRowTr(element.iIdRow, element,
												(globalData.bIsInputMov ? movement.iWhsDes : movement.iWhsSrc),
												type);
				});

				unfreeze();
				updateProgressbar();
			}

			localStorage.removeItem('movement');
		}


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

		$('.select-one').chosen({
			placeholder_select_single: 'Seleccione un item...'
		});

	</script>
@endsection

@include('wms.movs.lotrows')
@include('wms.movs.stock')
