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
                  {!! Form::label('po', null, ['class'=>'form-control input-sm',
                                                'id' => 'po']) !!}
                </div>
                <div class="col-md-5">
                  {!! Form::label('po_type', trans('mms.labels.PRODUCTION_ORDER_TYPE')) !!}
                  {!! Form::label('po_type', null, ['class'=>'form-control input-sm',
                                                'id' => 'po_type']) !!}
                </div>
              </div>
              <div class="row">
                <div class="col-md-9">
                  {!! Form::label('item', trans('wms.labels.MAT_PROD')) !!}
                  {!! Form::label('item', null, ['class'=>'form-control input-sm',
                                                'id' => 'item']) !!}
                </div>
                <div class="col-md-3">
                  {!! Form::label('po_qty', trans('mms.labels.ORDER_QUANTITY')) !!}
                  {!! Form::label('po_qty', null, ['class'=>'form-control input-sm',
                                                'id' => 'po_qty']) !!}
                </div>
              </div>
            </div>
            <div class="col-md-2">
              {!! Form::label('po_date', trans('siie.labels.DATE')) !!}
              {!! Form::label('po_date', null, ['class'=>'form-control input-sm',
                                                    'id' => 'po_date']) !!}
              {!! Form::label('unit', trans('wms.labels.UNIT')) !!}
              {!! Form::label('unit', null, ['class'=>'form-control input-sm',
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
                            <th>{{ trans('wms.labels.MOVEMENT') }}</th>
                            <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                            <th>{{ trans('wms.labels.PALLET') }}</th>
                            <th>{{ trans('wms.labels.LOT') }}</th>
                            <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                            <th>{{ trans('userinterface.labels.BRANCH') }}</th>
                            <th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
                            <th>{{ trans('userinterface.labels.LOCATION') }}</th>
                            <th>{{ trans('wms.labels.INPUTS') }}</th>
                            <th>{{ trans('wms.labels.OUTPUTS') }}</th>
                            <th>{{ trans('wms.labels.UN') }}</th>
                            <th>{{ '$'.trans('siie.labels.DEBITS') }}</th>
                            <th>{{ '$'.trans('siie.labels.CREDITS') }}</th>
                            <th>{{ '$'.trans('siie.labels.BALANCE') }}</th>
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
