<!-- Modal -->
<div id="pallet_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.PALLET') }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8">
            {!! Form::label(trans('actions.SEARCH').'...') !!}
            {!! Form::text('search_pallet', null, ['class'=>'form-control',
                                            'id' => 'search_pallet',
                                            'placeholder' => trans('wms.labels.PALLET').'...',
                                            'onkeypress' => 'searchPallet(event)', 'required']) !!}
          </div>
          <div class="col-md-1">
            {!! Form::label('.') !!}
            <a onclick="searchPall()" title="{{ trans('actions.SEARCH') }}"
                class="btn btn-primary">
              <span class="glyphicon glyphicon-search" aria-hidden = "true"/>
            </a>
          </div>
          @if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT') ||
                  $oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN'))
            <div class="col-md-1">
              {!! Form::label('.') !!}
              <button type="button" onclick="showPallets()"
                      class="btn btn-primary">
                      {{ trans('actions.SEARCH') }}
               </button>
            </div>
          @endif
        </div>
        <div class="row">
          <div class="col-md-10">
            {!! Form::label(trans('wms.labels.MAT_PROD')) !!}
            {!! Form::label('item_pallet', '--',
                                ['class' => 'form-control',
                                'id' => 'item_pallet']) !!}
          </div>
          <div class="col-md-2">
            {!! Form::label(trans('wms.labels.UNIT')) !!}
            {!! Form::label('unit_pallet', '--',
                                ['class' => 'form-control',
                                'id' => 'unit_pallet']) !!}
          </div>
        </div>
        <div class="row">
          <div class="col-md-5">
            {!! Form::label(trans('wms.labels.PALLET')) !!}
            {!! Form::label('name_pallet', '--',
                                ['class' => 'form-control',
                                'id' => 'name_pallet']) !!}
          </div>
          @if ($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_ADJ')
                || $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR')
                  || $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_IN_DLVRY_FP'))
            <div class="col-md-5">
              {!! Form::label(trans('wms.labels.PALLETS').'') !!}
              {!! Form::text('string_pallets', '',
                                  ['class' => 'form-control',
                                  'id' => 'string_pallets',
                                  'placeholder' => 'p. ej. 1-5, 8, 11-13']) !!}
            </div>
          @endif
          <div class="col-md-1">
            {!! Form::label(trans('actions.CLEAN')) !!}
            <a onclick="cleanPallet()" title="{{ trans('actions.CLEAN') }}"
                class="btn btn-info">
              <span class="glyphicon glyphicon-erase" aria-hidden = "true"/>
            </a>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="accPallet" class="btn btn-default" data-dismiss="modal">{{ trans('actions.ACCEPT') }}</button>
      </div>
    </div>

  </div>
</div>
