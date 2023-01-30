@extends('templates.home.modules')

@section('title',  trans('qms.REPORT_PH'))

@section('content')
    <div id="reportPhApp">
        <div class="row">
            {!! Form::open(['route' => 'qms.reports.ph',
                'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
                <div class="form-group">
                    <div class="input-group">
                        @include('templates.list.search')
                        <span class="input-group-btn">
                            {!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']) !!}
                            <input type="hidden" id="max_ph" name="max_ph" value="{{ $max_ph }}">
                            <input type="hidden" id="item_id" name="item_id" value="{{ $item_id }}">
                        </span>
                        <span class="input-group-btn">
                            <button id="searchbtn" type="submit" class="form-control">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                    </div>
                </div>
            {!! Form::close() !!}
        </div>

        <div class="row">
            <table id="ph_report" class="table table-striped" >
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Caducidad</th>
                        <th>PH</th>
                        <th>PH Máximo</th>
                        <th>Captura</th>
                        <th>Fecha</th>
                        <th>Modifica</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lPhLots as $phLot)
                        <tr>
                            <td>{{ $phLot->lot }}</td>
                            <td>{{ $phLot->dt_expiry }}</td>
                            <td style="text-align: right">{{ session('utils')->formatNumber($phLot->result, \Config::get('scsiie.FRMT.QTY')) }}</td>
                            <td style="text-align: right">{{ session('utils')->formatNumber($phLot->max_ph, \Config::get('scsiie.FRMT.QTY')) }}</td>
                            <td>{{ $phLot->created_by }}</td>
                            <td>{{ $phLot->created_at }}</td>
                            <td>{{ $phLot->updated_by }}</td>
                            <td>{{ $phLot->updated_at }}</td>
                        </tr>
                    @endforeach
                    {{-- <tr v-for="lotPh in vPhLots">
                        <td>@{{ lotPh.lot }}</td>
                        <td>@{{ lotPh.lot_date }}</td>
                        <td>@{{ lotPh.results[0].result }}</td>
                        <td>@{{ lotPh.results[0].result }}</td>
                        <td>@{{ lotPh.created_at }}</td>
                        <td>@{{ lotPh.lot }}</td>
                        <td>@{{ lotPh.updated_at }}</td>
                        <td>@{{ lotPh.lot }}</td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('js')

<script src="{{ asset('moment/moment.js') }}"></script>
<script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>

<script>

    function GlobalData () {
        this.scqms = <?php echo json_encode(\Config::get('scqms')) ?>;
        this.lPhLots = <?php echo json_encode($lPhLots) ?>;
    }

    var globalData = new GlobalData();
</script>

<script src="{{ asset('js/qms/reports/SPh.js') }}"></script>

<script>
$(function() {
    $('input[id="filterDate"]').daterangepicker({
    locale: {
            format: 'DD/MM/YYYY'
        }
    });
});

$('#filterDate').on('apply.daterangepicker', function(ev, picker) {
    console.log(picker.startDate.format('YYYY-MM-DD'));
    console.log(picker.endDate.format('YYYY-MM-DD'));
});

$('#ph_report').DataTable({
    "paging": true,
    "dom": 'Bfrtip',
      "lengthMenu": [
        [ 10, 25, 50, 100, -1 ],
        [ 'Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100', 'Mostrar todo' ]
      ],
      "buttons": [
            'pageLength', 'copy', 'csv', 'excel', 'print'
        ],
    "language": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "scrollX": true,
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":     "Último",
            "sNext":     "Siguiente",
            "sPrevious": "Anterior"
        },
        "oAria": {
            "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
    },
    "colReorder": true
});
</script>
    
@endsection