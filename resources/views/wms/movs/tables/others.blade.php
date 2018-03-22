<table id="doc_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
    <thead>
        <tr class="titlerow">
            <th>-</th>
            <th>{{ trans('wms.labels.CODE') }}</th>
            <th>{{ trans('wms.labels.MAT_PROD') }}</th>
            <th>Clase</th>
            <th>{{ trans('wms.labels.PRICE') }}</th>
            <th>{{ trans('wms.labels.QTY') }}</th>
            <th>Surtido</th>
            <th>Pendiente</th>
            <th>{{ trans('wms.labels.UN') }}</th>
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
            <td class="small" align="right">{{ session('utils')->formatNumber(($row->qty_row - $row->pending), \Config::get('scsiie.FRMT.QTY')) }}</td>
            <td class="small" align="right">{{ session('utils')->formatNumber($row->pending, \Config::get('scsiie.FRMT.QTY')) }}</td>
            <td class="small">{{ $row->unit }}</td>
        </tr>
        <?php
          $i++;
        ?>
      @endforeach
    </tbody>
</table>
