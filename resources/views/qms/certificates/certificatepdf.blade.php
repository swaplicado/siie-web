<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Saporis Comercial SA de CV</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/certificatestyle.css') }}">
  </head>
  <body>
      <table width="100%">
        <tr style='height:18px'>
            <td>
              <img src="{{ asset('images/companies/'.(session()->has('company') ? session('company')->database_name : 'siie').'.jpg') }}"
                    alt="saporis"
                    width="100" height="100">
            </td>
            <td>
                <h3><span style="display:inline-block; width: 10px;"></span>
                    {{ strtoupper(trans('qms.labels.QLTY_CERTIFICATE')) }}</h3>
            </td>
        </tr>
      </table>
      <table style="width:100%">
        <tr>
          <td class="hss" width="15%">
            <b>PRODUCTO:</b>
          </td>
          <td class="hss" width="55%">
            {{ $oLot->item->name }}
          </td>
          <td class="hss trr" width="20%">
            <b>FECHA DE EMISIÓN:</b>
          </td>
          <td class="hss trr" width="10%">
            {{ (new \Carbon\Carbon($sDate))->format('d/m/Y') }}
          </td>
        </tr>
        <tr>
            <td class="hss" width="15%">
              <b>LOTE:</b>
            </td>
            <td class="hss" width="55%">
              {{ $oLot->lot }}
            </td>
          </tr>
          <tr>
            <td class="hss" width="15%">
             <b>CADUCIDAD:</b>
            </td>
            <td class="hss" width="55%">
                {{ (new \Carbon\Carbon($oLot->dt_expiry))->format('d/m/Y') }}
            </td>
          </tr>
      </table>
      <hr>
      <div>
        {{-- Resultados fisicoquímicos --}}
        @if (sizeof($lFQResults) > 0)
          <p class="little9"><b>1. ANÁLISIS FISICOQUÍMICOS</b></p>
          <table style="width:100%" class="tc">
              <thead>
                  <tr class="little9" style="background-color:darkgrey">
                      <th class="tc" width="20%">PARÁMETRO</th>
                      <th class="tc" width="40%">MÉTODOS ANALÍTICOS</th>
                      <th class="tc" width="20%">ESPECIFICACIÓN</th>
                      <th class="tc" width="20%">RESULTADOS</th>
                  </tr>
              </thead>
              @foreach ($lFQResults as $oFQRes)
                  <tr class="little8">
                    <td>{{ $oFQRes->_analysis }}</td>
                    <td>{{ $oFQRes->standard }}</td>
                    {{-- <td>{{ $oFQRes->min_value.'-'.$oFQRes->max_value }}</td> --}}
                    <td>{{ $oFQRes->_specification == '' ? $oFQRes->_ana_specification : $oFQRes->_specification }}</td>
                    <td class="tc"><b>{{ ($oFQRes->mongoResult != null ? $oFQRes->mongoResult['result'] : '').' '.$oFQRes->result_unit }}</b></td>
                  </tr>
              @endforeach
          </table>
        @endif
        <br>
        {{-- Resultados organolépticos --}}
        @if (sizeof($lOLResults) > 0)
          <p class="little9"><b>2. ANÁLISIS ORGANOLÉPTICOS</b></p>
          <table style="width:100%" class="tc">
              <thead>
                  <tr class="little9" style="background-color:darkgrey">
                      <th class="tc" width="30%">PARÁMETRO</th>
                      <th class="tc" width="50%">DESCRIPCIÓN EN ESPECIFICACIÓN</th>
                      <th class="tc" width="20%">RESULTADO</th>
                  </tr>
              </thead>
              @foreach ($lOLResults as $oOLRes)
                  <tr class="little8">
                    <td>{{ $oOLRes->_analysis }}</td>
                    <td>{{ $oOLRes->_specification }}</td>
                    <td class="tc"><b>{{ $oOLRes->_result }}</b></td>
                  </tr>
              @endforeach
          </table>
          <br>
        @endif
        {{-- Resultados microbiológicos --}}
        @if (sizeof($lMBResults) > 0)
          <p class="little9"><b>3. ANÁLISIS MICROBIOLÓGICOS</b></p>
          <table style="width:100%" class="tc">
              <thead>
                  <tr class="little9" style="background-color:darkgrey">
                      <th class="tc" width="20%">PARÁMETRO</th>
                      <th class="tc" width="40%">MÉTODOS ANALÍTICOS</th>
                      <th class="tc" width="20%">ESPECIFICACIÓN</th>
                      <th class="tc" width="20%">RESULTADOS</th>
                  </tr>
              </thead>
              @foreach ($lMBResults as $oMBRes)
                  <tr class="little8">
                    <td>{{ $oMBRes->_analysis }}</td>
                    <td>{{ $oMBRes->standard }}</td>
                    <td>{{ $oMBRes->_specification == '' ? $oMBRes->_ana_specification : $oMBRes->_specification }}</td>
                    <td class="tc"><b>{{ ($oMBRes->mongoResult != null ? $oMBRes->mongoResult['result'] : '').' '.$oMBRes->result_unit }}</b></td>
                  </tr>
              @endforeach
          </table>
          <p class="little">De bacterias aerobias en placa en agar para cuenta estándar, incubadas 48 horas a 35 +/- 1°C</p>
          <p class="little">De bacterias coliformes en placa en agar rojo violeta bilis, incubados a 35°C por 24 +/- 2 horas</p>
          <p class="little">De levaduras en agar papa-dextrosa acidificado, incubadas a 25 +/- 1°C durante 5 días</p>
          <p class="little">De hongos en agar papa-dextrosa acidificado, incubadas a 25 +/- 1°C durante 5 días</p>
        @endif
      </div>
      <div class="row">
          <script type="text/php">
            if (isset($pdf)) {
              $pdf->Image(asset('images/qms/calidad.png'), 500, 570, 100, 101, 'PNG');
            }
          </script>
        <div class="col-md-12">
            <hr>
            <br>
            <br>
            <br>
            <p class="tc little8"><span style="display:inline-block; width: 720px;">Atentamente,</p>
            <br>
            <br>
            <br>
            <table width="100%">
              <tr>
                <td class="tc">_________________________________________</td>
                <td class="tc">_________________________________________</td>
              </tr>
            </table>
            <table width="100%">
              <tr>
                  <td class="little8 tc" width="50%">
                    <p>
                      <?php $first = true; ?>
                      @foreach (explode("##", $sSupervisor) as $sSup)
                          {{ $sSup }}
                          @if($first)
                          <br><?php $first = false; ?>
                          @endif
                      @endforeach
                    </p>
                    <p style="line-height:0px" class="little8"><b>SUPERVISOR DE MICROBIOLOGÍA</b></p>
                  </td>
                  <td class="little8 tc" width="50%">
                    <p class="little8">{{ $sManager }}</p>
                    <p style="line-height:0px" class="little8"><b>GERENTE DE CALIDAD</b></p>
                  </td>
              </tr>
            </table>
        </div>
      </div>
      <footer>
          <br>
          <table width="100%">
            <tr>
                <td class="little tc">
                  Oriente Cuatro #602 Col. Ciudad Industrial CP 58200 Morelia, México. Tel. 4433232300 Ext. 109. www.saporis.mx
                </td>
            </tr>
            <tr>
                <td class="little tc">
                  {{ 'SIIE Web 1.0 www.swaplicado.com.mx     usr: '.\Auth::user()->username.'    Impresión: '.Carbon\Carbon::now()->toDateTimeString() }}
                </td>
                <script type="text/php">
                  if (isset($pdf)) {
                    $font = $fontMetrics->getFont("Calibri", "bold");
                    $pdf->page_text(530, 765, "Página {PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
                  }
                </script>
            </tr>
          </table>
        </footer>
  </body>
  
</html>
