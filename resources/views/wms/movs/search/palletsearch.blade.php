<div class="col-md-10">
  {!! Form::label(trans('wms.labels.PALLET').'*') !!}
  <div class="row">
    <div class="col-md-6">
      {!! Form::text('search_pallet', null, ['class'=>'form-control input-sm',
        'id' => 'search_pallet',
        'title' => trans('wms.tooltips.ELEMENT_MULTIPLE'),
        'placeholder' => trans('wms.labels.PALLET').'...',
        'title' => trans('wms.tooltips.ONLY_BARCODES'),
        'onkeypress' => 'searchPalletReconf(event)']) !!}
    </div>
    <div class="col-md-6">
      {!! Form::select('pallet', $lPallets, null, ['class'=>'form-control select-one',
                                                              'placeholder' => trans('wms.placeholders.SELECT_PALLET'),
                                                               'id' => 'pallet']) !!}
    </div>
  </div>
</div>
