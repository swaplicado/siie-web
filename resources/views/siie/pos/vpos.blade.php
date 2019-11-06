@extends('templates.home.modules')

@section('title', 'Órdenes de Producción')

@section('content')
    <div class="row">
        {!! Form::open(['route' => ['siie.pos.index', $iFrom],
            'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
            <div class="form-group">
                <div class="input-group">
                    @include('templates.list.search')
                    <span class="input-group-btn">
                        {!! Form::select('po_status', $lOrderStatus, $iOrderStatus,
                                                                    ['class'=>'form-control']) !!}
                    </span>
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
            <table class="table table-striped table-hover table-condensed" id="poss_table">
                <thead>
                    <tr>
                        <th>Folio Padre</th>
                        <th>Fecha Padre</th>
                        <th>ítem Padre</th>
                        <th>Folio Hijo</th>
                        <th>Fecha Hijo</th>
                        <th>Ítem Hijo</th>
                        <th>Lote</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lProductionOrders as $oPO)
                        <tr>
                            @if ($oPO->father_order_id > 1)
                                <td>{{ session('utils')->formatFolio($oPO->folio_padre) }}</td>
                                <td>{{ $oPO->datef_ }}</td>
                                <td>{{ $oPO->item_code_f_.'-'.$oPO->itemf_.' - '.$oPO->unitf_ }}</td>
                                <td>{{ session('utils')->formatFolio($oPO->folio_hijo) }}</td>
                                <td>{{ $oPO->dateh_ }}</td>
                                <td>{{ $oPO->item_code_.'-'.$oPO->item_.' - '.$oPO->unit_ }}</td>
                                <td>{{ $oPO->lot }}</td>
                                <td>
                                    @if (App\SUtils\SValidation::hasPermission(\Config::get('scperm.PERMISSION.QMS_ANALYSIS_FQ')))
                                        <a class="btn btn-warning btn-xs" title="Papeleta FQ"
                                            href="{{ route('qms.qdocs.index', [$oPO->father_order_id, $oPO->id_order, $oPO->lot_id, \Config::get('scqms.CFG_ZONE.FQ')]) }}">
                                            <span class="glyphicon glyphicon-file" aria-hidden = "true"></span>
                                        </a>
                                    @endif
                                    @if (App\SUtils\SValidation::hasPermission(\Config::get('scperm.PERMISSION.QMS_ANALYSIS_MB')))
                                        <a class="btn btn-warning btn-xs" title="Papeleta MB"
                                            href="{{ route('qms.qdocs.index', [$oPO->father_order_id, $oPO->id_order, $oPO->lot_id, \Config::get('scqms.CFG_ZONE.MB')]) }}">
                                            <span class="glyphicon glyphicon-check" aria-hidden = "true"></span>
                                        </a>
                                    @endif
                                    @if ($oPO->lot_id > 1)
                                        @if (App\SUtils\SValidation::hasPermission(\Config::get('scperm.PERMISSION.MMS_PROD_ORDERS_ASSIGNAMENTS')))
                                            <a class="btn btn-primary btn-xs" title="Entrada por producción"
                                                href="{{ route('wms.movs.create', [\Config::get('scwms.MVT_IN_DLVRY_FP'), trans('mms.DELIVERY_FP'), $oPO->id_order]) }}">
                                                <span class="glyphicon glyphicon-arrow-right" aria-hidden = "true"></span>
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            @else
                                <td>{{ session('utils')->formatFolio($oPO->folio_hijo) }}</td>
                                <td>{{ $oPO->dateh_ }}</td>
                                <td>{{ $oPO->item_code_.'-'.$oPO->item_.' - '.$oPO->unit_ }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    @if (App\SUtils\SValidation::hasPermission(\Config::get('scperm.PERMISSION.QMS_ANALYSIS_FQ')))
                                        <a class="btn btn-warning btn-xs" title="Papeleta FQ"
                                            href="{{ route('qms.qdocs.index', [$oPO->id_order, 0, 0, \Config::get('scqms.CFG_ZONE.FQ')]) }}">
                                            <span class="glyphicon glyphicon-file" aria-hidden = "true"></span>
                                        </a>
                                    @endif
                                    @if (App\SUtils\SValidation::hasPermission(\Config::get('scperm.PERMISSION.QMS_ANALYSIS_MB')))
                                        <a class="btn btn-warning btn-xs" title="Papeleta MB"
                                            href="{{ route('qms.qdocs.index', [$oPO->id_order, 0, 0, \Config::get('scqms.CFG_ZONE.MB')]) }}">
                                            <span class="glyphicon glyphicon-check" aria-hidden = "true"></span>
                                        </a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    @include('siie.pos.vscripts')
    <script src="{{ asset('moment/moment.js') }}"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
    
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

        $('#poss_table').DataTable({
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