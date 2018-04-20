<div class="row">
  <div class="col-md-4">
    {!! Form::label('', trans('wms.labels.SOURCE_BRANCH')) !!}
    {!! Form::label('lab_branch_src', $oMovRef->branch->name, ['class'=>'form-control input-sm']) !!}
  </div>
  <div class="col-md-4">
    {!! Form::label('', trans('userinterface.labels.MVT_DATE')) !!}
    {!! Form::label('lab_branch_src', $oMovRef->dt_date, ['class'=>'form-control input-sm']) !!}
  </div>
  <div class="col-md-4">
    {!! Form::label('', trans('wms.labels.SOURCE_BRANCH')) !!}
    {!! Form::label('lab_branch_src', $oMovRef->userCreation->username, ['class'=>'form-control input-sm']) !!}
  </div>
</div>
<hr>
<div class="row">
  <div class="col-md-2">
    {!! Form::label('mvt_whs_type_id', trans('userinterface.labels.MVT_TYPE')) !!}
    {!! Form::label('mvt_whs_type_id', 'ENTRADA TRASPASO', ['class'=>'form-control input-sm']) !!}
  </div>
  <div class="col-md-2">
    {!! Form::label('dt_date', trans('wms.labels.RECEPTION_DATE').'*') !!}
    {!! Form::date('dt_date', session('work_date'),
                                              ['class'=>'form-control input-sm',
                                              'id' => 'dt_date']) !!}
  </div>
  <div class="col-md-4">
    {!! Form::label('whs_id', trans('userinterface.labels.MVT_WHS_DEST').'*') !!}
    {!! Form::select('whs_id', $warehouses, session('whs')->id_whs, ['class'=>'form-control select-one',
                        'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required']) !!}
  </div>
  <div class="col-md-1" id="div_continue">
    {!! Form::label('') !!}
    {!! Form::button(trans('actions.CONTINUE'), ['class' => 'btn btn-primary', 'onclick' => 'headerContinue()']) !!}
  </div>
</div>
<hr>
