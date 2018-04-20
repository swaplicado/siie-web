<div id="div_search" style="display: none;">
  <div class="row">
    <div class="col-md-3">
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
  </div>
  <div class="row">
    <div class="col-md-5">
      {!! Form::label('seleccionado') !!}
      {!! Form::label('label_sel', '--',
                          ['class' => 'form-control input-sm',
                          'id' => 'label_sel']) !!}
    </div>
    <div class="col-md-2">
        {!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
        {!! Form::number('quantity', 0, ['class'=>'form-control input-sm', 'id' => 'quantity',
                                              'placeholder' => trans('userinterface.placeholders.QUANTITY'),
                                              'style' => 'text-align: right;',
                                              'max' => '999999999999',
                                              'step' => '0.01']) !!}
    </div>
    <div class="col-md-1">
      {!! Form::label('Unidad') !!}
      {!! Form::label('label_unit', '--',
                          ['class' => 'form-control input-sm',
                          'id' => 'label_unit']) !!}
    </div>
  </div>
  <div class="row">
    <div class="col-md-2">
        {!! Form::label('price', trans('userinterface.labels.PRICE').'*') !!}
        {!! Form::number('price', 1, ['class'=>'form-control input-sm', 'id' => 'price',
                                              'placeholder' => trans('userinterface.placeholders.PRICE'),
                                              'style' => 'text-align: right;',
                                              'max' => '999999999999',
                                              'step' => '0.01']) !!}
    </div>
    <div class="col-md-1">
      {!! Form::label('Moneda') !!}
      {!! Form::label('label_cur', session('currency')->code,
                          ['class' => 'form-control input-sm',
                          'id' => 'label_cur']) !!}
    </div>
    <div class="col-md-4" id="div_pallets">
      {!! Form::label('', trans('wms.labels.PALLET')) !!}
      {!! Form::label('label_pallet', '--',
                          ['class' => 'form-control input-sm',
                          'id' => 'label_pallet']) !!}
    </div>
    <div class="col-md-1" id="div_lots">
      {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
      {!! Form::button(trans('wms.labels.LOTS'), ['class' => 'btn btn-info',
                                                    'id' => 'btn_lots']) !!}
    </div>
    <div class="col-md-1">
      {!! Form::label('') !!}
      {!! Form::button(trans('actions.ADD'), ['class' => 'btn btn-primary']) !!}
    </div>
  </div>
  <hr>
</div>
