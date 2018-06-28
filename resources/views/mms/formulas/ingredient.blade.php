<!-- Modal -->
<div id="modalIngredient" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar ingrediente</h4>
      </div>
      <div id='app' class="modal-body">
        <div class="row">
          <div class="col-md-3">
            {!! Form::label('mat_prod', trans('wms.labels.MAT_PROD').'*') !!}
          </div>
          <div class="col-md-9">
            {!! Form::select('mat_prod', $lMaterialsList, null,
                      ['class'=>'form-control select-one ing-chos cls-ing', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD'),
                       'onChange' => 'setIngredientData(this)']) !!}
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('wms.labels.ITEM_TYPE')) !!}
          </div>
          <div class="col-md-9">
            {!! Form::text('item_type', null, ['class'=>'form-control', 'id' => 'item_type',
                                                    'placeholder' => trans('wms.placeholders.ITEM_TYPE'), 'readonly']) !!}
          </div>
        </div>
        <br />
        <div class="row" id="div_formula" style="display: none;">
          <div class="col-md-3">
            {!! Form::label(trans('mms.labels.FORMULA').'*') !!}
          </div>
          <div class="col-md-9">
            {!! Form::select('sel_formula', array(), null,
                      ['class'=>'form-control', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD'),
                       'onChange' => '', 'id' => 'sel_formula']) !!}
          </div>
        </div>
        {{-- <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('userinterface.labels.DATE_START').'*') !!}
          </div>
          <div class="col-md-4">
            {!! Form::date('dt_start_ing', session('work_date'), ['class'=>'form-control', 'id' => 'dt_start_ing']) !!}
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('userinterface.labels.DATE_END').'*') !!}
          </div>
          <div class="col-md-4">
            {!! Form::date('dt_end_ing', session('work_date'), ['class'=>'form-control', 'id' => 'dt_end_ing']) !!}
          </div>
        </div> --}}
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('quantityIngredient', 0, ['class'=>'form-control', 'id' => 'quantityIngredient',
                                              'style' => 'text-align: right;',
                                              'placeholder' => trans('userinterface.placeholders.QUANTITY')]) !!}
          </div>
          <div class="col-md-2">
            {!! Form::label('lUnitIngredient', '-',  ['class' => 'form-control',
                                                        'id' => 'lUnitIngredient']) !!}
          </div>
          <div class="col-md-3">
            {!! Form::label('lBulk', '-',  ['class' => 'form-control',
                                                        'id' => 'lBulk']) !!}
          </div>
        </div>
        {{-- <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('userinterface.labels.COST').'*') !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('costIngredient', 0, ['class'=>'form-control', 'id' => 'costIngredient',
                                              'style' => 'text-align: right;',
                                              'placeholder' => trans('userinterface.placeholders.COST')]) !!}
          </div>
          <div class="col-md-2">
            {!! Form::label('', '$', ['class'=>'form-control']) !!}
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('mms.labels.DURATION')) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('duration', 0, ['class'=>'form-control', 'id' => 'duration',
                                              'style' => 'text-align: right;',
                                              'placeholder' => trans('mms.placeholders.DURATION')]) !!}
          </div>
          <div class="col-md-2">
            {!! Form::label('Hrs', '', ['class'=>'form-control']) !!}
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('mms.labels.SUBSTITUTE')) !!}
          </div>
          <div class="col-md-9">
            {!! Form::select('substitute', $lMaterialsList, null,
                      ['class'=>'form-control select-one ing-chos cls-subs', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD'),
                       'onChange' => '', 'id' => 'substitute']) !!}
          </div>
        </div> --}}
        {{-- <br />
        <div class="row" id="div_formula_subs" style="display: none;">
          <div class="col-md-3">
            {!! Form::label(trans('mms.labels.FORMULA').'*') !!}
          </div>
          <div class="col-md-9">
            {!! Form::select('sel_formula_subs', array(), null,
                      ['class'=>'form-control', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD'),
                       'onChange' => '', 'id' => 'sel_formula_subs']) !!}
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('mms.labels.SUGGESTED_MIX')) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('suggested', 0, ['class'=>'form-control', 'id' => 'suggested',
                                              'style' => 'text-align: right;',
                                              'placeholder' => trans('userinterface.placeholders.PERCENTAGE')]) !!}
          </div>
          <div class="col-md-2">
            {!! Form::label('', '%', ['class'=>'form-control']) !!}
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-3">
            {!! Form::label(trans('mms.labels.MAX_MIX')) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('max', 0, ['class'=>'form-control', 'id' => 'max',
                                              'style' => 'text-align: right;',
                                              'placeholder' => trans('userinterface.placeholders.PERCENTAGE')]) !!}
          </div>
          <div class="col-md-2">
            {!! Form::label('', '%', ['class'=>'form-control']) !!}
          </div>
        </div>

      </div> --}}
      <div class="modal-footer">
        <button  id="closeIngredient" type="button" class="btn btn-success" data-dismiss="modal">{{ trans('actions.ADD') }}</button>
      </div>
    </div>

    </div>
  </div>
</div>
