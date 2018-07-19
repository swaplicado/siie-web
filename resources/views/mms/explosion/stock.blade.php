<!-- Modal -->
<div id="stock_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('wms.WHS_STK') }}</h4>
      </div>
      <div class="modal-body">
        <table id="stock_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
            <thead>
                <tr class="titlerow">
                    <th data-priority="1" style="text-align: center;">{{ trans('siie.labels.KEY') }}</th>
                    <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                    <th>{{ trans('wms.labels.AVAILABLE') }}</th>
                    <th>{{ trans('wms.labels.UN') }}</th>
                    <th>{{ trans('wms.labels.LOT') }}</th>
                    <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                    <th>{{ trans('wms.labels.PALLET') }}</th>
                    <th>{{ trans('wms.labels.LOCATION') }}</th>
                    <th>{{ trans('wms.labels.WHS') }}</th>
                    <th>{{ trans('wms.labels.STOCK') }}</th>
                    <th>{{ trans('wms.labels.SEGREGATED') }}</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($lStock as $oStock)
                <tr>
                  <td>{{ $oStock->item_code }}</td>
                  <td>{{ $oStock->item }}</td>
                  <td align="right">{{ session('utils')->
                      formatNumber(($oStock->stock - $oStock->segregated),
                      \Config::get('scsiie.FRMT.QTY')) }}</td>
                  <td>{{ $oStock->unit_code }}</td>
                  <td>{{ $oStock->lot }}</td>
                  <td>{{ $oStock->dt_expiry }}</td>
                  <td>{{ $oStock->pallet_id }}</td>
                  <td>{{ $oStock->location_code }}</td>
                  <td>{{ $oStock->whs_code }}</td>
                  <td align="right">{{ session('utils')->
                      formatNumber($oStock->stock,
                      \Config::get('scsiie.FRMT.QTY')) }}
                  </td>
                  <td align="right">{{ session('utils')->
                      formatNumber($oStock->segregated,
                      \Config::get('scsiie.FRMT.QTY')) }}
                  </td>
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
