<div class="row" id="div_table">
  <div class="col-md-12">
    {!! Form::button(trans('actions.RECEIVE'),
      ['class' => 'btn btn-success',
      'style' => 'display:none',
      'onClick' => 'onPressReceive()',
      'id' => 'btn_receive']) !!}
    <table id="rows_table" class="table table-striped table-condensed table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th>id_mov</th>
                <th>id_mov_row</th>
                <th data-priority="1">{{ trans('wms.labels.CODE') }}</th>
                <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                <th>{{ trans('wms.labels.UNIT') }}</th>
                <th>{{ trans('wms.labels.PALLET') }}</th>
                <th>{{ trans('userinterface.labels.PRICE') }}</th>
                <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                <th>{{ 'Recibido' }}</th>
                <th>{{ trans('wms.labels.PENDING')  }}</th>
                <th>{{ trans('wms.labels.LOTS') }}</th>
            </tr>
        </thead>
        <tbody>
          <?php $i = 0; ?>
           @foreach ($oMovementSrc->rows as $row)
             <tr>
                <td>{{ $row->mvt_id }}</td>
                <td>{{ $row->id_mvt_row }}</td>
                <td>{{ $row->item->code }}</td>
                <td>{{ $row->item->name }}</td>
                <td>{{ $row->unit->code }}</td>
                <td>{{ $row->pallet_id == 1 ? $row->pallet->pallet : $row->pallet_id }}</td>
                <td class="small" align="right">{{ session('utils')->formatNumber($row->price_unit, \Config::get('scsiie.FRMT.AMT')) }}</td>
                <td class="small" align="right">{{ session('utils')->formatNumber($row->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
                <td class="small" align="right">{{ session('utils')->formatNumber($row->dReceived, \Config::get('scsiie.FRMT.QTY')) }}</td>
                <td class="small" align="right">{{ session('utils')->formatNumber($row->quantity - $row->dReceived, \Config::get('scsiie.FRMT.QTY')) }}</td>
                @if($row->item->is_lot)
                  <td>{!! Form::button(
                            '<span class=" glyphicon glyphicon-info-sign"></span>',
                          [
                            'type' => 'button',
                            'onClick' => 'setLots('.$i.', '.$row->id_mvt_row.')',
                            'class' => 'btn btn-info btn-sm',
                          ])
                      !!}
                  </td>
                @else
                  <td>
                    --
                  </td>
                @endif
             </tr>

             <?php $i++; ?>
           @endforeach
        </tbody>
      </table>
  </div>
</div>
