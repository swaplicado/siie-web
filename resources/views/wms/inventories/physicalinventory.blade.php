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
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
					{!! Form::date('dt_date', session('work_date'),
																										['class'=>'form-control input-sm',
																										'id' => 'dt_date']) !!}
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					{!! Form::label('whs', trans('userinterface.labels.WAREHOUSE').'*') !!}
					{!! Form::select('whs', $warehouses, session('whs')->id_whs,
															['class'=>'form-control select-one',
															'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required']) !!}
				</div>
			</div>
			<div class="col-md-1 col-md-offset-1">
				<div class="form-group">
					{!! Form::label('.', '----',['style' => 'color: white;']) !!}
					{!! Form::button(trans('actions.CONTINUE'),
															['class'=>'btn btn-primary',
															'id' => 'continue']) !!}
				</div>
			</div>
		</div>
		<hr>
		@include('wms.movs.subviews.searchpanel')
	</div>
	<div class="row">
	  {!! Form::open(['route' => 'wms.inventory.physicalinventory.create', 'method' => 'POST']) !!}
			<div class="form-group" align="right">
				{!! Form::submit(trans('actions.GENERATE'), ['class' => 'btn btn-primary', 'id' => 'saveButton']) !!}
				<input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="window.history.back();"/>
			</div>
		{!! Form::close() !!}
	</div>
@endsection

@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/Movements.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/wms/inventories/SInitialInventory.js')}}"></script>
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
