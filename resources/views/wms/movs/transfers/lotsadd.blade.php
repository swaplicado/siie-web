<!-- Modal -->
<div id="lots_transfers_add" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.LOTS') }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table id="lots_add_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>iIndex</th>
                        <th>idLot</th>
                        <th>{{ trans('wms.labels.LOT') }}</th>
                        <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                        <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                        <th>{{ trans('wms.labels.RECEIVED') }}</th>
                        <th>{{ trans('wms.labels.PENDING') }}</th>
                        <th>{{ trans('wms.labels.IN_MOVEMENT') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="accepLots" class="btn btn-success" data-dismiss="modal">{{ trans('actions.ACCEPT') }}</button>
      </div>
    </div>

  </div>
</div>
