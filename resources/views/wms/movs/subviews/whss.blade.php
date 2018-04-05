<div class="col-md-8">
  <div class="form-group">
    @if (App\SUtils\SGuiUtils::isWhsShowed($oMovement->mvt_whs_class_id, $oMovement->mvt_whs_type_id, 'whs_src'))
        {!! Form::label('whs_src', trans('userinterface.labels.MVT_WHS_SRC').'*') !!}
        {!! Form::select('whs_src', $warehouses, $whs_src, ['class'=>'form-control border_red select-one',
                            'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required',
                            isset($oMovement->id_mvt) ? 'disabled' : '']) !!}
    @endif
    @if (App\SUtils\SGuiUtils::isWhsShowed($oMovement->mvt_whs_class_id, $oMovement->mvt_whs_type_id, 'whs_des') &&
            ! $bIsExternalTransfer)
        {!! Form::label('whs_des', ($oMovement->mvt_whs_type_id == \Config::get('scwms.PALLET_RECONFIG_IN') ?
          trans('wms.labels.WAREHOUSE') :
              trans('userinterface.labels.MVT_WHS_DEST')).'*') !!}
        {!! Form::select('whs_des', $warehouses, $whs_des, ['class'=>'form-control select-one',
                              'placeholder' => trans('userinterface.placeholders.SELECT_WHS'), 'required',
                              isset($oMovement->id_mvt) ? 'disabled' : '']) !!}
    @endif
    @if ($bIsExternalTransfer)
      {!! Form::label('branch_des', trans('wms.labels.BRANCH_DESTINY')) !!}
      {!! Form::select('branch_des', $branches, null, ['class'=>'form-control select-one',
                            'placeholder' => trans('userinterface.placeholders.SELECT_BRANCH'), 'required']) !!}
    @endif
  </div>
</div>
