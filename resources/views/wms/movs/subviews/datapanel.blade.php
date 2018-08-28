<div class="row">
  <div class="col-md-6">
    <div class="row">
      <div class="col-md-2" id="div_delete" style="display: none;">
        <button id="delButton" onclick="deleteElement()" type="button" class="btn btn-danger">{{ trans('actions.QUIT') }}</button>
      </div>
      @if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT'))
        <div class="col-md-5">
          <button id="stkButton" type='button' onClick='stockComplete()'
                class='butstk btn btn-success'
                data-toggle='modal' data-target='#stock_com_modal'
                title='Ver existencias'>{{ trans('wms.WHS_IN_STK') }}
          </button>
        </div>
      @endif
      @if (App\SUtils\SGuiUtils::isProductionMovement($oMovement->mvt_whs_type_id))
        <div class="col-md-2" style="display: none;" id="prod_ord_div">
          <button id="poBtn" type='button'
                  {{-- onClick='stockComplete()' --}}
                class='btn btn-warning'
                data-toggle='modal' data-target='#prodOrderModal'
                title='Ver orden de producción'>{{ trans('wms.labels.SEE_PO') }}
          </button>
        </div>
      @endif
    </div>
  </div>
  <div id="info_div" style="display: none;" class="col-md-6">
    <div class="row">
      <div class="col-md-offset-6 col-md-3">
        {!! '$ '.Form::label('Monto') !!}
        {!! Form::label('label_amt', '--',
                            ['class' => 'form-control input-sm',
                              'style' => 'text-align: right; color: blue;',
                              'id' => 'label_amt']) !!}
      </div>
      <div class="col-md-3">
        {!! Form::label('Cantidad') !!}
        {!! Form::label('label_qty', '--',
                            ['class' => 'form-control input-sm',
                              'style' => 'text-align: right; color: blue;',
                              'id' => 'label_qty']) !!}
      </div>
    </div>
  </div>
</div>
