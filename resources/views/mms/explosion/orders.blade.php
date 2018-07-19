<!-- Modal -->
<div id="orders_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('mms.PRODUCTION_ORDERS') }}</h4>
      </div>
      <div class="modal-body">
        <table id="orders_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
            <thead>
                <tr class="titlerow">
                    <th data-priority="1" style="text-align: center;">{{ trans('userinterface.labels.FOLIO') }}</th>
                    <th>{{ trans('userinterface.labels.DATE') }}</th>
                    <th>{{ trans('mms.labels.FORMULA') }}</th>
                    <th style="text-align: center;">{{ trans('mms.labels.VERSION') }}</th>
                    <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                    <th>{{ trans('wms.labels.UN') }}</th>
                    <th style="text-align: center;">{{ trans('mms.labels.CHARGES') }}</th>
                    <th>{{ trans('userinterface.labels.TYPE') }}</th>
                    <th>{{ trans('userinterface.labels.STATUS') }}</th>
                    <th>{{ trans('mms.labels.FLOOR') }}</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($lOrders as $oOrder)
                <tr>
                  <td>{{ session('utils')->formatFolio($oOrder->folio) }}</td>
                  <td>{{ $oOrder->date }}</td>
                  <td>{{ $oOrder->formula->identifier }}</td>
                  <td>{{ $oOrder->formula->version }}</td>
                  <td>{{ $oOrder->item->name }}</td>
                  <td>{{ $oOrder->unit->code }}</td>
                  <td>{{ $oOrder->charges }}</td>
                  <td>{{ $oOrder->type->name }}</td>
                  <td>{{ $oOrder->status->name }}</td>
                  <td>{{ $oOrder->floor->name }}</td>
                </tr>
              @endforeach
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>
  </div>
</div>
