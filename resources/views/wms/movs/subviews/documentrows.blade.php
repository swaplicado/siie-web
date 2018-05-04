<div class="row">
  @if($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR') ||
        $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_SAL') ||
        $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_SAL'))
    <div class="col-md-1" id="div_setdata" style="display: none;">
      <button id="sData" type='button' onClick='setRowData()'
            class='btn btn-success'
            title='{{ trans('actions.SUPPLY') }}'>{{ trans('actions.SUPPLY') }}
      </button>
    </div>
  @endif
  @if ($oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_PUR') ||
      $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_IN_SAL') ||
        $oMovement->mvt_whs_type_id == \Config::get('scwms.MVT_TP_OUT_SAL'))
    <div class="col-md-12">
        @include('wms.movs.tables.others')
    </div>
  @endif
</div>
