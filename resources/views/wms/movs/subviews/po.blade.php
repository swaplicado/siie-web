<!-- Modal -->
<div id="po_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('mms.labels.PRODUCTION_ORDER') }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('src_po', $iAssType == \Config::get('scmms.ASSIGN_TYPE.MP') ?
                                        trans('mms.labels.PRODUCTION_ORDER') :
                                        trans('mms.labels.SRC_PRODUCTION_ORDER').'*') !!}
              {!! Form::select('src_po', $lSrcPO, $iSrcPO, ['class'=>'form-control select-one',
                                                              'onChange' => 'updatePOs()',
                                                              'style' => 'text-align: right',
                                                              'id' => 'src_po']) !!}
            </div>
          </div>
          {{-- <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('des_po_label', $iAssType == \Config::get('scmms.ASSIGN_TYPE.MP') ?
                                        '--' :
                                        trans('mms.labels.DES_PRODUCTION_ORDER' ).'*') !!}

            <select class='form-control' name="des_po" id="des_po" value="">
  							<option value="">{{ trans('mms.labels.DES_PRODUCTION_ORDER') }}</option>
    				</select>
            </div>
          </div> --}}
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              {!! Form::label('src_item', $iAssType == \Config::get('scmms.ASSIGN_TYPE.MP') ?
                                        trans('mms.labels.PRODUCT') :
                                        trans('mms.labels.SRC_ITEM').'*') !!}
              {!! Form::label('src_item', '--', ['class'=>'form-control',
                                                  'id' => 'src_item']) !!}
            </div>
          </div>
          {{-- <div class="col-md-6">
            <div class="form-group">
              {!! Form::label('des_item', $iAssType == \Config::get('scmms.ASSIGN_TYPE.MP') ?
                                        '--' :
                                        trans('mms.labels.DES_ITEM').'*') !!}
              {!! Form::label('des_item', '--', ['class'=>'form-control',
                                                  'id' => 'des_item']) !!}
            </div>
          </div> --}}
        </div>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('po_reference', trans('siie.labels.REFERENCE')) !!}
              {!! Form::text('po_reference', null, ['class'=>'form-control',
                                                  'id' => 'po_reference']) !!}
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              {!! Form::label('po_comments', trans('siie.labels.COMMENTS')) !!}
              {!! Form::textarea('po_comments', null, ['class'=>'form-control',
                                                      'id' => 'po_comments',
                                                      'rows' => 2, 'cols' => 40]) !!}
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('actions.ACCEPT') }}</button>
      </div>
    </div>

  </div>
</div>
