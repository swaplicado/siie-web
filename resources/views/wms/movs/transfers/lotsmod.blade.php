<!-- Modal -->
<div id="lots_transfers" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.LOTS') }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table id="lots_header_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>iIndex</th>
                        <th>idLot</th>
                        <th>{{ trans('wms.labels.LOT') }}</th>
                        <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                        <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                        <th>{{ trans('wms.labels.RECEIVED') }}</th>
                        <th>{{ trans('wms.labels.PENDING') }}</th>
                    </tr>
                </thead>
                <tbody>
                  <tr>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                      <td>-</td>
                  </tr>
                </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">{{ trans('actions.ACCEPT') }}</button>
      </div>
    </div>

  </div>
</div>
