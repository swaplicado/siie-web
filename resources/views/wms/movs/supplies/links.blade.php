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
			{!! Form::label('Documento destino:') !!}
			@include('wms.movs.docheader')
			<br />
			<div class="row">
				<div class="col-md-12">
						<button type="button" onclick="setDataToLink()" class="btn btn-success">{{ trans('actions.LINK') }}</button>
				</div>
			</div>
      <div class="row">
				<div class="col-md-12">
						@include('wms.movs.tables.others')
				</div>
      </div>
			<div class="row">
				<div class="col-md-7">
					{!! Form::label(trans('wms.labels.MAT_PROD')) !!}
					{!! Form::text('item', '--', ['class'=>'form-control', 'id' => 'item', 'placeholder' => trans('wms.labels.MAT_PROD').'...', 'readonly']) !!}
				</div>
				<div class="col-md-3">
					{!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
					{!! Form::number('quantity', 0, ['class'=>'form-control', 'id' => 'quantity',
																								'placeholder' => trans('userinterface.placeholders.QUANTITY'),
																								'style' => 'text-align: right;', 'readonly']) !!}
				</div>
				<div class="col-md-2">
					{!! Form::label(trans('userinterface.labels.UNIT')) !!}
					{!! Form::text('unit', '--', ['class'=>'form-control', 'id' => 'unit', 'placeholder' => trans('userinterface.labels.UNIT').'...', 'readonly']) !!}
				</div>
			</div>
			<br />
			<div class="row" id="div_actions">
				<div class="col-md-1 col-md-offset-10">
						<input type="button" onclick="assignAll()" value="{{ trans('userinterface.labels.ALL_SIN') }}" class="btn btn-primary"/>
				</div>
				<div class="col-md-1">
						<input type="button" onclick="assignNothing()" value="{{ trans('userinterface.labels.NOTHING') }}" class="btn btn-warning"/>
				</div>
			</div>
			<br />
      <div class="row">
				@include('wms.movs.supplies.linklots')
				<div class="col-md-12">
					<table id="movs_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
							<thead>
									<tr class="titlerow">
											<th>id_mov</th>
											<th>id_mov_row</th>
											<th>{{ trans('userinterface.labels.BRANCH') }}</th>
											<th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
											<th data-priority="1">{{ trans('userinterface.labels.LOCATION') }}</th>
											<th>{{ trans('wms.labels.PALLET') }}</th>
											<th>{{ trans('userinterface.labels.PRICE') }}</th>
											<th>{{ trans('userinterface.labels.QUANTITY') }}</th>
											<th>{{ trans('wms.labels.INDIRECT_SUPPLY')  }}</th>
											<th>{{ trans('wms.labels.PENDING')  }}</th>
											<th>{{ trans('wms.labels.LINKED') }}</th>
											<th>{{ trans('wms.labels.LOTS') }}</th>
									</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
      	</div>
      </div>
      <div class="row">
        {!! Form::open(['route' => 'wms.docs.storelinks', 'method' => 'POST']) !!}
          {!! Form::hidden('spackage_object', null, ['id' => 'spackage_object']) !!}
          <div class="form-group" align="right">
            <input type="button" onclick="freezeMovement()" value="{{ trans('actions.FREEZE') }}" id="idFreezeMov" class="btn btn-info"/>
            {!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveMovButton', 'disabled']) !!}
            <input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger"/>
          </div>
        {!! Form::close() !!}
      </div>
@endsection

@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/links/SLinksCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/links/SGuiLink.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/links/SLinkLotsCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/links/movstable.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/links/lotstable.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/links/SPackage.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/tables.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/Movements.js')}}"></script>
		<script type="text/javascript">

				function GlobalData() {
						this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;
						this.lServerMovements = <?php echo json_encode($lMovements) ?>;
						this.lMovements = null;
						this.oDocumentSrc = <?php echo json_encode($oDocumentSrc) ?>;
						this.oDocumentDes = <?php echo json_encode($oDocumentDes) ?>;
						this.lDocRows = <?php echo json_encode($lDocData) ?>;

						var qty = <?php echo json_encode(session('decimals_qty')) ?>;
						var amt = <?php echo json_encode(session('decimals_amt')) ?>;
						var loc = <?php echo json_encode(session('location_enabled')) ?>;
						this.DEC_QTY = parseInt(qty);
						this.DEC_AMT = parseInt(amt);
						this.LOCATION_ENABLED = (parseInt(loc) == 1);
				}

				var globalData = new GlobalData();

				globalData.lMovements = linksCore.serverToClientMovements(globalData.lServerMovements);
				guiLink.hideActions();

		</script>
@endsection
