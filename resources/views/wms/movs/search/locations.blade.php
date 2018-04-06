<div class="row">
  @if (session('location_enabled'))
    <div class="col-md-2">
      {!! Form::label(trans('actions.SEARCH_LOCATION').'...') !!}
        {!! Form::text('location', null, ['class'=>'form-control input-sm',
          'id' => 'location',
          'placeholder' => trans('userinterface.placeholders.CODE'),
          'onkeypress' => 'searchLoc(event)']) !!}
    </div>
    <div class="col-md-1">
      {!! Form::label('.') !!}
      <button type="button"
      class="btn btn-warning"
      data-toggle="modal"
      data-target="#location_search">Ubica.</button>
    </div>
    <div class="col-md-3">
      {!! Form::label('Ubicación') !!}
      {!! Form::label('label_loc', '--',
                          ['class' => 'form-control input-sm',
                          'id' => 'label_loc']) !!}
    </div>
    <div id="locss" style="display: none;">
      <div class="col-md-2">
        {!! Form::label(trans('actions.SEARCH_LOCATION').'...') !!}
          {!! Form::text('location_des', null, ['class'=>'form-control input-sm',
            'id' => 'location_des',
            'placeholder' => trans('userinterface.placeholders.CODE'),
            'onkeypress' => 'searchLocDes(event)']) !!}
      </div>
      <div class="col-md-1">
        {!! Form::label('.') !!}
        <button type="button"
        class="btn btn-secondary"
        data-toggle="modal"
        data-target="#location_search_des">Ubica.</button>
      </div>
    </div>
    <div id="loc_des_lab" style="display: none;" class="col-md-3">
      {!! Form::label('Ubicación destino') !!}
      {!! Form::label('label_loc_des', '--',
                          ['class' => 'form-control input-sm',
                          'id' => 'label_loc_des']) !!}
      </div>
  @endif
</div>
