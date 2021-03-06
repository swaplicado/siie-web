<!-- Modal -->
<div class="modal fade" id="stock_modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.labels.STOCKS') }}</h4>
      </div>
      <br />
      <div id="divTable" class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table id="stock_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>{{ trans('wms.labels.LOCATION') }}</th>
                        <th>{{ trans('wms.labels.PALLET') }}</th>
                        <th>{{ trans('wms.labels.LOT') }}</th>
                        <th>{{ trans('wms.labels.AVAILABLE') }}</th>
                        <th>{{ trans('wms.labels.UNIT') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button id="closeModal" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>

  </div>
</div>
