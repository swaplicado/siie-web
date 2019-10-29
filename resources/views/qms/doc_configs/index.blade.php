@extends('templates.home.modules')

@section('title',  trans('qms.CFG_DOCS'))

@section('content')
    <div id="primary_app">
        
        <div class="row" v-if="iMode == 1">
            <div class="col-md-3" style="margin-top: 25px;" v-for="doc_type in lDocumentTypes">
                <button v-bind:class="doc_type.b_class" v-on:click="toConfiguration(doc_type.id_link_type, doc_type.id_link)">
                    @{{ doc_type.name }}
                </button>
            </div>
        </div>
        <div class="row" v-if="iMode == 2">
            <div class="row">
                <div class="col-md-1" style="text-align: center;">
                    <label for="id_section">Secciones:</label>
                </div>
                <div class="col-md-5">
                    <select v-model="oSelectedSection" class="form-control" id="id_section" :required="true">
                        <option v-for="nSection in lAllSections"
                                v-bind:value="nSection"
                        >
                                @{{ nSection.title }}
                        </option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-info" v-on:click="addSection()">Agregar sección</button>
                </div>
                <div class="col-md-1 col-md-offset-1">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#sectionModal">Nueva sección</button>
                </div>
            </div>
            <hr>
            <div class="panel panel-default" v-for="section in lSections">
                <div class="panel-heading">@{{ section.title }}</div>
                <div class="panel-body" style="background-color: lightblue">
                    <div class="row">
                        <div class="col-md-4" style="text-align: center;">
                            @{{ section.dt_section }}
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            @{{ section.comments }}
                        </div>
                        <div class="col-md-4" style="text-align: center;">
                            <button type="button" class="btn btn-info" v-on:click="addElement(section.id_section)" 
                                    data-toggle="modal" data-target="#elementModal">Agregar Elemento</button>
                        </div>
                    </div>
                </div>
                <ul class="list-group">
                    <li class="list-group-item" v-for="config in lConfigurations" v-if="config.id_section == section.id_section">
                        <div class="row">
                            <div class="col-md-3" style="text-align: center;">
                                @{{ config.element }}
                            </div>
                            <div class="col-md-2" style="text-align: center;">
                                @{{ getElementType(config.element_type_id).element_type }}
                            </div>
                            <div class="col-md-4" style="text-align: center;">
                                @{{ config.analysis_id > 0 ? getAnalysisById(config.analysis_id).name : 'NA' }}
                            </div>
                            <div class="col-md-2" style="text-align: center;">
                                <button type="button" class="btn btn-primary" v-on:click="setConfiguration(config)">
                                    @{{ config.n_values }}
                                </button>
                                <button type="button" class="btn btn-danger" aria-label="Borrar" v-on:click="removeConfiguration(config.id_configuration)">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        @include('qms.doc_configs.sectionmodal')
        @include('qms.doc_configs.elementmodal')
        @include('qms.doc_configs.fieldsmodal')
    </div>
@endsection

@section('js')
    <script src="{{ asset('moment/moment.js') }}"></script>
    <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('js/qms/qdocs/objs/SGui.js') }}"></script>

    <script>
        function GlobalData () {
            this.scwms = <?php echo json_encode(\Config::get('scwms')) ?>;
            this.lElementTypes = <?php echo json_encode($lElementTypes) ?>;
            this.lAllAnalysis = <?php echo json_encode($lAllAnalysis) ?>;
            this.cfgZone = <?php echo json_encode($cfgZone) ?>;
        }

        var oGD = new GlobalData();
        var oGui = new SGui();
    </script>

    <script src="{{ asset('js/qms/qdocs/objs/SClasses.js') }}"></script>
    <script src="{{ asset('js/qms/qdocs/SQualityDocuments.js') }}"></script>
    
    <script>
        // new Vue({ el: '#vue-table' })

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