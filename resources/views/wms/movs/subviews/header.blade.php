@if (is_object($oDocument))
    @include('wms.movs.docheader')
@endif
<div class="row">
  <div class="col-md-6">
    @if (isset($oMovement->folio))
      <div class="form-group">
        {!! Form::label('folio', trans('userinterface.labels.MVT_FOLIO').'*') !!}
        {!! Form::text('folio', $oMovement->folio, ['class'=>'form-control input-sm',
                                'placeholder' => trans('userinterface.placeholders.FOLIO'),
                                'readonly']) !!}
      </div>
    @endif

    <div class="form-group">
      {!! Form::hidden('mvt_whs_class_id', $oMovement->mvt_whs_class_id) !!}
      {!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE').'*') !!}
      {!! Form::select('mvt_whs_type_id', $movTypes, $oMovement->mvt_whs_type_id, ['class'=>'form-control select-one',
                                                            'id' => 'mvt_whs_type_id',
                                                            'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'),
                                                            'disabled']) !!}
    </div>

    <div class="form-group">
      {!! Form::label('mvt_com', trans('userinterface.labels.MVT_SUB_TYPE').'*') !!}
      {!! Form::select('mvt_com', $mvtComp, $iMvtSubType, ['class'=>'form-control select-one',
                                                            'placeholder' => trans('userinterface.placeholders.SELECT_MVT_TYPE'),
                                                            'required', 'id' => 'mvt_com',
                                                            isset($oMovement->id_mvt) ||
                                                            $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_TRA')	||
                                                            App\SUtils\SGuiUtils::showPallet($oMovement->mvt_whs_type_id) ?
                                                            'disabled' : '']) !!}
    </div>

    @if ('1' == '1')
      <div class="form-group">
        {!! Form::button(trans('mms.labels.PRODUCTION_ORDER'),
                                    ['class' => 'btn btn-info',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#po_modal',
                                    'id' => 'po_btn'
                                    ]) !!}
      </div>
    @endif


  </div>
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-12">

        <div class="form-group">
          {!! Form::label('dt_date', trans('userinterface.labels.MVT_DATE').'*') !!}
          {!! Form::date('dt_date',
              isset($oMovement->dt_date) ? $oMovement->dt_date : session('work_date'),
                                                    ['class'=>'form-control input-sm',
                                                    'id' => 'dt_date',
                                                    isset($oMovement->id_mvt) ? 'readonly' : '']) !!}
        </div>

        <div class="row">
          @include('wms.movs.subviews.whss')
          {{-- @if (App\SUtils\SGuiUtils::showPallet($oMovement->mvt_whs_type_id))
            @include('wms.movs.search.palletsearch')
          @endif --}}
          <div id="div_modify" style="display: none;">
            <br />
            <br />
            <button style="float: right;" onclick="modifyHeader()"
            type="button" class="btn btn-danger">{{ trans('actions.MODIFY') }}</button>
          </div>

          <div id="div_continue">
            <br />
            <br />
            <br />
            <button style="float: right;" onclick="validateHeader()" id="butContinue"
                      type="button" class="btn btn-primary">{{ trans('actions.CONTINUE') }}</button>
            <br />
            <br />
          </div>
        </div>
        <div class="row">

        </div>
      </div>
    </div>

  </div>
</div>
<hr />
