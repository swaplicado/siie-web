@extends('templates.formmovs')

@section('head')
	@include('templates.stock.headstock')
@endsection

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('title', $sTitle)

@section('content')
<div>
    <div class="row">
      <div class="col-md-7">
        <div class="row">
                <div class="col-md-10">
                  {!! Form::label('item', trans('wms.labels.MAT_PROD')) !!}
                  {!! Form::text('item', $data->item->name, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'item']) !!}
                </div>
                <div class="col-md-2">
                  {!! Form::label('unit', trans('wms.labels.UNIT')) !!}
                  {!! Form::text('unit', $data->unit->name, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'unit']) !!}
                </div>
              </div>

                <div class="row">
                  <div class="col-md-6">
                    {!! Form::label('lot', 'Lote') !!}
                    {!! Form::text('lot', $data->lot, ['class'=>'form-control input-sm',
                                                                  'readonly',
                                                                  'id' => 'element_type']) !!}
                  </div>
                  <div class="col-md-6">
                    {!! Form::label('label_expiration', trans('wms.labels.EXPIRATION')) !!}
                    {!! Form::text('expiration', session('work_date'), ['class'=>'form-control input-sm',
                                                          'readonly',
                                                          'id' => 'expiration']) !!}
                  </div>
                </div>
            </div>
            <div class="col-md-2">
              {!! Form::label('cutoff_date', trans('wms.labels.CUTOFF_DATE')) !!}
              {!! Form::text('cutoff_date', session('work_date')->toDateString(), ['class'=>'form-control input-sm',
                                                    'readonly',
                                                    'id' => 'cutoff_date']) !!}
              {!! Form::label('year', trans('userinterface.labels.YEAR')) !!}
              {!! Form::number('year', session('work_date')->year, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'year']) !!}
            </div>
            <div class="col-md-3">
              {!! Form::label('inputs', trans('wms.labels.INPUTS').'*') !!}
              {!! Form::number('inputs', $dInputs, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'style' => 'text-align: right; color: green;',
                                              'id' => 'inputs']) !!}
              {!! Form::label('outputs', trans('wms.labels.OUTPUTS')) !!}
              {!! Form::number('outputs', $dOutputs, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'style' => 'text-align: right; color: red;',
                                              'id' => 'outputs']) !!}
              {!! Form::label('stock', trans('wms.labels.STOCK')) !!}
              {!! Form::number('stock', $dStock, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'style' => 'text-align: right; color: blue;',
                                              'id' => 'stock']) !!}
            </div>
          </div>
          <br>
          <div class="row">
            <div class="col-md-12">
              <table id="kardex_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
                  <thead>
                      <tr class="titlerow">
                          <th data-priority="1">{{ '#' }}</th>
                          <th>{{ trans('userinterface.labels.DATE') }}</th>
                          <th>{{ trans('userinterface.labels.FOLIO') }}</th>
                          <th>{{ trans('wms.labels.MVT_TYPE') }}</th>
                          <th>{{ trans('wms.labels.LOT_PALLET') }}</th>
                          <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                          <th>{{ trans('userinterface.labels.BRANCH') }}</th>
                          <th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
                          <th>{{ trans('userinterface.labels.LOCATION') }}</th>
                          <th>{{ trans('wms.labels.INPUTS') }}</th>
                          <th>{{ trans('wms.labels.OUTPUTS') }}</th>
                          <th>{{ trans('wms.labels.STOCK') }}</th>
                          <th>{{ trans('wms.labels.UN') }}</th>
                          <th>{{ '$'.trans('siie.labels.DEBITS') }}</th>
                          <th>{{ '$'.trans('siie.labels.CREDITS') }}</th>
                          <th>{{ '$'.trans('siie.labels.BALANCE') }}</th>
                          <th>{{ trans('siie.labels.ORDER') }}</th>
                          <th>{{ trans('siie.labels.INVOICE') }}</th>
                          <th>{{ trans('siie.labels.C_N') }}</th>
                      </tr>
                  </thead>
                  <tbody>
                    <?php $contador=0?>
                    @foreach ($query as $row)
                      <?php $contador++?>
                    <tr>
                      <td>{{ $contador }}</td>
                      <td>{{ $row->dt_date }}</td>
                      <td>{{ $row->folio}}</td>
                      <td>{{ $row->mvt_name}}</td>
                      <td>{{ $row->lot}}</td>
                      <td>{{ $row->dt_expiry}}</td>
                      <td>{{ $row->branch_code}}</td>
                      <td>{{ $row->whs_code}}</td>
                      <td>{{ $row->loc_code}}</td>
                      <td>{{ $row->inputs}}</td>
                      <td>{{ $row->outputs}}</td>
                      <td>{{ $row->stock}}</td>
                      <td>{{ $row->unit_code}}</td>
                      <td>{{ $row->debit}}</td>
                      <td>{{ $row->credit}}</td>
                      <td>{{ $row->balance}}</td>
                      <td>{{ $row->num_order}}</td>
                      <td>{{ $row->ser_num_invoice}}</td>
                      <td>{{ $row->num_cn}}</td>
                    </tr>
                    @endforeach
                  </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default">Regresar</button>
        </div>
@endsection
@section('js')
	@include('templates.stock.scriptsstock')
	<script src="{{ asset('datatables/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('datatables/buttons.flash.min.js') }}"></script>
	<script src="{{ asset('datatables/jszip.min.js') }}"></script>
	<script src="{{ asset('datatables/pdfmake.min.js') }}"></script>
	<script src="{{ asset('datatables/vfs_fonts.js') }}"></script>
	<script src="{{ asset('datatables/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('datatables/buttons.print.min.js') }}"></script>


@endsection
