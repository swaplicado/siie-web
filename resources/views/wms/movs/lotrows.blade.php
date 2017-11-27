<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.LOTS_ASSIGNAMENT') }}</h4>
      </div>
      <br />
      <div class="col-md-4">
        <button id="addBtn" type="button" onclick="addRowAux()" class="btn btn-success">
                {{ trans('wms.buttons.ADD_LOT') }}
        </button>
      </div>
      {{-- <div class="col-md-4">
        <label for="disabledTextInput">{{ trans('wms.labels.QTY_FOR_COMPLETE') }}</label>
      </div> --}}
      {{-- <div class="col-md-4">
        <input type="text" id="qtyComplete" disabled="true" class="form-control" placeholder="Cantidad">
      </div> --}}
      <br />
      <br />
      <div id="divTable" class="modal-body">
        <table id="lotsTable" class="table table-bordered" cellspacing="0" width="100%">
						<thead>
								<tr class="titlerow">
										<th data-override="lot">Lote/Caducidad</th>
										<th data-override="qty">Cantidad</th>
										<th data-override="price">Precio</th>
										<th>Total</th>
										<th>-</th>
                    <th data-override="id_parent" style="display:none;"></th>
                    <th data-override="lot_value" style="display:none;"></th>
								</tr>
						</thead>
						<tfoot>

						</tfoot>
						<tbody id="lotsbody">

						</tbody>
				</table>
      </div>
      <div class="modal-footer">
        <button id="closeModal" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.SAVE') }}</button>
      </div>
    </div>

  </div>
</div>
