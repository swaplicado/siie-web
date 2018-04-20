<div class="row" id="div_table">
  <div class="col-md-12">
    {!! Form::button(trans('actions.SUPPLY_ROW'), ['class' => 'btn btn-success', 'style' => 'display:none']) !!}
    <table id="rows_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th>id_mov</th>
                <th>id_mov_row</th>
                <th>{{ trans('userinterface.labels.BRANCH') }}</th>
                <th>{{ trans('userinterface.labels.WAREHOUSE') }}</th>
                <th data-priority="1">{{ trans('userinterface.labels.LOCATION') }}</th>
                <th>{{ trans('wms.labels.PALLET') }}</th>
                <th>{{ trans('userinterface.labels.PRICE') }}</th>
                <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                <th>{{ trans('wms.labels.INDIRECT_SUPPLY')  }}</th>
                <th>{{ trans('wms.labels.PENDING')  }}</th>
                <th>{{ trans('wms.labels.LINKED') }}</th>
                <th>{{ trans('wms.labels.LOTS') }}</th>
            </tr>
        </thead>
        <tbody>
           @foreach ($oMovementSrc->rows as $row)
             <tr>
                <td>{{ $row->mvt_id }}</td>
                <td>{{ $row->id_mvt_row }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
                <td>{{ $row->quantity }}</td>
             </tr>
           @endforeach
        </tbody>
      </table>
  </div>
</div>
