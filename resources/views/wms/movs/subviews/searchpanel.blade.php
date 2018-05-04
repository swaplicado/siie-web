<div id="div_rows" style="display: none;">
  <div class="row">
    <div class="col-md-12">
          @include('wms.movs.search.locations')
          <div class="row">
            @if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_ADJ') ||
                  $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_ADJ') ||
                  $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_TRA') ||
                  ($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION')) ||
                  App\SUtils\SGuiUtils::showPallet($oMovement->mvt_whs_type_id))
                <div class="col-md-3">
                  {!! Form::label('', trans('actions.SEARCH_ELEMENT').'...') !!}
                  {!! Form::text('item', null, ['class'=>'form-control input-sm',
                                                  'id' => 'item',
                                                  'title' => trans('wms.tooltips.ELEMENT_MULTIPLE'),
                                                  'placeholder' => trans('wms.placeholders.SEARCH_ELEMENT'),
                                                  'onkeypress' => 'searchElem(event)']) !!}
                </div>
                <div class="col-md-1" id="div_search_button">
                    {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
                    <a title="{{ trans('actions.SEARCH') }}"
                        data-toggle="modal"
                        data-target="#mat_prod_search"
                        class="btn btn-info">
                      <span class="glyphicon glyphicon-search" aria-hidden = "true"/>
                    </a>
                </div>
            @endif
            <div class="col-md-6">
              {!! Form::label('seleccionado') !!}
              {!! Form::label('label_sel', '--',
                                  ['class' => 'form-control input-sm',
                                  'id' => 'label_sel']) !!}
            </div>
          </div>
          <div class="row">
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
            <div class="col-md-2">
                {!! Form::label('price', trans('userinterface.labels.PRICE').'*') !!}
                {!! Form::number('price', 1, ['class'=>'form-control input-sm', 'id' => 'price',
                                                      'placeholder' => trans('userinterface.placeholders.PRICE'),
                                                      'style' => 'text-align: right;',
                                                      'max' => '999999999999',
                                                      'step' => '0.01',
                                                      $oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ||
                                                      $oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_OUT') ? 'disabled' : '']) !!}
            </div>
            <div class="col-md-1">
              {!! Form::label('Moneda') !!}
              {!! Form::label('label_cur', session('currency')->code,
                                  ['class' => 'form-control input-sm',
                                  'id' => 'label_cur']) !!}
            </div>
            <div class="col-md-1" id="div_pallets">
              {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
              <button type="button" id="btn_pallet" class="btn btn-secondary" onclick="showPalletModal()">
                      {{ trans('wms.labels.PALLET') }}
              </button>
            </div>
            <div class="col-md-1" id="div_lots">
              {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
              <button type="button" id="btn_lots" class="btn btn-secondary" onclick="showLotsModal()">
                      {{ trans('wms.labels.LOTS') }}
              </button>
            </div>
            <div class="col-md-2">
              {!! Form::label('-', '-----', ['style' => 'color: white;']) !!}
                <div class="row">
                  <div class="col-md-8" id="div_add">
                    <button id="tButton" onclick="addElement()" type="button" class="btn btn-primary buttonlarge">{{ trans('actions.ADD') }}</button>
                  </div>
                  <div class="col-md-4">
                    <a onclick="cleanPanel()" title="{{ trans('actions.CLEAN') }}" class="btn btn-default">
                      <span class="glyphicon glyphicon-erase" aria-hidden = "true"/>
                    </a>
                  </div>
                </div>
            </div>
          </div>
    </div>
  </div>
  <hr />
</div>
