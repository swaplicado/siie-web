<div class="row" id="div_pallet" style="display: none;">
  <label style="color: #0200e6">{{ App\SUtils\SGuiUtils::getLabelOfPallet($oMovement->mvt_whs_type_id) }}</label>
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-6">
        {!! Form::label(trans('wms.labels.PALLET')) !!}
        {!! Form::label('label_pallet', '--',
                            ['class' => 'form-control input-sm',
                            'id' => 'label_pallet']) !!}
      </div>
      <div class="col-md-6">
        {!! Form::label(trans('wms.labels.LOCATION')) !!}
        {!! Form::label('label_pallet_location', '--',
                            ['class' => 'form-control input-sm',
                            'id' => 'label_pallet_location']) !!}
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        {!! Form::label(trans('userinterface.labels.CODE')) !!}
        {!! Form::label('label_pallet_item_code', '--',
                            ['class' => 'form-control input-sm',
                            'id' => 'label_pallet_item_code']) !!}
      </div>
      <div class="col-md-8">
        {!! Form::label(trans('wms.labels.MAT_PROD')) !!}
        {!! Form::label('label_pallet_item', '--',
                            ['class' => 'form-control input-sm',
                            'id' => 'label_pallet_item']) !!}
      </div>
      <div class="col-md-2">
        {!! Form::label(trans('userinterface.labels.UNIT')) !!}
        {!! Form::label('label_pallet_unit', '--',
                            ['class' => 'form-control input-sm',
                            'id' => 'label_pallet_unit']) !!}
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <table id="pallet_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
            <thead>
                <tr class="titlerow">
                    <th>pallet_row_index</th>
                    <th>{{ trans('wms.labels.LOT') }}</th>
                    <th>{{ trans('wms.labels.EXPIRATION_DATE') }}</th>
                    <th>{{ trans('wms.labels.QTY') }}</th>
                    <th>{{ trans('wms.labels.QTY_TO_MOVE') }}</th>
                    <th>{{ trans('wms.labels.QTY_REMAINING') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <hr>
      </div>
    </div>
  </div>
</div>
<label style="color: #0200e6">{{ trans('wms.labels.ELEMENTS_TO_MOVE') }}</label>
