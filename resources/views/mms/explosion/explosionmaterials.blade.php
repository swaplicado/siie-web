@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', $sTitle.' '.session('utils')->formatFolio($oProductionPlan->folio).'/'.$oProductionPlan->production_plan)
@section('titlepanel', $sTitle.' '.session('utils')->formatFolio($oProductionPlan->folio).'/'.$oProductionPlan->production_plan)

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('content')
  <div class="row">
    <div class="col-md-3">
      <div class="form-group">
        {!! Form::label('branch', trans('wms.labels.BRANCH')	) !!}
        {!! Form::label('branch', session('branch')->code.'-'.session('branch')->name,
                        ['class' => 'form-control input-sm']) !!}
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group">
        {!! Form::label('dt_date', trans('userinterface.labels.DATE').'*') !!}
        {!! Form::date('dt_date', $sDate,
                                          ['class'=>'form-control input-sm',
                                          'readonly']) !!}
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group">
        {!! Form::label('production_plan', trans('mms.labels.PRODUCTION_PLAN')	) !!}
        {!! Form::label('production_plan', $oProductionPlan->id_production_plan > 0 ?
                                            session('utils')->formatFolio($oProductionPlan->folio).'-'.$oProductionPlan->production_plan
                                            : '----',
                        ['class' => 'form-control input-sm']) !!}
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        {!! Form::label('warehouses', trans('wms.labels.WAREHOUSES')	) !!}
        {!! Form::label('warehouses', sizeof($sWarehouses) > 0 ? $sWarehouses : '----',
                        ['class' => 'form-control input-sm']) !!}
      </div>
    </div>
    <div class="col-md-1">
      <div class="form-group">
        {!! Form::label('.', '.', ['style' => 'color:white']) !!}
        {!! Form::button(trans('mms.SEE_PRODUCTION_ORDERS'),
                                                ['class' => 'btn btn-primary',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#orders_modal']) !!}
      </div>
    </div>
    <div class="col-md-1 col-md-offset-1">
      <div class="form-group">
        {!! Form::label('.', '.', ['style' => 'color:white']) !!}
        {!! Form::button(trans('wms.WHS_STK'), ['class' => 'btn btn-success',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#stock_modal']) !!}
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      {!! Form::label('comments', trans('userinterface.labels.COMMENTS')) !!}
      {!! Form::textarea('comments', null, ['class'=>'form-control',
                                              'id' => 'comments',
                                              'readonly',
                                              'rows' => 2, 'cols' => 40]) !!}
    </div>
  </div>
  <hr>
  <div class="row">
      <div class="col-md-12">
        <table id="explosion_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
            <thead>
                <tr class="titlerow">
                    <th data-priority="1" style="text-align: center;">{{ trans('siie.labels.KEY') }}</th>
                    <th>{{ trans('mms.labels.SUPPLIES') }}</th>
                    <th>{{ trans('wms.labels.UN') }}</th>
                    <th>{{ trans('mms.labels.NEED') }}</th>
                    <th>{{ trans('wms.labels.AVAILABLE') }}</th>
                    <th>{{ trans('wms.labels.STOCK') }}</th>
                    <th>{{ trans('wms.labels.SEGREGATED') }}</th>
                    <th>{{ trans('wms.labels.BACKORDER') }}</th>
                    <th>{{ trans('siie.labels.PROVIDER') }}</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($lExplosion as $oExpRow)
                <tr>
                    <td>{{ $oExpRow->item_code }}</td>
                    <td>{{ $oExpRow->item }}</td>
                    <td>{{ $oExpRow->unit_code }}</td>
                    <td align="right">{{ session('utils')->
                        formatNumber($oExpRow->dRequiredQuantity,
                        \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td align="right">{{ session('utils')->
                        formatNumber(($oExpRow->dStock - $oExpRow->dSegregated),
                        \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td align="right">{{ session('utils')->
                        formatNumber($oExpRow->dStock,
                        \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td align="right">{{ session('utils')->
                        formatNumber($oExpRow->dSegregated,
                        \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td align="right">{{ session('utils')->
                        formatNumber($oExpRow->dBackOrder,
                        \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td>{{ $oExpRow->sPartner }}</td>
                </tr>
              @endforeach
            </tbody>
        </table>
      </div>
  </div>
@endsection
@include('mms.explosion.stock')
@include('mms.explosion.orders')

@section('js')
  <script src="{{ asset('js/mms/explosion/tables.js') }}" charset="utf-8"></script>
  <script type="text/javascript">
  </script>
@endsection
