<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Saporis Comercial SA de CV</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <style type='text/css'>
          body {font-family:calibri;font-size:11pt;padding:0;margin:0;}
          a {color: #0000FF}
          a:hover {text-decoration:underline}
          table {border-collapse:collapse;}
          .t {font-family:Calibri;text-align:left;vertical-align:bottom}
          .r1 {font-weight:bold;text-align:center}
          .tc {text-align:center}
          .trr {text-align:right;}
          .tb {font-weight:bold;}
          .r21 {text-align:center;vertical-align:middle}
          .c21_1 {text-align:left;vertical-align:bottom}
          .c21_2 {border-top:1px solid #000000;border-left:1px solid #000000}
          .c21_12 {border-top:1px solid #000000;border-left:1px solid #000000;border-right:1px solid #000000}
          .c22_4 {text-align:left;border-top:1px solid #000000;border-left:1px solid #000000}
          .c23_2 {border-top:1px solid #000000}
          .c23_4 {vertical-align:middle;border-top:1px solid #000000}
          .c25_4 {vertical-align:middle}
          .c31_9 {font-weight:bold;text-decoration:underline}
          footer { position: fixed; bottom: -20px; left: 0px; right: 0px; height: 50px; }
      </style>
    {{-- {!! Html::style('bootstrap/css/bootstrap.min.css') !!} --}}
  </head>
  <body>
      <table width="100%">
        <tr style='height:18px'>
            <td class='r1'>
              <img src="{{ asset('images/companies/'.(session()->has('company') ? session('company')->database_name : 'siie').'.jpg') }}" alt="">
            </td>
            <td class='r1'>
                <h3>{{ session('partner')->name }}</h2>
            </td>
            <td class='r1'>
              {{ $oMovement->mvtType->name }}
              <br>
              {{ $oMovement->mvtType->code.'-'.$oMovement->folio }}
        </tr>
      </table>
      <table width="100%">
        <tr style='height:18px'>
            <td class='tc'>
                <?php $oAddress = session('branch')->getMainAddress(); ?>
                {{ strtoupper ($oAddress->street.' #'.$oAddress->num_ext.'. '.$oAddress->neighborhood.
                    ', '.$oAddress->locality.', '.$oAddress->state_name.'; CP '.$oAddress->zip_code) }}
            </td>
        </tr>
      </table>
      <table width="100%">
        <tr style='height:18px'>
            <td>
                Docto surtido:
            </td>
            <td>
                <?php

                  $oDocument = $oMovement->getDocumentSupplied();
                  $sText = '-----';
                  if ($oDocument->id_document > 1) {
                    $sText = $oDocument->docClass->code.'/'.
                        ($oDocument->service_num == '' ?
                        $oDocument->num :
                        $oDocument->service_num.'-'.$oDocument->num);
                  }

                ?>

                {{ $sText }}
            </td>
            <td>
                Tipo ajuste:
            </td>
            <td>
                {{ $oMovement->mvt_adj_type_id > 1 ? $oMovement->adjType->name : '--------' }}
            </td>
            <td class='r1'>
                {{ $oMovement->dt_date }}
                <br>
                {{ $oMovement->warehouse->name }}
            </td>
        </tr>
      </table>
      <table width="100%">
        <tr style='height:18px'>
            <td>
                Orden prod.:
            </td>
            <td>

            </td>
            <td>
                Referencia:
            </td>
            <td>

            </td>
            <td class='r1'>
                {{ $oMovement->branch->name }}
            </td>
        </tr>
      </table>
      <hr>
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
      		    <thead>
      		        <tr class="titlerow">
    								<th>{{ trans('userinterface.labels.CODE') }}</th>
                    <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                    <th>{{ trans('wms.labels.UN') }}</th>
                    <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                    <th>{{ trans('wms.labels.PALLET') }}</th>
                    <th>{{ trans('userinterface.labels.PRICE') }}</th>
                    <th>{{ trans('userinterface.labels.AMOUNT') }}</th>
      		        </tr>
      		    </thead>
      		    <tbody>
                <?php $dTotal=0 ?>
      					@foreach ($oMovement->rows as $row)
      						<tr>
						        <td>{{ $row->item->code }}</td>
                    <td class="trr">{{ session('utils')->formatNumber(($row->quantity), \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td>{{ $row->unit->code }}</td>
						        <td>{{ $row->item->name }}</td>
						        <td>{{ $row->pallet_id == '1' ? 'SIN TARIMA' :  $row->pallet_id }}</td>
						        <td class="trr">{{ session('utils')->formatNumber(($row->amount_unit), \Config::get('scsiie.FRMT.AMT')) }}</td>
						        <td class="trr">{{ session('utils')->formatNumber(($row->quantity * $row->amount_unit), \Config::get('scsiie.FRMT.AMT')) }}</td>
                  </tr>
                  @foreach ($row->lotRows as $lotRow)
                    <tr>
                      <td>{{ '' }}</td>
                      <td class="trr">{{ session('utils')->formatNumber(($lotRow->quantity), \Config::get('scsiie.FRMT.QTY')) }}</td>
                      <td>{{ $row->unit->code }}</td>
                      <td>{{ $lotRow->lot->lot }}</td>
                      <td>{{ $lotRow->lot->dt_expiry }}</td>
                    </tr>
                  @endforeach
                  {!! $dTotal+= ($row->quantity * $row->amount_unit) !!}
                @endforeach
              </tbody>
              <tfoot>
                   <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td class="tb trr">TOTAL:</td>
                     <td></td>
                     <td class="trr" colspan="2">
                          {{ session('utils')->formatNumber(($dTotal), \Config::get('scsiie.FRMT.AMT')).' '.session('currency')->code }}
                     </td>
                   </tr>
              </tfoot>
            </table>
            <hr>
            <table width="100%">
              <tr style='height:18px'>
                  <td class="c21_1">
                      <p class="tc">_____________________</p>
                      <p class="tc">Entrega</p>
                      <p class="tc">(Nombre, firma y  fecha)</p>
                  </td>
                  <td></td>
                  <td class="trr">
                    <p class="tc">_____________________</p>
                    <p class="tc">Recibe</p>
                    <p class="tc">(Nombre, firma y  fecha)</p>
                  </td>
              </tr>
            </table>
        </div>
      </div>
      <footer>
        <table class="table table-condensed" cellspacing="0" width="100%">
          <tr>
              <td class="c21_1">
                  SIIE Web 1.0 Software Aplicado SA de CV
              </td>
              <td class='trr'>
                  {{ 'Impresión: '.Carbon\Carbon::now()->toDateTimeString() }}
              </td>
          </tr>
          <tr>
              <td class="c21_1">
                  www.swaplicado.com.mx
              </td>
              <td class='trr'>
                  {{ \Auth::user()->username }}
              </td>
          </tr>
        </table>
      </footer>
  </body>
</html>
