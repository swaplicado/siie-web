<!-- Modal -->
<div id="consumModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ 'Consumos' }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
              <table id="consumptions_table" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
                  <thead>
                      <tr class="titlerow">
                          <th>{{ trans('wms.labels.CODE')  }}</th>
                          <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                          <th>{{ trans('wms.labels.LOT') }}</th>
                          <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                          <th>{{ trans('wms.labels.PALLET') }}</th>
                          <th>{{ trans('userinterface.labels.LOCATION') }}</th>
                          <th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
                          <th>{{ trans('userinterface.labels.BRANCH') }}</th>
                          <th>{{ trans('wms.labels.DELIVERED') }}</th>
                          <th>{{ trans('wms.labels.RETURNED') }}</th>
                          <th>{{ trans('wms.labels.CONSUMED') }}</th>
                          <th>{{ trans('mms.labels.TO_CONSUM') }}</th>
                          <th>{{ trans('wms.labels.UN') }}</th>
                      </tr>
                  </thead>
                  <tbody>

                  </tbody>
              </table>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="consume()" class="btn btn-warning" data-dismiss="modal">{{ trans('actions.CONSUME') }}</button>
      </div>
    </div>

  </div>
</div>
