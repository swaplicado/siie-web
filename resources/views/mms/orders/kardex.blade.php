<!-- Modal -->
<div id="poKardexModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('mms.labels.PO_KARDEX') }}</h4>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-7">
              <div class="row">
                <div class="col-md-7">
                  {!! Form::label('po', trans('mms.labels.PRODUCTION_ORDER')) !!}
                  {!! Form::text('po', null, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'po']) !!}
                </div>
                <div class="col-md-5">
                  {!! Form::label('po_type', trans('mms.labels.PRODUCTION_ORDER_TYPE')) !!}
                  {!! Form::text('po_type', null, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'po_type']) !!}
                </div>
              </div>
              <div class="row">
                <div class="col-md-9">
                  {!! Form::label('item', trans('wms.labels.MAT_PROD')) !!}
                  {!! Form::text('item', null, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'item']) !!}
                </div>
                <div class="col-md-3">
                  {!! Form::label('po_qty', trans('mms.labels.ORDER_QUANTITY')) !!}
                  {!! Form::text('po_qty', null, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'po_qty']) !!}
                </div>
              </div>
            </div>
            <div class="col-md-2">
              {!! Form::label('po_date', trans('siie.labels.DATE')) !!}
              {!! Form::text('po_date', null, ['class'=>'form-control input-sm',
                                                    'readonly',
                                                    'id' => 'po_date']) !!}
              {!! Form::label('unit', trans('wms.labels.UNIT')) !!}
              {!! Form::number('unit', null, ['class'=>'form-control input-sm',
                                                'readonly',
                                                'id' => 'unit']) !!}
            </div>
            <div class="col-md-3">
              {!! Form::label('charges', trans('siie.labels.CHARGES').'*') !!}
              {!! Form::number('charges', 0, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'style' => 'text-align: right; color: green;',
                                              'id' => 'charges']) !!}
              {!! Form::label('payments', trans('siie.labels.PAYMENTS')) !!}
              {!! Form::number('payments', 0, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'style' => 'text-align: right; color: red;',
                                              'id' => 'payments']) !!}
              {!! Form::label('balance', trans('siie.labels.BALANCE')) !!}
              {!! Form::number('balance', 0, ['class'=>'form-control input-sm',
                                              'readonly',
                                              'style' => 'text-align: right; color: blue;',
                                              'id' => 'balance']) !!}
            </div>
          </div>
          <div class="row">
              <div class="col-md-12">
                <table id="po_kardex_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
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
