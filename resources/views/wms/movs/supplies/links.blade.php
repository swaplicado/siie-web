@extends('templates.basic_form')

@section('head')
	@include('templates.head')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $title)

@section('content')
      <div class="row">

      </div>
      <div class="row">
				<div class="col-md-12">
						@include('wms.movs.tables.others')
				</div>
      </div>
			<div class="row">
				<div class="col-md-7">
					{!! Form::label(trans('wms.labels.MAT_PROD')) !!}
					{!! Form::text('item', '--', ['class'=>'form-control', 'placeholder' => trans('wms.labels.MAT_PROD').'...', 'readonly']) !!}
				</div>
				<div class="col-md-3">
					{!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
					{!! Form::number('quantity', 0, ['class'=>'form-control', 'id' => 'quantity',
																								'placeholder' => trans('userinterface.placeholders.QUANTITY'),
																								'style' => 'text-align: right;', 'readonly']) !!}
				</div>
				<div class="col-md-2">
					{!! Form::label(trans('userinterface.labels.UNIT')) !!}
					{!! Form::text('unit', '--', ['class'=>'form-control', 'placeholder' => trans('userinterface.labels.UNIT').'...', 'readonly']) !!}
				</div>
			</div>
			<br />
      <div class="row">
				<div class="col-md-12">
					<table id="movs_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
							<thead>
									<tr class="titlerow">
											<th>{{ trans('userinterface.labels.BRANCH') }}</th>
											<th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
											<th data-priority="1">{{ trans('userinterface.labels.LOCATION') }}</th>
											<th>{{ trans('wms.labels.PALLET') }}</th>
											<th>{{ trans('userinterface.labels.QUANTITY') }}</th>
											<th>{{ trans('wms.labels.ASSIGNED') }}</th>
											<th>{{ trans('wms.labels.LOTS') }}</th>
									</tr>
							</thead>
							<tbody>
								<?php
									$i = 0;
								?>
								@foreach ($lMovements as $oMov)
										@foreach ($oMov->rows as $oRow)
											<tr>
												<td>{{ strtoupper($oMov->branch->name) }}</td>
												<td>{{ strtoupper($oMov->warehouse->name) }}</td>
												<td>{{ strtoupper($oRow->location->name) }}</td>
												<td>{{ strtoupper($oRow->pallet->pallet) }}</td>
												<td>{{ session('utils')->formatNumber($oRow->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
												<td>{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
												<td>@if ($oRow->item->is_lot)
						                    <button type='button' onClick='viewLots({{ $i }})'
						                          class='btn btn-primary btn-md'
						                          title='Ver lotes'>
						                      <i class='glyphicon glyphicon-info-sign'></i>
						                    </button>
						                @else
															{!! Form::number('assigned', 0, ['class'=>'form-control', 'id' => 'assigned',
																														'placeholder' => trans('userinterface.placeholders.QUANTITY'),
																														'style' => 'text-align: right;']) !!}
						                @endif
						            </td>
											</tr>
											<?php
					              $i++;
					            ?>
										@endforeach
								@endforeach
							</tbody>
						</table>
      	</div>
      </div>
      <div class="row">
        {!! Form::open() !!}
          {!! Form::hidden('movement_object', null, ['id' => 'movement_object']) !!}
          <div class="form-group" align="right">
            <input type="button" value="{{ trans('actions.FREEZE') }}" id="idFreeze" class="btn btn-info"/>
            {!! Form::submit(trans('actions.SAVE'), ['class' => 'btn btn-primary', 'id' => 'saveButton', 'disabled']) !!}
            <input type="button" value="{{ trans('actions.CANCEL') }}" class="btn btn-danger"/>
          </div>
        {!! Form::close() !!}
      </div>
@endsection

@section('js')
		<script type="text/javascript" src="{{ asset('js/movements/links/SLinksCore.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/links/movstable.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/tables.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/movements/Movements.js')}}"></script>
		<script type="text/javascript">

				function GlobalData() {
						this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;
						this.lServerMovements = <?php echo json_encode($lMovements) ?>;
				}

				var globalData = new GlobalData();

				linksCore.serverToClientMovements(globalData.lServerMovements);

		</script>
@endsection
