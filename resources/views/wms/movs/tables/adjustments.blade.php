<table id="example" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
  <thead>
      <tr class="titlerow">
          <th>-</th>
          <th>{{ trans('wms.labels.CODE') }}</th>
          <th>{{ trans('wms.labels.MAT_PROD') }}</th>
          <th>{{ trans('wms.labels.UN') }}</th>
          <th>{{ trans('wms.labels.LOCATION') }}</th>
          <th>{{ trans('wms.labels.PALLET') }}</th>
          <th>{{ '$ '.trans('wms.labels.PRICE') }}</th>
          <th>{{ trans('wms.labels.QTY') }}</th>
          <th>{{ trans('wms.labels.LOT') }}</th>
          <th>{{ trans('wms.labels.STOCK') }}</th>
      </tr>
  </thead>
  <tbody id="lbody">
    <?php
      $i = 0;
    ?>
    @foreach ($oMovement->rows as $oRow)
      @if (! $oRow->is_deleted)
        <tr>
            <td>{{ $i }}</td>
            <td>{{ $oRow->item->code }}</td>
            <td>{{ $oRow->item->name }}</td>
            <td>{{ $oRow->unit->code }}</td>
            <td>{{ $oRow->location->code }}</td>
            <td>{{ $oRow->pallet_id == '1' ? 'SIN TARIMA' : $oRow->pallet_id}}</td>
            <td>{{ session('utils')->formatNumber($oRow->amount_unit, \Config::get('scsiie.FRMT.AMT')) }}</td>
            <td>{{ session('utils')->formatNumber($oRow->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
            <td>@if ($oRow->item->is_lot)
                    <button type='button' onClick='viewLots({{ $i }})'
                          class='butstk btn btn-primary btn-md'
                          title='Ver lotes'>
                      <i class='glyphicon glyphicon-info-sign'></i>
                    </button>
                @else
                  -
                @endif
            </td>
            <td>
                  <button type='button' onClick='viewStock({{ $i }})'
                        class='butstk btn btn-success btn-md'
                        data-toggle='modal' data-target='#stock_modal'
                        title='Ver existencias'>
                        <i class='glyphicon glyphicon-info-sign'></i>
                  </button>
            </td>
            <?php
              $i++;
            ?>
        </tr>
      @endif
    @endforeach
  </tbody>
</table>
