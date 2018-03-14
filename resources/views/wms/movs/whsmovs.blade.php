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
@endif

@section('content')
{!! Form::open(
	['route' => 'wms.movs.store', 'method' => 'POST', 'id' => 'theForm']
	) !!}
  <div class="row">
    <div class="col-md-6">
			@if (isset($oMovement->folio))
				<div class="form-group">
					{!! Form::label('folio', trans('userinterface.labels.MVT_FOLIO').'*') !!}
					{!! Form::text('folio', $oMovement->folio, ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.FOLIO'), 'readonly']) !!}
				</div>
			@endif

      <div class="form-group">
				{!! Form::hidden('mvt_whs_class_id', $oMovement->mvt_whs_class_id) !!}
  			{!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE').'*') !!}
				{!! Form::select('mvt_whs_type_id', $movTypes, $oMovement->mvt_whs_type_id, ['class'=>'form-control select-one',
																															'id' => 'mvt_whs_type_id',
																															'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'), 'disabled']) !!}
  		</div>

      <div class="form-group">
  			{!! Form::label('mvt_com', trans('userinterface.labels.MVT_SUB_TYPE').'*') !!}
				{!! Form::select('mvt_com', $mvtComp, $iMvtSubType, ['class'=>'form-control select-one',
																															'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'),
																															'required', 'id' => 'mvt_com',
																															isset($oMovement->id_mvt) ? 'disabled' : '']) !!}
  		</div>

    </div>
    <div class="col-md-6">
			<div class="row">
				<div class="col-md-12">

					<div class="form-group">
						{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
						{!! Form::date('dt_date',
								isset($oMovement->dt_date) ? $oMovement->dt_date : session('work_date'),
																											['class'=>'form-control',
																											'id' => 'dt_date',
																											isset($oMovement->id_mvt) ? 'readonly' : '']) !!}
					</div>

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								@if (App\SUtils\SGuiUtils::isWhsShowed($oMovement->mvt_whs_class_id, $oMovement->mvt_whs_type_id, 'whs_src'))
										{!! Form::label('whs_src', trans('userinterface.labels.MVT_WHS_SRC').'*') !!}
										{!! Form::select('whs_src', $warehouses, $whs_src, ['class'=>'form-control border_red select-one',
																				'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required',
																				isset($oMovement->id_mvt) ? 'disabled' : '']) !!}
								@endif
								@if (App\SUtils\SGuiUtils::isWhsShowed($oMovement->mvt_whs_class_id, $oMovement->mvt_whs_type_id, 'whs_des'))
										{!! Form::label('whs_des', ($oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ?
											trans('wms.labels.WAREHOUSE') :
													trans('userinterface.labels.MVT_WHS_DEST')).'*') !!}
										{!! Form::select('whs_des', $warehouses, $whs_des, ['class'=>'form-control select-one',
																					'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required',
																					isset($oMovement->id_mvt) ? 'disabled' : '']) !!}
								@endif
							</div>
						</div>
						<div id="div_modify" style="display: none;">
							<br />
							<br />
							<button style="float: right;" onclick="modifyHeader()"
							type="button" class="btn btn-danger">{{ trans('actions.MODIFY') }}</button>
						</div>

						<div id="div_continue">
							<br />
							<br />
							<br />
							<button style="float: right;" onclick="validateHeader()" id="butContinue"
												type="button" class="btn btn-primary">{{ trans('actions.CONTINUE') }}</button>
						</div>
					</div>
					<div class="row">

					</div>
				</div>
			</div>

    </div>
  </div>
	<div id="div_rows" style="display: none;">
		<div class="row">
			<div class="col-md-12">
						<div class="row">
							@if (session('location_enabled'))
								<div class="col-md-3">
									{!! Form::label(trans('actions.SEARCH_LOCATION').'...') !!}
										{!! Form::text('location',
											isset($whs) ? $whs->code : null , ['class'=>'form-control',
											'id' => 'location',
											'placeholder' => trans('userinterface.placeholders.CODE'),
											'onkeypress' => 'searchLoc(event)']) !!}
								</div>
								<div class="col-md-1">
									{!! Form::label('.') !!}
									<button type="button"
									class="btn btn-warning"
									data-toggle="modal"
									data-target="#location_search">Ubica.</button>
								</div>
								<div class="col-md-3">
									{!! Form::label('Ubicación') !!}
									{!! Form::label('label_loc', '--',
																			['class' => 'form-control',
																			'id' => 'label_loc']) !!}
								</div>
							@endif
						</div>
						<div class="row">
							@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_ADJ') ||
										$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_ADJ'))
									<div class="col-md-3">
										{!! Form::label(trans('actions.SEARCH').'...') !!}
										{!! Form::text('item', null, ['class'=>'form-control',
																										'id' => 'item',
																										'placeholder' => trans('userinterface.placeholders.CODE'),
																										'onkeypress' => 'searchElem(event)']) !!}
									</div>
									<div class="col-md-1">
											{!! Form::label('.') !!}
											<button type="button" class="btn btn-info"
															onclick="showItems()">{{ trans('actions.SEARCH') }}</button>
									</div>
							@endif
							<div class="col-md-6">
								{!! Form::label('seleccionado') !!}
								{!! Form::label('label_sel', '--',
																		['class' => 'form-control',
																		'id' => 'label_sel']) !!}
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
									{!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
									{!! Form::number('quantity', 0, ['class'=>'form-control', 'id' => 'quantity',
																												'placeholder' => trans('userinterface.placeholders.QUANTITY'),
																												'style' => 'text-align: right;',
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ||
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_OUT') ? 'disabled' : '']) !!}
							</div>
							<div class="col-md-1">
								{!! Form::label('Un.') !!}
								{!! Form::label('label_unit', '--',
																		['class' => 'form-control',
																		'id' => 'label_unit']) !!}
							</div>
							<div class="col-md-2">
									{!! Form::label('item', trans('userinterface.labels.PRICE').'*') !!}
									{!! Form::number('price', 1, ['class'=>'form-control', 'id' => 'price',
																												'placeholder' => trans('userinterface.placeholders.PRICE'),
																												'style' => 'text-align: right;',
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ||
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_OUT') ? 'disabled' : '']) !!}
							</div>
							<div class="col-md-1">
								{!! Form::label('Mon.') !!}
								{!! Form::label('label_cur', session('currency')->code,
																		['class' => 'form-control',
																		'id' => 'label_cur']) !!}
							</div>
							<div class="col-md-1" id="div_pallets">
								{!! Form::label(trans('.')) !!}
								<button type="button" id="btn_pallet" class="btn btn-secondary" onclick="showPalletModal()">
												{{ trans('wms.labels.PALLET') }}
								</button>
							</div>
							<div class="col-md-1" id="div_lots">
								{!! Form::label(trans('.')) !!}
								<button type="button" id="btn_lots" class="btn btn-secondary" onclick="showLotsModal()">
												{{ trans('wms.labels.LOTS') }}
								</button>
							</div>
							<div class="col-md-2">
								{!! Form::label('.') !!}
									<div class="row">
										<div class="col-md-8" id="div_add">
											<button id="tButton" onclick="addElement()" type="button" class="btn btn-primary buttonlarge">{{ trans('actions.ADD') }}</button>
										</div>
										<div class="col-md-4">
											<a onclick="cleanPanel()" title="{{ trans('actions.CLEAN') }}" class="btn btn-default">
												<span class="glyphicon glyphicon-erase" aria-hidden = "true"/>
											</a>
										</div>
									</div>
							</div>
						</div>
			</div>
		</div>
		@if (App\SUtils\SGuiUtils::showPallet($oMovement->mvt_whs_type_id))
			<label style="color: #0200e6">{{ App\SUtils\SGuiUtils::getLabelOfPallet($oMovement->mvt_whs_type_id) }}</label>
			@include('wms.movs.pallet')
			<br />
			<label style="color: #0200e6">{{ trans('wms.labels.ELEMENTS_TO_MOVE') }}</label>
		@endif
	</div>
	<br />
	<div class="row">

		<div class="col-md-2" id="div_delete" style="display: none;">
			<button id="delButton" onclick="deleteElement()" type="button" class="btn btn-danger">{{ trans('actions.QUIT') }}</button>
		</div>
		@if($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT'))
			<div class="col-md-2">
				<button id="stkButton" type='button' onClick='stockComplete()'
							class='butstk btn btn-success'
							data-toggle='modal' data-target='#stock_com_modal'
							title='Ver existencias'>{{ trans('wms.WHS_IN_STK') }}
				</button>
			</div>
		@endif
		@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR'))
			<div class="col-md-2" id="div_setdata" style="display: none;">
				<button id="sData" type='button' onClick='setRowData()'
							class='btn btn-success'
							title='Agregar datos'>Data
				</button>
			</div>
		@endif
	</div>
  <div class="row">
    <div class="col-xs-12">
			<div class="form-group">
					@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_ADJ') ||
								$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_ADJ'))
							@include('wms.movs.tables.adjustments')
					@elseif ($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR'))
							@include('wms.movs.tables.others')
					@endif
			</div>
    </div>
  </div>
	{!! Form::hidden('movement_object', null, ['id' => 'movement_object']) !!}
	<div class="form-group" align="right">
		<a id="idFreeze" class="btn btn-info" onclick="unfreeze()" role="button">{{ trans('actions.FREEZE') }}</a>
		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled']) !!}
		<input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="location.href='{{ route('wms.movs.docs') }}'"/>
	</div>
{!! Form::close() !!}
@endsection

@section('js')
	@include('templates.scriptsmovs')
	<script>

		function GlobalData () {
		  this.oDocument = <?php echo json_encode($oDocument); ?>;
		  this.lDocData = <?php echo json_encode($lDocData); ?>;
		  this.iOperation = <?php echo json_encode($iOperation); ?>;
		  this.iMovId = <?php echo json_encode($oMovement->id_mvt); ?>;
		  this.iMvtClass = <?php echo json_encode($oMovement->mvt_whs_class_id); ?>;
		  this.iMvtType = <?php echo json_encode($oMovement->mvt_whs_type_id); ?>;
			this.bIsInputMov = <?php echo json_encode($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')); ?>;
			this.lFItems = [];
			this.lFLots = [];
			this.lFPallets = [];
			this.lFStock = <?php echo json_encode($lStock != null ? $lStock : array()); ?>;
			this.lFSrcLocations = [];
			this.lFDesLocations = [];
			this.lElementsType = <?php echo json_encode(\Config::get('scwms.ELEMENTS_TYPE')) ?>;
			this.lOperationType = <?php echo json_encode(\Config::get('scwms.OPERATION_TYPE')) ?>;
			this.lOperation = <?php echo json_encode(\Config::get('scwms.OPERATION')) ?>;

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
			this.dPerSupp = <?php echo json_encode(($dPerSupp/100)); ?>; //percentage of supply permitted
		}

		var globalData = new GlobalData();
		headerCore.initializeStock();
		if (! globalData.LOCATION_ENABLED) {
		    oMovsTable.column( 4 ).visible( false );
		}

		// movement.iDocumentId = globalData.oDocument != 0 ? globalData.oDocument.id_document : 0;
		var lDocRows = <?php echo json_encode($lDocData) ?>;

		if (lDocRows.length > 0) {
				headerCore.transformServerToClientDocRows(lDocRows);
		}

		var lRows = <?php echo json_encode($oMovement->rows) ?>;

		if (lRows.length > 0) {
				headerCore.transformServerToClientRows(lRows);
		}

		if (localStorage.getItem('movement') !== null) {
			var errors = <?php echo json_encode($errors->all()) ?>;
			console.log(errors);

			if (errors.length > 0) {
				console.log("here again");
				var retrievedObject = localStorage.getItem('movement');
				console.log(JSON.parse(retrievedObject));
				// movement = setMovement(JSON.parse(retrievedObject));

				console.log('cargar movimiento');

				if (movement.iWhsDes != 0) {
					document.getElementById('whs_des').value = movement.iWhsDes;
					$('#whs_des').prop('disabled', true).trigger("chosen:updated");
				}
				if (movement.iWhsSrc != 0) {
					document.getElementById('whs_src').value = movement.iWhsSrc;
					$('#whs_src').prop('disabled', true).trigger("chosen:updated");
				}

				// if (globalData.isPalletReconfiguration) {
				// 	if (localStorage.getItem('pallet') !== null) {
				// 		var oPalletSaved = localStorage.getItem('pallet');
				// 		console.log(JSON.parse(oPalletSaved));
        //
				// 		oPalletRow = JSON.parse(oPalletSaved);
				// 		updatePallet(oPalletRow, globalData.iMvtType);
				// 	}
				// }
				// else {
				// 	oPalletRow = '';
				// }

				// movement.rows.forEach(function(element) {
				// 		var type = 0;
				// 		if(element.iPalletId > 1) {
				// 				type = globalData.IS_PALLET;
				// 		}
				// 		else if(element.lotRows.length == 0){
				// 				type = globalData.IS_ITEM;
				// 		}
				// 		else {
				// 			type = globalData.IS_LOT;
				// 		}
        //
				//     addRowTr(element.iIdRow, element,
				// 								(globalData.bIsInputMov ? movement.iWhsDes : movement.iWhsSrc),
				// 								type);
				// });

				unfreeze();
				// updateProgressbar();
			}

			localStorage.removeItem('movement');
		}

		$('.select-one').chosen({
			placeholder_select_single: 'Seleccione un item...'
		});

	</script>
@endsection

@include('wms.movs.lotrows')
@include('siie.items.itemsearch')
@include('wms.locs.locationsearch')
@include('wms.movs.lotsmodal')
@include('wms.movs.palletmodal')
@include('wms.movs.stockmodal')
@include('wms.movs.stockcompletemodal')
