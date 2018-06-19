<!-- Modal -->
<div id="theKardex" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Tarjeta auxiliar de almacén</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-7">
            <div class="row">
              <div class="col-md-12">
                {!! Form::label('warehouse', trans('wms.labels.WAREHOUSE')) !!}
                {!! Form::text('warehouse', $lWarehouses[$iFilterWhs], ['class'=>'form-control input-sm',
                                              'readonly',
                                              'id' => 'warehouse']) !!}
              </div>
            </div>
            <div class="row">
              <div class="col-md-10">
                {!! Form::label('item', trans('wms.labels.MAT_PROD')) !!}
                {!! Form::text('item', null, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'id' => 'item']) !!}
              </div>
              <div class="col-md-2">
                {!! Form::label('unit', trans('wms.labels.UNIT')) !!}
                {!! Form::text('unit', null, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'id' => 'unit']) !!}
              </div>
            </div>

              <div class="row">
                <div class="col-md-6">
                  {!! Form::label('type_label', '', ['id' => 'type_label']) !!}
                  {!! Form::text('element_type', null, ['class'=>'form-control input-sm',
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
            {!! Form::number('inputs', 0, ['class'=>'form-control input-sm',
                                            'readonly',
                                            'style' => 'text-align: right; color: green;',
                                            'id' => 'inputs']) !!}
            {!! Form::label('outputs', trans('wms.labels.OUTPUTS')) !!}
            {!! Form::number('outputs', 0, ['class'=>'form-control input-sm',
                                            'readonly',
                                            'style' => 'text-align: right; color: red;',
                                            'id' => 'outputs']) !!}
            {!! Form::label('stock', trans('wms.labels.STOCK')) !!}
            {!! Form::number('stock', 0, ['class'=>'form-control input-sm',
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

                </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>

  </div>
</div>
