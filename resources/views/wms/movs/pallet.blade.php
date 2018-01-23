<div class="row">
  <div class="col-md-12">
    <table id="palletTable" class="table table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th>{{ trans('wms.labels.CODE') }}</th>
                <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                <th>{{ trans('wms.labels.UNIT') }}</th>
                @if (session('location_enabled'))
                  <th>{{ trans('wms.labels.LOCATION') }}</th>
                @endif
                <th>{{ trans('wms.labels.PALLET') }}</th>
                <th>{{ trans('wms.labels.QTY') }}</th>
                <th>{{ trans('wms.labels.LOT') }}</th>
            </tr>
        </thead>
        <tbody id="palletBody">
            <tr>
              <th align="right">@{{ sPallet.code }}</th>
              <th align="right">@{{ sPallet.item }}</th>
              <th align="right">@{{ sPallet.unit }}</th>
              @if (session('location_enabled'))
                <th align="right">@{{ sPallet.location }}</th>
              @endif
              <th align="right">@{{ sPallet.pallet }}</th>
              <th align="right">@{{ sPallet.quantity }}</th>
              <th align="right">
                <button id='palletStk' type='button' class='palletLots btn btn-info btn-md' data-toggle='modal' data-target='#myModal' title='Lotes' disabled>
                    <i class='glyphicon glyphicon-list-alt'></i>
                </button>
              </th>
            </tr>
        </tbody>
    </table>
  </div>
</div>
