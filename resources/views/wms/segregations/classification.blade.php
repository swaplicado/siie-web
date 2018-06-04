<!-- Modal -->
<div id="classQlty" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='appQl'>
        <div class="row">
          <div class="col-md-6">
            Clave
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item_code }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Item
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Unidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.unit }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Lote
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.lot }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Estatus actual de calidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.status }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Cantidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.quantity }}</b>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-6">
            Estatus Calidad nuevo
          </div>
          <div class="col-md-4">
            Unidades a clasificar
          </div>
          <div class="col-md-1">
    				Todo
    			</div>
        </div>
        <div class="row">
          <div class="col-md-6">

            {!! Form::select('statusQl', $lStatusSeg, null,
                            ['class'=>'form-control', 'id' => 'statusQl',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('quantityQl', 0, ['class'=>'form-control', 'id' => 'quantityQl',
                                              'style' => 'text-align: right;', 'min' => "0",
                                              'placeholder' => trans('userinterface.placeholders.QUANTITY')]) !!}
          </div>
          <div class="col-md-1">
    				{!! Form::checkbox('to_all_ql', 1, false, ['class' => 'form-control',
                                                  'id' => 'to_all_ql',
                                                  'align' => 'left',
                                                  'onClick' => 'setAllQl(this)']) !!}
    			</div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClassQl" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>

<div id="classRls" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='appRl'>
        <div class="row">
          <div class="col-md-6">
            Clave
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item_code }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Item
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Unidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.unit }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Lote
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.lot }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Estatus actual de calidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.status }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Cantidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.quantity }}</b>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-6">
            Estatus Calidad nuevo
          </div>
          <div class="col-md-4">
            Unidades a clasificar
          </div>
          <div class="col-md-1">
    				Todo
    			</div>
        </div>
        <div class="row">
          <div class="col-md-6">
            {!! Form::select('statusRl', $lStatusRec, null,
                            ['class'=>'form-control', 'id' => 'statusRl',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('quantityRl', 0, ['class'=>'form-control', 'id' => 'quantityRl',
                                              'style' => 'text-align: right;', 'min' => "0",
                                              'placeholder' => trans('userinterface.placeholders.QUANTITY')]) !!}
          </div>
          <div class="col-md-1">
    				{!! Form::checkbox('to_all_rl', 1, false, ['class' => 'form-control',
                                                  'id' => 'to_all_rl',
                                                  'align' => 'left',
                                                  'onClick' => 'setAllRl(this)']) !!}
    			</div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClassRl" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>

<div id="classRfs" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='appRf'>
        <div class="row">
          <div class="col-md-6">
            Clave
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item_code }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Item
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Unidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.unit }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Lote
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.lot }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Estatus actual de calidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.status }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Cantidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.quantity }}</b>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-6">
            Estatus Calidad nuevo
          </div>
          <div class="col-md-4">
            Unidades a clasificar
          </div>
          <div class="col-md-1">
    				Todo
    			</div>
        </div>
        <div class="row">
          <div class="col-md-6">
            {!! Form::select('statusRf', $lStatusLib, null,
                            ['class'=>'form-control statusRF', 'id' => 'statusRf',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('quantityRf', 0, ['class'=>'form-control', 'id' => 'quantityRf',
                                              'style' => 'text-align: right;','min' => "0",
                                              'placeholder' => trans('userinterface.placeholders.QUANTITY')]) !!}
          </div>
          <div class="col-md-1">
    				{!! Form::checkbox('to_all_rf', 1, false, ['class' => 'form-control',
                                                  'id' => 'to_all_rf',
                                                  'align' => 'left',
                                                  'onClick' => 'setAllRf(this)']) !!}
    			</div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Almacenes
          </div>
          <div class="col-md-4">
            Ubicaciones
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 warehouse">

          </div>
          <div class="col-md-6 location">

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClassRf" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="classQltyP" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='appQlP'>
        <div class="row">
          <div class="col-md-6">
            Clave
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item_code }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Item
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Unidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.unit }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Lote
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.lot }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Estatus actual de calidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.status }}</b>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-6">
            Estatus Calidad nuevo
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">

            {!! Form::select('statusQlP', $lStatusSeg, null,
                            ['class'=>'form-control', 'id' => 'statusQlP',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClassQlP" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>

<div id="classRlsP" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='appRlP'>
        <div class="row">
          <div class="col-md-6">
            Clave
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item_code }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Item
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Unidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.unit }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Lote
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.lot }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Estatus actual de calidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.status }}</b>
          </div>
        </div>
        <br/>
        <div class="row">
          <div class="col-md-6">
            Estatus Calidad nuevo
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            {!! Form::select('statusRlP', $lStatusRec, null,
                            ['class'=>'form-control', 'id' => 'statusRlP',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClassRlP" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>

<div id="classRfsP" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='appRfP'>
        <div class="row">
          <div class="col-md-6">
            Clave
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item_code }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Item
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.item }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Unidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.unit }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Lote
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.lot }}</b>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Estatus actual de calidad
          </div>
          <div class="col-md-6">
            <b>@{{ dataItem.status }}</b>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-6">
            Estatus Calidad nuevo
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            {!! Form::select('statusRfP', $lStatusLib, null,
                            ['class'=>'form-control statusRFP', 'id' => 'statusRfP',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            Almacenes
          </div>
          <div class="col-md-4">
            Ubicaciones
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 warehouseP">

          </div>
          <div class="col-md-6 locationP">

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClassRf" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>
