<div class="row">
    <div class="col-md-2">
      {!! Form::label(trans('userinterface.labels.FOLIO').'*') !!}
      {!! Form::text('folio', $oDocument->num, ['class'=>'form-control input-sm', 'placeholder' => trans('userinterface.placeholders.FOLIO'), 'readonly']) !!}
    </div>
    <div class="col-md-2">
      {!! Form::label('dt_date', trans('userinterface.labels.DATE').'*') !!}
      {!! Form::date('dt_date', $oDocument->dt_doc, ['class'=>'form-control input-sm', 'id' => 'dt_date', 'readonly']) !!}
    </div>
    <div class="col-md-2">
      {!! Form::label('dt_doc', trans('userinterface.labels.DATE_DOC').'*') !!}
      {!! Form::date('dt_doc', $oDocument->dt_doc, ['class'=>'form-control input-sm', 'id' => 'dt_doc', 'readonly']) !!}
    </div>
    <div class="col-md-6">
      {!! Form::label(trans('userinterface.labels.BP').'*') !!}
      {!! Form::text('partner_name', $oDocument->partner->name, ['class'=>'form-control input-sm', 'readonly']) !!}
    </div>
</div>
