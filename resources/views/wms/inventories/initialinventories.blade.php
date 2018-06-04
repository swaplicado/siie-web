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
    <div class="col-md-12">
      {!! Form::open(['route' => 'wms.inventory.initialinventory.store', 'method' => 'POST']) !!}
        <div class="row">
          <div class="col-md-2">
          </div>
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('year', trans('userinterface.labels.YEAR').'*') !!}
              {!! Form::number('year', session('work_date')->year,
                                        ['class'=>'form-control',
                                        'id' => 'year',
                                        'required']) !!}
            </div>
          </div>
        </div>
          <div class="form-group" align="right">
            {!! Form::submit(trans('actions.GENERATE'), ['class' => 'btn btn-primary', 'id' => 'saveButton']) !!}
            <input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger" onClick="window.history.back();"/>
          </div>
      {!! Form::close() !!}
    </div>
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
