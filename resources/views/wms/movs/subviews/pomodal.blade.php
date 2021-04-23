<!-- Modal -->
<div id="prodOrderModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('mms.labels.PRODUCTION_ORDER') }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              {!! Form::label('formula', trans('mms.labels.FORMULA')) !!}
              {!! Form::label('formula', null, ['class'=>'form-control',
                                                              'id' => 'formula']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('plan', trans('mms.labels.PRODUCTION_PLAN')) !!}
              {!! Form::label('plan', null, ['class'=>'form-control',
                                              'style' => 'text-align: right;',
                                              'id' => 'plan']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7">
            <div class="form-group">
              {!! Form::label('identifier', trans('mms.labels.PRODUCTION_ORDER')) !!}
              {!! Form::label('identifier', null, ['class'=>'form-control',
                                                              'id' => 'identifier']) !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('order_type', trans('mms.labels.PRODUCTION_ORDER_TYPE')) !!}
              {!! Form::label('order_type', null, ['class'=>'form-control',
                                                              'id' => 'order_type']) !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('order_date', trans('siie.labels.DATE')) !!}
              {!! Form::label('order_date', null, ['class'=>'form-control',
                                                              'id' => 'order_date']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('order_item', trans('mms.labels.PO_ITEM')) !!}
              {!! Form::label('order_item', null,
                                                        ['class'=>'form-control',
                                                      'id' => 'order_item']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-5">
            <div class="form-group">
              {!! Form::label('charges', trans('wms.labels.QTY')) !!}
              {!! Form::number('charges', null, ['class'=>'form-control',
                                                  'style' => 'text-align: right;',
                                                  'readonly',
                                                  'id' => 'charges']) !!}
            </div>
          </div>
          <div class="col-md-5">
            <div class="form-group">
              {!! Form::label('delivered', trans('mms.labels.DELIVERED')) !!}
              {!! Form::number('delivered', 0, ['class'=>'form-control',
                                                  'style' => 'text-align: right;',
                                                  'readonly',
                                                  'id' => 'delivered']) !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('po_unit', trans('wms.labels.UN')) !!}
              {!! Form::label('po_unit', 0, ['class'=>'form-control',
                                                  'style' => 'text-align: center;',
                                                  'readonly',
                                                  'id' => 'po_unit']) !!}
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>

  </div>
</div>
