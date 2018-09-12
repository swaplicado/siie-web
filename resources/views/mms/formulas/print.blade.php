<!DOCTYPE html>
<html lang="es"><head>
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
  </head><body>
      <table width="100%">
        <tr style='height:18px'>
            <td class='r1'>
              <img src="{{ asset('images/companies/'.(session()->has('company') ?
                            session('company')->database_name :
                            'siie').'.jpg') }}" alt="">
            </td>
            <td class='t'>
                <h3>{{ session('partner')->name }}</h2>
            </td>
        </tr>
      </table>
      <table width="100%">
        <tr style='height:18px'>
            <td class='tc tb'>
              LISTA DE INGREDIENTES/MATERIALES DE LA FÓRMULA
            </td>
        </tr>
      </table>
      <hr>
      <table width="100%">
        <tr style='height:18px'>
            <td>
                Fecha:
            </td>
            <td class="tb">
                {{ $oFormula->dt_date }}
            </td>
            <td>
                Identificador:
            </td>
            <td class="tb">
                {{ $oFormula->identifier }}
            </td>
        </tr>
      </table>
      <table width="100%">
        <tr style='height:18px'>
            <td>
                Producto:
            </td>
            <td class="tb">
                {{ $oFormula->item->name.' - '.$oFormula->unit->code }}
            </td>
            <td>
                Versión:
            </td>
            <td class="tb">
                {{ $oFormula->version }}
            </td>
        </tr>
      </table>
      <hr>
      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped no-wrap table-condensed" cellspacing="0" width="100%">
              <thead>
                  <tr class="titlerow">
                    <th>{{ '#' }}</th>
                    <th>{{ trans('userinterface.labels.CODE') }}</th>
                    <th>{{ trans('wms.labels.MAT_PROD') }}</th>
                    <th>{{ trans('userinterface.labels.QUANTITY') }}</th>
                    <th>{{ trans('wms.labels.UN') }}</th>
                  </tr>
              </thead>
              <tbody>
                <?php $index = 1; ?>
                @foreach ($oFormula->rows as $row)
                  <tr>
                    <td>{{ $index }}</td>
                    <td>{{ $row->item->code }}</td>
                    <td>{{ $row->item->name }}</td>
                    <td class="trr">{{ session('utils')->formatNumber(($row->quantity), \Config::get('scsiie.FRMT.QTY')) }}</td>
                    <td>{{ $row->unit->code }}</td>
                  </tr>
                  <?php $index++ ?>
                @endforeach
              </tbody>
              <tfoot>
                   <tr>
                     <td></td>
                     <td></td>
                     <td</td>
                     <td class="trr" colspan="2">
                     </td>
                     <td></td>
                   </tr>
              </tfoot>
            </table>
            <hr>
        </div>
      </div>
      <footer>
        <table class="table table-condensed" cellspacing="0" width="100%">
          <tr>
              <td class="c21_1">
                  SIIE Web 1.0 Software Aplicado SA de CV
              </td>
              <td>
                <script type="text/php">
                  if (isset($pdf)) {
                    $font = $fontMetrics->getFont("Calibri", "bold");
                    $pdf->page_text(530, 765, "Página {PAGE_NUM}/{PAGE_COUNT}", $font, 10, array(0, 0, 0));
                  }
                </script>
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
  </body></html>
