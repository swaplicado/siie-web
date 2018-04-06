@extends('templates.formmovs')

@section('head')
	@include('templates.headmovs')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

@section('content')
@if ($iOperation == \Config::get('scwms.OPERATION_TYPE.CREATION'))
	{!! Form::open(
		['route' => 'wms.movs.store', 'method' => 'POST', 'id' => 'theForm']
		) !!}
@elseif ($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION'))
	{!! Form::open(
		['route' => ['wms.movs.update', $oMovement->id_mvt], 'method' => 'POST', 'id' => 'theForm']
		) !!}
@endif
	@if (is_object($oDocument))
			@include('wms.movs.docheader')
	@endif
  <div class="row">
    <div class="col-md-6">
			@if (isset($oMovement->folio))
				<div class="form-group">
					{!! Form::label('folio', trans('userinterface.labels.MVT_FOLIO').'*') !!}
					{!! Form::text('folio', $oMovement->folio, ['class'=>'form-control input-sm', 'placeholder' => trans('userinterface.placeholders.FOLIO'), 'readonly']) !!}
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
																															isset($oMovement->id_mvt) ||
																															$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_TRA') ?
																															'disabled' : '']) !!}
  		</div>

    </div>
    <div class="col-md-6">
			<div class="row">
				<div class="col-md-12">

					<div class="form-group">
						{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
						{!! Form::date('dt_date',
								isset($oMovement->dt_date) ? $oMovement->dt_date : session('work_date'),
																											['class'=>'form-control input-sm',
																											'id' => 'dt_date',
																											isset($oMovement->id_mvt) ? 'readonly' : '']) !!}
					</div>

					<div class="row">
						@include('wms.movs.subviews.whss')
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
							<br />
							<br />
						</div>
					</div>
					<div class="row">

					</div>
				</div>
			</div>

    </div>
  </div>
	<div class="row">
		@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR') ||
					$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_SAL'))
			<div class="col-md-1" id="div_setdata" style="display: none;">
				<button id="sData" type='button' onClick='setRowData()'
							class='btn btn-success'
							title='{{ trans('actions.SUPPLY') }}'>{{ trans('actions.SUPPLY') }}
				</button>
			</div>
		@endif
		@if ($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR') ||
					$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_SAL'))
			<div class="col-md-12">
					@include('wms.movs.tables.others')
			</div>
		@endif
	</div>
	<div id="div_rows" style="display: none;">
		<div class="row">
			<div class="col-md-12">
						@include('wms.movs.search.locations')
						<div class="row">
							@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_ADJ') ||
										$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_ADJ') ||
										$oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_TRA') ||
										($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION')))
									<div class="col-md-3">
										{!! Form::label(trans('actions.SEARCH').'...') !!}
										{!! Form::text('item', null, ['class'=>'form-control input-sm',
																										'id' => 'item',
																										'placeholder' => trans('userinterface.placeholders.CODE'),
																										'onkeypress' => 'searchElem(event)']) !!}
									</div>
									<div class="col-md-1">
											{!! Form::label('.') !!}
											<button type="button" class="btn btn-info" data-toggle="modal"
												data-target="#mat_prod_search">{{ trans('actions.SEARCH') }}
											</button>
									</div>
							@endif
							<div class="col-md-6">
								{!! Form::label('seleccionado') !!}
								{!! Form::label('label_sel', '--',
																		['class' => 'form-control input-sm',
																		'id' => 'label_sel']) !!}
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
									{!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
									{!! Form::number('quantity', 0, ['class'=>'form-control input-sm', 'id' => 'quantity',
																												'placeholder' => trans('userinterface.placeholders.QUANTITY'),
																												'style' => 'text-align: right;',
																												'step' => '0.01',
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ||
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_OUT') ? 'disabled' : '']) !!}
							</div>
							<div class="col-md-1">
								{!! Form::label('Un.') !!}
								{!! Form::label('label_unit', '--',
																		['class' => 'form-control input-sm',
																		'id' => 'label_unit']) !!}
							</div>
							<div class="col-md-2">
									{!! Form::label('price', trans('userinterface.labels.PRICE').'*') !!}
									{!! Form::number('price', 1, ['class'=>'form-control input-sm', 'id' => 'price',
																												'placeholder' => trans('userinterface.placeholders.PRICE'),
																												'style' => 'text-align: right;',
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ||
																												$oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_OUT') ? 'disabled' : '']) !!}
							</div>
							<div class="col-md-1">
								{!! Form::label('Mon.') !!}
								{!! Form::label('label_cur', session('currency')->code,
																		['class' => 'form-control input-sm',
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
		<div class="col-md-6">
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
			</div>
		</div>
		<div id="info_div" style="display: none;" class="col-md-6">
			<div class="row">
				<div class="col-md-offset-6 col-md-3">
					{!! '$ '.Form::label('Monto') !!}
					{!! Form::label('label_amt', '--',
															['class' => 'form-control input-sm',
																'style' => 'text-align: right; color: blue;',
																'id' => 'label_amt']) !!}
				</div>
				<div class="col-md-3">
					{!! Form::label('Cantidad') !!}
					{!! Form::label('label_qty', '--',
															['class' => 'form-control input-sm',
																'style' => 'text-align: right; color: blue;',
																'id' => 'label_qty']) !!}
				</div>
			</div>
		</div>
	</div>
  <div class="row">
    <div class="col-xs-12">
			<div class="form-group">
					@if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_TRA'))
							@include('wms.movs.tables.whstransfers')
					@else
							@include('wms.movs.tables.adjustments')
					@endif
			</div>
    </div>
  </div>
	@if (is_object($oDocument))
			<div class="row">
				<div class="col-md-6  col-md-offset-5">
						Porcentaje de surtido
				</div>
			</div>
			<div class="progress">
			  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"></div>
			</div>
	@endif
	{!! Form::hidden('movement_object', null, ['id' => 'movement_object']) !!}
	<div class="form-group" align="right">
		<a id="idFreeze" style="display:none" class="btn btn-info" onclick="unfreeze()" role="button">{{ trans('actions.FREEZE') }}</a>
		{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled']) !!}
		<input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="window.history.back();"/>
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
			this.lOperation = <?php echo json_encode(\Config::get('scwms.OPERATION')) ?>; //input-output
			this.bIsExternalTransfer = <?php echo json_encode($bIsExternalTransfer) ?>;

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

			this.lItemLinks = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK')); ?>;

			this.lContainers = <?php echo json_encode(\Config::get('scwms.CONTAINERS')); ?>;

			var qty = <?php echo json_encode(session('decimals_qty')) ?>;
			var amt = <?php echo json_encode(session('decimals_amt')) ?>;
			var loc = <?php echo json_encode(session('location_enabled')) ?>;
			this.DEC_QTY = parseInt(qty);
			this.DEC_AMT = parseInt(amt);
			this.LOCATION_ENABLED = (parseInt(loc) == 1);
			this.isPalletReconfiguration = this.iMvtType == this.PALLET_RECONFIG_IN || this.iMvtType == this.PALLET_RECONFIG_OUT;
			this.dPerSupp = <?php echo json_encode(($dPerSupp/100)); ?>; //percentage of supply permitted

			this.sRoute = '';
	    if (this.iOperation == this.lOperationType.EDITION) {
	        this.sRoute = 'edit';
	    }
	    else if (this.oDocument != 0) {
	        this.sRoute = 'supply';
	    }
	    else {
	        this.sRoute = 'create';
	    }
		}

		var globalData = new GlobalData();
		headerCore.initializeStock();
		if (! globalData.LOCATION_ENABLED) {
		    oMovsTable.column( 4 ).visible( false );
		}

		var lDocRows = <?php echo json_encode($lDocData) ?>;

		// if (lDocRows.length > 0) {
		// 		headerCore.transformServerToClientDocRows(lDocRows);
		// }

		var lRows = <?php echo json_encode($oMovement->rows) ?>;

		if (lRows.length > 0) {
				headerCore.transformServerToClientRows(lRows);
		}

		if (localStorage.getItem('movement') !== null) {
			var errors = <?php echo json_encode($errors->all()) ?>;
			console.log(errors);

			if (errors.length > 0) {
				var retrievedObject = localStorage.getItem('movement');
				var movement = JSON.parse(retrievedObject);
				oMovement = loadMovement(movement);

				document.getElementById('mvt_com').value = oMovement.iMvtSubType;

				if (oMovement.iWhsDes != 0) {
					document.getElementById('whs_des').value = oMovement.iWhsDes;
				}
				if (movement.iWhsSrc != 0) {
					document.getElementById('whs_src').value = oMovement.iWhsSrc;
				}

				guiValidations.disableHeader();
			}

			localStorage.removeItem('movement');
		}

		$('.select-one').chosen({
			placeholder_select_single: 'Seleccione un item...'
		});

		if (globalData.iMvtType == globalData.MVT_TP_IN_PUR) {
				progressBar.updateProgressbar();
		}

	</script>
@endsection

@include('wms.movs.lotrows')
@include('wms.movs.search.itemsearch')
@include('wms.movs.search.items')
@include('wms.locs.locationsearch')
@include('wms.locs.locationsearchdes')
@include('wms.movs.lotsmodal')
@include('wms.movs.palletmodal')
@include('wms.movs.stockmodal')
@include('wms.movs.stockcompletemodal')
