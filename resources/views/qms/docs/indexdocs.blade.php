@extends('templates.home.modules')

@section('title', 'Papeletas')

@section('content')
<div id="the_index">
    <div class="row">
        {!! Form::open(['route' => 'qms.qdocs.docs',
            'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
            <div class="form-group">
                <div class="input-group">
                    @include('templates.list.search')
                    <span class="input-group-btn">
                        {!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']); !!}
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
        <div class="col-md-12">
            <table class="table table-striped table-condensed" id="qdocs_table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Fecha Papeleta</th>
                        <th>Nombre</th>
                        <th>Producto</th>
                        <th>Lote</th>
                        <th>Caducidad</th>
                        <th>Firmas</th>
                        <th>Firmas I</th>
                        <th>Firmas II</th>
                        <th>Ver</th>
                        <th>Certificado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="papeleta in vlPapeletas">
                        <td>@{{ papeleta.id_document }}</td>
                        <td>@{{ papeleta.dt_document }}</td>
                        <td>@{{ papeleta.title }}</td>
                        <td>@{{ papeleta.item_name }}</td>
                        <td>@{{ papeleta.lot }}</td>
                        <td>@{{ papeleta.dt_expiry }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" aria-label="Firmas" 
                                    data-toggle="modal" data-target="#sigModal">
                                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                        </td>
                        <td>
                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default">
                                <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> Ver
                            </button>
                        </td>
                        <td>
                            <a  v-if="(papeleta.body_id.length > 0)"
                                 type="button" class="btn btn-warning" :href="'../certificates/print/' + papeleta.id_document">
                                <span class="glyphicon glyphicon-file" aria-hidden="true"></span> Imprimir
                            </a>
                            <label v-else>-</label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @include('qms.docs.signatures')
</div>
@endsection
    
@section('js')
    {{-- <script src="{{ asset('js/qms/qdocs/objs/SGui.js') }}"></script> --}}
    <script src="{{ asset('moment/moment.js') }}"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
    <script>
        function GlobalData () {
            this.scqms = <?php echo json_encode(\Config::get('scqms')) ?>;
            this.lPapeletas = <?php echo json_encode($lQltyDocs) ?>;
        }

        var oData = new GlobalData();
    </script>

    <script src="{{ asset('js/qms/qdocs/SIndex.js') }}"></script>
    <script>
        // new Vue({ el: '#vue-table' }) 

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

        $('#qdocs_table').DataTable({
            "paging": true,
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
            }
        });
    </script>
@endsection