<table id="doc_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
    <thead>
        <tr class="titlerow">
            <th>-</th>
            <th>{{ trans('wms.labels.CODE') }}</th>
            <th>{{ trans('wms.labels.MAT_PROD') }}</th>
            <th>Clase</th>
            <th>{{ trans('wms.labels.PRICE') }}</th>
            <th>{{ trans('wms.labels.QTY') }}</th>
            <th>Pendiente</th>
            <th>Surtir</th>
            <th>{{ trans('wms.labels.UN') }}</th>
            <th>{{ trans('wms.labels.LOCATION') }}</th>
            <th>{{ trans('wms.labels.PALLET') }}</th>
            <th>{{ trans('wms.labels.LOT') }}</th>
            {{-- <th>{{ trans('wms.labels.STOCK') }}</th> --}}
        </tr>
    </thead>
    <tbody>
      <?php
        $i = 0;
      ?>
      @foreach ($lDocData as $row)
        <tr>
            <td>{{ $i }}</td>
            <td class="small">{{ $row->concept_key }}</td>
            <td class="small">{{ $row->concept }}</td>
            <td class="small">{{ $row->class_name }}</td>
            <td class="small" align="right">{{ session('utils')->formatNumber($row->price_unit_cur, \Config::get('scsiie.FRMT.AMT')) }}</td>
            <td class="small" align="right">{{ session('utils')->formatNumber($row->qty_row, \Config::get('scsiie.FRMT.QTY')) }}</td>
            <td class="small" align="right">{{ session('utils')->formatNumber($row->pending, \Config::get('scsiie.FRMT.QTY')) }}</td>
            <td class="small" align="right">{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}</td>
            {{-- <td class="small" align="right">
                    <input disabled type="number" class="form-control input-sm" style="text-align: right;" name="quantity" min="0"
                    value="{{ session('utils')->formatNumber(0, \Config::get('scsiie.FRMT.QTY')) }}">

            </td> --}}
            <td class="small">{{ $row->unit }}</td>
            <td class="small">{{ '---' }}</td>
            <td class="small">{{ '---' }}</td>
            <td>@if ($row->is_lot)
                    <button disabled type='button' onClick='viewLots({{ $i }})'
                          class='butstk btn btn-primary btn-md'
                          title='Ver lotes'>
                      <i class='glyphicon glyphicon-info-sign'></i>
                    </button>
                @else
                  -
                @endif
            </td>
            {{-- <td>
                  <button type='button' onClick='setRowData({{ $i }})'
                        class='butstk btn btn-success btn-md'
                        title='Ver existencias'>
                        <i class='glyphicon glyphicon-info-sign'></i>
                  </button>
            </td> --}}
        </tr>
        <?php
          $i++;
        ?>
      @endforeach
    </tbody>
</table>
