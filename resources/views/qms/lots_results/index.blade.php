@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('qms.SEGREGATED_LOTS'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('qms.SEGREGATED_LOTS'))

@section('content')
    <table id="lots_results_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">Lote</th>
                <th data-priority="1" style="text-align: center;">Item</th>
                <th data-priority="1" style="text-align: center;">Cantidad</th>
                <th data-priority="1" style="text-align: center;">Un</th>
                <th data-priority="1" style="text-align: center;">Estatus</th>
                <th data-priority="1" style="text-align: center;">-</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lSegregatedLots as $sLot)
                <tr>
                    <td>{{ $sLot->lot }}</td>
                    <td>{{ $sLot->_item }}</td>
                    <td>{{ $sLot->_seg }}</td>
                    <td>{{ $sLot->_unit }}</td>
                    <td>{{ $sLot->_evtname }}</td>
                    <td>
                        <a
                            onClick="getModal({{ $sLot->id_lot }})"
                            title="Capture resultados"
                                class="btn btn-info btn-sm">
                                <span class="glyphicon glyphicon-list-alt" aria-hidden = "true"/>
                        </a>
                    </td>
                </tr>
            @endforeach
    </table>
    @include('qms.lots_results.capture')
@endsection

@section('js')
  <script src="{{ asset('js/qms/lots_results/tables.js')}}"></script>
  <script src="{{ asset('js/qms/lots_results/SGuiResults.js')}}"></script>
  <script src="{{ asset('js/qms/lots_results/SResults.js')}}"></script>
  <script src="{{ asset('js/qms/lots_results/SCaptureRow.js')}}"></script>

  <script type="text/javascript">
    function GlobalData () {
        this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;
        this.scqms = <?php echo json_encode(\Config::get('scqms')) ?>;

        this.DEC_QTY = <?php echo json_encode(session('decimals_qty')) ?>;
        this.DEC_AMT = <?php echo json_encode(session('decimals_amt')) ?>;
    }

    const globalData = new GlobalData();

   </script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
