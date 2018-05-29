@extends('templates.basic_form')

@section('head')
	@include('templates.head')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)
@section('titlepanel', $title)

@section('content')
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-8">
				<div class="form-group">
					{!! Form::label('warehouse', trans('userinterface.labels.WAREHOUSE').'*') !!}
					{!! Form::select('warehouse', $lWarehouses, session('whs')->id_whs, ['class'=>'form-control select-one',
																				'placeholder' => trans('wms.placeholders.SELECT_WAREHOUSE'),
																				'required', 'id' => 'warehouse']) !!}
				</div>
			</div>
			<div class="col-md-1">
					<div class="form-group">
						{!! Form::label(trans('...')) !!}
						{!! Form::button(trans('actions.READ'), ['class' => 'btn btn-primary',
																										'onclick' => 'readWhsStk()',
																										'id' => 'read_btn']) !!}
					</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::label(trans('En almacén:')) !!}
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<table id="stk_whs_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
				    <thead>
				        <tr class="titlerow">
				            <th data-priority="1">{{ trans('userinterface.labels.CODE') }}</th>
				            <th data-priority="1">{{ trans('wms.labels.MAT_PROD') }}</th>
				            <th>{{ trans('wms.labels.LOCATION') }}</th>
				            <th>{{ trans('wms.labels.PALLET') }}</th>
				            <th>{{ trans('wms.labels.LOT') }}</th>
				            <th>{{ trans('wms.labels.EXPIRATION') }}</th>
				            <th data-priority="1">{{ trans('wms.labels.AVAILABLE') }}</th>
				            <th>{{ trans('wms.labels.STOCK') }}</th>
				            <th>{{ trans('wms.labels.SEGREGATED') }}</th>
				            <th data-priority="1">{{ trans('wms.labels.UNIT') }}</th>
								</tr>
						</thead>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				{!! Form::open(['route' => 'wms.movs.store', 'method' => 'POST']) !!}
					<div class="row">
						<div class="col-md-1">
						</div>
						<div class="col-md-6">
							<div class="form-group">
								{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
								{!! Form::date('dt_date', session('work_date'),
																					['class'=>'form-control',
																					'id' => 'dt_date']) !!}
							</div>
						</div>
					</div>

					{!! Form::hidden('mvt_whs_class_id', \Config::get('scwms.MVT_CLS_OUT'), ['id' => 'mvt_whs_class_id']) !!}
					{!! Form::hidden('movement_object', null, ['id' => 'movement_object']) !!}
						<div class="form-group" align="right">
							<a id="idFreeze" class="btn btn-info" onclick="unfreeze()" role="button">{{ trans('actions.FREEZE') }}</a>
							{!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled']) !!}
							<input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="window.history.back();"/>
						</div>
				{!! Form::close() !!}
			</div>
		</div>
@endsection

@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/Movements.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/wms/inventories/stock_whs.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/wms/inventories/SInventories.js')}}"></script>
		<script type="text/javascript">

				function GlobalData() {
						this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;

						var qty = <?php echo json_encode(session('decimals_qty')) ?>;
						var amt = <?php echo json_encode(session('decimals_amt')) ?>;
						var loc = <?php echo json_encode(session('location_enabled')) ?>;
						this.DEC_QTY = parseInt(qty);
						this.DEC_AMT = parseInt(amt);
						this.LOCATION_ENABLED = (parseInt(loc) == 1);
				}

				var globalData = new GlobalData();

		</script>
@endsection
