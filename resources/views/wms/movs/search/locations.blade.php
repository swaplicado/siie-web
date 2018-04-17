<div class="row">
  @if (session('location_enabled'))
    <div class="col-md-2">
      {!! Form::label('', trans('actions.SEARCH_LOCATION').'...') !!}
        {!! Form::text('location', null, ['class'=>'form-control input-sm',
          'id' => 'location',
          'title' => trans('wms.tooltips.ONLY_BARCODES'),
          'placeholder' => trans('wms.placeholders.BAR_CODE_LOCATION'),
          'onkeypress' => 'searchLoc(event)']) !!}
    </div>
    <div class="col-md-1">
      {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
      <a title="{{ trans('actions.SEARCH') }}"
          data-toggle="modal"
          data-target="#location_search"
          class="btn btn-warning">
        <span class="glyphicon glyphicon-search" aria-hidden = "true"/>
      </a>
    </div>
    <div class="col-md-3">
      {!! Form::label('Ubicación') !!}
      {!! Form::label('label_loc', '--',
                          ['class' => 'form-control input-sm',
                          'id' => 'label_loc']) !!}
    </div>
    <div id="locss" style="display: none;">
      <div class="col-md-2">
        {!! Form::label('', trans('actions.SEARCH_LOCATION').'...') !!}
          {!! Form::text('location_des', null, ['class'=>'form-control input-sm',
            'id' => 'location_des',
            'title' => trans('wms.tooltips.ONLY_BARCODES'),
            'placeholder' => trans('wms.placeholders.BAR_CODE_LOCATION'),
            'onkeypress' => 'searchLocDes(event)']) !!}
      </div>
      <div class="col-md-1">
        {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
        <a title="{{ trans('actions.SEARCH') }}"
            data-toggle="modal"
            data-target="#location_search_des"
            class="btn btn-secondary">
          <span class="glyphicon glyphicon-search" aria-hidden = "true"/>
        </a>
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
