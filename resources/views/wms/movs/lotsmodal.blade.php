<!-- Modal -->
<div id="lotss_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.LOTS') }}</h4>
      </div>
      <div class="modal-body">
        <div id="lot_edition">
          <div class="row">
            <div class="col-md-4 paddingsm">
              {!! Form::label(trans('actions.SEARCH').' '.trans('wms.labels.LOT').'...') !!}
              {!! Form::text('search_lot',
                null , ['class'=>'form-control input-sm',
                'id' => 'search_lot',
                'placeholder' => trans('wms.labels.LOT').'...',
                'onkeypress' => 'searchLot(event)',
                'required']) !!}
            </div>
            <div class="col-sm-1 paddingsm">
              {!! Form::label('.') !!}
              <a onclick="searchLotByButton()" title="{{ trans('actions.SEARCH') }}"
                  class="btn btn-primary">
                <span class="glyphicon glyphicon-search" aria-hidden = "true"/>
              </a>
            </div>
            @if ($oMovement->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_OUT'))
              <div class="col-md-1">
                {!! Form::label('.') !!}
                <button type="button" onclick="showLots()"
                        class="btn btn-info">
                        {{ trans('actions.SEARCH') }}
                 </button>
              </div>
            @endif
          </div>
          <div class="row">
            <div class="col-sm-4 paddingsm">
              {!! Form::label(trans('wms.labels.LOT')) !!}
              {!! Form::text('lot',
                null, ['class' => 'form-control input-sm',
                'id' => 'lot',
                'placeholder' => trans('wms.labels.LOT').'...']) !!}
            </div>
            <div class="col-sm-3 paddingsm">
              {!! Form::label(trans('wms.labels.EXPIRATION_DATE').'*') !!}
  						{!! Form::date('exp_date', null, ['class'=>'form-control input-sm',
  																															'id' => 'exp_date']) !!}
            </div>
            <div class="col-sm-4 paddingsm">
              {!! Form::label(trans('userinterface.labels.QUANTITY').'*') !!}
              {!! Form::number('quantity_lot', 1, ['class'=>'form-control input-sm', 'id' => 'quantity_lot',
                                                    'placeholder' => trans('userinterface.placeholders.QUANTITY'),
                                                    'style' => 'text-align: right;']) !!}
            </div>
          </div>
          <div class="row">
            <div class="col-md-1 col-md-offset-5">
            </div>
            @if ($bCanCreateLotMat || $bCanCreateLotProd)
              <div class="col-md-2 paddingsm" id="div_cancreate">
                {!! Form::checkbox('is_lot_new', 'value', false, [ 'id' => 'is_lot_new']); !!}
                {!! Form::label(trans('actions.CREATE')) !!}
              </div>
            @endif
            <div class="col-md-2 paddingsm">
              <button onclick="addLotRow()" type="button" class="btn btn-info">
                      {{ trans('actions.ADD') }}
              </button>
            </div>
            <div class="col-md-1 paddingsm">
              <a onclick="cleanEntry()" title="{{ trans('actions.CLEAN') }}" class="btn btn-default">
                <span class="glyphicon glyphicon-erase" aria-hidden = "true"/>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-2 paddingsm" id="delete_lot">
            <button onclick="deleteLot()" type="button" class="btn btn-danger">
                    {{ trans('actions.QUIT') }}
            </button>
          </div>
          <div class="col-sm-3 paddingsm">
            {!! Form::label(trans('userinterface.labels.QUANTITY')) !!}
            {!! Form::label('lots_quantity', 1, ['class'=>'form-control input-sm backgreen',
                                                  'id' => 'lots_quantity',
                                                  'style' => 'text-align: right;']) !!}
          </div>
          <div class="col-sm-3 paddingsm">
            {!! Form::label(trans('wms.labels.ACCUM_QUANTITY')) !!}
            {!! Form::label('accum_quantity', 1, ['class'=>'form-control input-sm  backblue',
                                                  'id' => 'accum_quantity',
                                                  'style' => 'text-align: right;']) !!}
          </div>
          <div class="col-md-12">
            <table id="lots_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>iIndex</th>
                        <th>idLot</th>
                        <th>{{ trans('wms.labels.LOT') }}</th>
                        <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                        <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                        <th>{{ trans('actions.CREATE') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
        </div>
      </div>
      <div id="lot_accep_div" class="modal-footer">
        <button type="button" id="accLots" class="btn btn-success" data-dismiss="modal">{{ trans('actions.ADD') }}</button>
      </div>
    </div>

  </div>
</div>
