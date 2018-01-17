<!-- Modal -->
<div id="classQlty" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Clasificación de material/producto</h4>
      </div>
      <div class="modal-body" id='app'>
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
            {!! Form::select('status', $lStatus, null,
                            ['class'=>'form-control', 'id' => 'status',
														'placeholder' => trans('qms.placeholders.SELECT_STATUS'),
                            'required', ]) !!}
          </div>
          <div class="col-md-4">
            {!! Form::number('quantity', 0, ['class'=>'form-control', 'id' => 'quantity',
                                              'style' => 'text-align: right;',
                                              'placeholder' => trans('userinterface.placeholders.QUANTITY')]) !!}
          </div>
          <div class="col-md-1">
    				{!! Form::checkbox('to_all', 1, false, ['class' => 'form-control',
                                                  'id' => 'to_all',
                                                  'align' => 'left',
                                                  'onClick' => 'setAll(this)']) !!}
    			</div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeClass" type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
      </div>
    </div>

  </div>
</div>
